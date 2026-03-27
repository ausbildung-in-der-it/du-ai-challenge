<?php

namespace App\Http\Controllers;

use App\Models\GitCommit;
use Illuminate\Http\JsonResponse;

class GitCommitController extends Controller
{
    public function index(): JsonResponse
    {
        $commits = GitCommit::orderBy('committed_at')->get();

        $first = $commits->first();
        $last = $commits->last();

        $durationMinutes = $first && $last
            ? $first->committed_at->diffInMinutes($last->committed_at)
            : 0;

        return response()->json([
            'commits' => $commits->map(fn (GitCommit $c) => [
                'hash' => $c->short_hash,
                'message' => $c->message,
                'time' => $c->committed_at->format('H:i'),
                'minutes_in' => $c->minutes_in,
            ]),
            'total' => $commits->count(),
            'duration_minutes' => $durationMinutes,
        ]);
    }
}
