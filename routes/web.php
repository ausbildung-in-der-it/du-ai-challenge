<?php

use App\Http\Controllers\AiReadyController;
use App\Http\Controllers\CommentaryController;
use App\Http\Controllers\JourneyController;
use App\Http\Controllers\JourneySessionController;
use App\Models\JourneySession;
use App\Models\LearningJourney;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

// AI Ready Funnel
Route::get('/ai-ready', [AiReadyController::class, 'index'])->name('ai-ready');
Route::post('/ai-ready/checkout', [AiReadyController::class, 'checkout'])->name('ai-ready.checkout');
Route::get('/ai-ready/danke', [AiReadyController::class, 'success'])->name('ai-ready.success');

Route::get('/journey/{journey:slug}', [JourneyController::class, 'show'])->name('journey.show');

// Session API
Route::post('/api/sessions', [JourneySessionController::class, 'store'])->name('sessions.store');
Route::get('/api/sessions/{journeySession}', [JourneySessionController::class, 'show'])->name('sessions.show');
Route::patch('/api/sessions/{journeySession}', [JourneySessionController::class, 'update'])->name('sessions.update');
Route::post('/api/sessions/{journeySession}/persona', [JourneySessionController::class, 'setPersona'])->name('sessions.persona');

// Commentary API (SSE stream + DB persistence)
Route::post('/api/sessions/{journeySession}/commentaries', [CommentaryController::class, 'store'])->name('commentaries.store');
Route::get('/api/sessions/{journeySession}/commentaries/{commentary}', [CommentaryController::class, 'show'])->name('commentaries.show');
Route::get('/api/sessions/{journeySession}/cards/{quizCard}/commentary', [CommentaryController::class, 'latest'])->name('commentaries.latest');

// Dev: reset all sessions for a journey
Route::get('/journey/{journey:slug}/reset', function (LearningJourney $journey) {
    $count = JourneySession::where('learning_journey_id', $journey->id)->delete();

    return redirect("/journey/{$journey->slug}")->with('message', "{$count} Sessions gelöscht.");
})->name('journey.reset');
