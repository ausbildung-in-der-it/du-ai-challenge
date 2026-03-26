<?php

use App\Http\Controllers\JourneyController;
use App\Http\Controllers\QuizCommentaryController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::get('/journey/{journey:slug}', [JourneyController::class, 'show'])->name('journey.show');

Route::post('/api/quiz-commentary', QuizCommentaryController::class)->name('quiz.commentary');
