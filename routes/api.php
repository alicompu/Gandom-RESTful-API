<?php

use App\Http\Controllers\{TeamController,
    TeamParticipationController,
    TournamentController,
    TournamentParticipationController};
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('teams/captained', [TeamParticipationController::class, 'captainedTeams']);
    Route::post('teams/{team}/join', [TeamParticipationController::class, 'join']);
    Route::post('teams/{team}/leave', [TeamParticipationController::class, 'leave']);
    Route::post('teams/{team}/members/{user}/remove', [TeamParticipationController::class, 'removeMember']);
    Route::apiResource('teams', TeamController::class);

    Route::get('tournaments/my', [TournamentParticipationController::class, 'myTournaments']);
    Route::get('tournaments/participating', [TournamentParticipationController::class, 'participatingTournaments']);
    Route::post('tournaments/{tournament}/join', [TournamentParticipationController::class, 'join']);
    Route::apiResource('tournaments', TournamentController::class);
});
