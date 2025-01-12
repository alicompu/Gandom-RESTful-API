<?php

namespace App\Http\Controllers;

use App\Http\Requests\TournamentRequest;
use App\Http\Resources\TournamentResource;
use App\Models\Tournament;
use http\Client\Curl\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TournamentParticipationController
{
    public function join(Tournament $tournament): JsonResponse
    {
        DB::transaction(function () use ($tournament) {
            $user = \App\Models\User::query()->lockForUpdate()->find(Auth::id());

            if ($user->balance < $tournament->entry_fee) {
                throw new \Exception('Insufficient balance');
            }

            $user->balance -= $tournament->entry_fee;
            $user->save();

            $tournament->participants()->create([
                'participant_id'   => $user->id,
                'participant_type' => 'User',
                'payment_status'   => true
            ]);
        });

        return response()->json([
            'message'    => 'Successfully joined tournament',
            'tournament' => new TournamentResource($tournament->load('creator')->loadCount('participants'))
        ]);
    }

    public function myTournaments(): JsonResponse
    {
        $tournaments = Tournament::where('creator_id', Auth::id())
            ->with('creator')
            ->withCount('participants')
            ->paginate(10);

        return response()->json(TournamentResource::collection($tournaments));
    }

    public function participatingTournaments(): JsonResponse
    {
        $tournaments = Tournament::whereHas('participants', function ($query) {
            $query->where('participant_id', Auth::id())
                ->where('participant_type', 'User');
        })
            ->with('creator')
            ->withCount('participants')
            ->paginate(10);

        return response()->json(TournamentResource::collection($tournaments));
    }
}
