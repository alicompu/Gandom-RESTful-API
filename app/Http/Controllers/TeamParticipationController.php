<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamRequest;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeamParticipationController extends Controller
{
    public function captainedTeams(): JsonResponse
    {
        $teams = Auth::user()
            ->captainedTeams()
            ->with(['members'])
            ->withCount('members')
            ->get();

        return response()->json(TeamResource::collection($teams));
    }

    public function join(Team $team): JsonResponse
    {
        DB::transaction(function () use ($team) {
            $team->members()->attach(Auth::id(), [
                'created_at' => now(),
                'updated_at' => now()
            ]);
        });

        return response()->json([
            'message' => 'Successfully joined team',
            'team'    => new TeamResource($team->load(['captain', 'members']))
        ]);
    }

    public function leave(Team $team): JsonResponse
    {
        DB::transaction(function () use ($team) {
            $team->members()->detach(Auth::id());
        });

        return response()->json([
            'message' => 'Successfully left team',
            'team'    => new TeamResource($team->load(['captain', 'members']))
        ]);
    }


    public function removeMember(Team $team, User $user): JsonResponse
    {
        $this->authorize('removeMember', [$team, $user]);

        DB::transaction(function () use ($team, $user) {
            $team->members()->detach($user->id);
        });

        return response()->json([
            'message' => 'Member removed successfully',
            'team'    => new TeamResource($team->load(['captain', 'members']))
        ]);
    }
}
