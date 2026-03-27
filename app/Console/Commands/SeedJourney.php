<?php

namespace App\Console\Commands;

use Database\Seeders\JourneySeeder;
use Illuminate\Console\Command;

class SeedJourney extends Command
{
    protected $signature = 'journey:seed';

    protected $description = 'Re-seed the learning journey (idempotent — deletes existing and recreates)';

    public function handle(): int
    {
        $this->call('db:seed', ['--class' => JourneySeeder::class]);

        $this->info('Journey seeded successfully.');

        return self::SUCCESS;
    }
}
