<?php

use App\Http\Controllers\CommentaryController;
use App\Http\Controllers\JourneyController;
use App\Http\Controllers\JourneySessionController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::get('/journey/{journey:slug}', [JourneyController::class, 'show'])->name('journey.show');

// Session API
Route::post('/api/sessions', [JourneySessionController::class, 'store'])->name('sessions.store');
Route::get('/api/sessions/{journeySession}', [JourneySessionController::class, 'show'])->name('sessions.show');
Route::patch('/api/sessions/{journeySession}', [JourneySessionController::class, 'update'])->name('sessions.update');
Route::post('/api/sessions/{journeySession}/persona', [JourneySessionController::class, 'setPersona'])->name('sessions.persona');

// Commentary API
Route::post('/api/sessions/{journeySession}/commentaries', [CommentaryController::class, 'store'])->name('commentaries.store');
Route::get('/api/sessions/{journeySession}/commentaries/{commentary}', [CommentaryController::class, 'show'])->name('commentaries.show');
