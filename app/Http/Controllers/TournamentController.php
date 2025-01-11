<?php

namespace App\Http\Controllers;

use App\Http\Requests\TournamentRequest;
use App\Http\Resources\TournamentResource;
use App\Models\Tournament;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TournamentController
{
    public function index(): JsonResponse
    {
        $tournaments = Tournament::with('creator')
            ->withCount('participants')
            ->paginate(10);
        return response()->json(TournamentResource::collection($tournaments));
    }

    public function store(TournamentRequest $request): JsonResponse
    {
        $tournament = DB::transaction(function () use ($request) {
            return Tournament::create([
                'name'        => $request->name,
                'description' => $request->description,
                'entry_fee'   => $request->entry_fee,
                'creator_id'  => Auth::id(),
                'type'        => $request->type,
                'start_date'  => $request->start_date,
                'end_date'    => $request->end_date
            ]);
        });

        return response()->json(new TournamentResource($tournament->load('creator')), 201);
    }

    public function show(Tournament $tournament): JsonResponse
    {
        $tournament->load(['creator', 'participants'])
            ->loadCount('participants');

        return response()->json(new TournamentResource($tournament));
    }

    public function update(TournamentRequest $request, Tournament $tournament): JsonResponse
    {
        $this->authorize('update', $tournament);

        DB::transaction(function () use ($request, $tournament) {
            $tournament->update($request->validated());
        });

        return response()->json(new TournamentResource($tournament->load('creator')));
    }

    public function destroy(Tournament $tournament): JsonResponse
    {
        $this->authorize('delete', $tournament);

        DB::transaction(function () use ($tournament) {
            $tournament->participants()->delete();
            $tournament->delete();
        });

        return response()->json(null, 204);
    }
}
