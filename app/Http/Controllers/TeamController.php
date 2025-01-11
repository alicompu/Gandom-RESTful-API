<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamRequest;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeamController extends Controller
{

    public function index(): JsonResponse
    {
        $teams = Auth::user()->teams()->with(['captain', 'members'])->get();
        return response()->json(TeamResource::collection($teams));
    }

    public function store(TeamRequest $request): JsonResponse
    {
        $team = DB::transaction(function () use ($request) {
            $team = Team::create([
                'name'       => $request->name,
                'captain_id' => Auth::id()
            ]);

            $team->members()->attach(Auth::id(), [
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return $team;
        });

        return response()->json(new TeamResource($team->load(['captain', 'members'])), 201);
    }

    public function show(Team $team): JsonResponse
    {
        $team->load(['members', 'captain']);
        return response()->json(new TeamResource($team));
    }

    public function update(TeamRequest $request, Team $team): JsonResponse
    {
        $this->authorize('update', $team);

        DB::transaction(function () use ($request, $team) {
            $team->update($request->validated());
        });

        return response()->json(new TeamResource($team->load(['captain', 'members'])));
    }

    public function destroy(Team $team): JsonResponse
    {
        $this->authorize('delete', $team);

        DB::transaction(function () use ($team) {
            $team->members()->detach();
            $team->delete();
        });

        return response()->json(null, 204);
    }
}
