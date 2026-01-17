<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventCommentController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\EventParticipantController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Groups
    Route::get('/groups', [GroupController::class, 'index']);
    Route::post('/groups', [GroupController::class, 'store']);
    Route::get('/groups/{group}', [GroupController::class, 'show']);
    Route::post('/groups/{group}/join', [GroupController::class, 'join']);
    Route::post('/groups/{group}/subscribe', [GroupController::class, 'subscribe']);
    Route::post('/groups/{group}/unsubscribe', [GroupController::class, 'unsubscribe']);

    // Events
    Route::get('/groups/{group}/events', [EventController::class, 'index']);
    Route::post('/groups/{group}/events', [EventController::class, 'store']);
    Route::get('/events/{event}', [EventController::class, 'show']);

    // Event Participants
    Route::post('/events/{event}/participate', [EventParticipantController::class, 'participate']);

    // Comments
    Route::get('/events/{event}/comments', [EventCommentController::class, 'index']);
    Route::post('/events/{event}/comments', [EventCommentController::class, 'store']);

    // Notifications
    Route::post('/groups/{group}/notifications', [NotificationController::class, 'store']);
});
