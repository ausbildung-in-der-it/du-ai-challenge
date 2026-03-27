<?php

namespace App\Console\Commands;

use App\Models\GitCommit;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class SyncGitCommits extends Command
{
    protected $signature = 'sync:git-commits';

    protected $description = 'Sync git commit history into the database';

    public function handle(): int
    {
        $process = new Process(
            ['git', 'log', '--reverse', '--format=%H|%s|%aI'],
            base_path(),
        );
        $process->run();

        if (! $process->isSuccessful()) {
            $this->error('Failed to read git log: '.$process->getErrorOutput());

            return self::FAILURE;
        }

        $lines = array_filter(explode("\n", trim($process->getOutput())));

        if (empty($lines)) {
            $this->warn('No commits found.');

            return self::SUCCESS;
        }

        $firstCommitTime = null;
        $synced = 0;

        foreach ($lines as $line) {
            [$hash, $message, $date] = explode('|', $line, 3);

            $committedAt = Carbon::parse($date);

            if ($firstCommitTime === null) {
                $firstCommitTime = $committedAt;
            }

            $minutesIn = (int) $firstCommitTime->diffInMinutes($committedAt);

            GitCommit::updateOrCreate(
                ['hash' => $hash],
                [
                    'short_hash' => substr($hash, 0, 7),
                    'message' => $message,
                    'committed_at' => $committedAt,
                    'minutes_in' => $minutesIn,
                ],
            );

            $synced++;
        }

        $this->info("Synced {$synced} commits.");

        return self::SUCCESS;
    }
}
