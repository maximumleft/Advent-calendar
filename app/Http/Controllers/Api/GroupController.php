<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGroupRequest;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index(): JsonResponse
    {
        $groups = auth()->user()->groups()->with('owner')->get();
        return response()->json($groups);
    }

    public function store(StoreGroupRequest $request): JsonResponse
    {
        $user = auth()->user();
        $group = Group::create([
            'name' => $request->name,
            'description' => $request->description,
            'owner_id' => $user->id,
            'color' => $request->color,
            'is_public' => $request->is_public ?? false,
        ]);

        $group->members()->create([
            'user_id' => $user->id,
            'role' => 'owner',
            'status' => 'accepted',
            'subscribed' => true,
            'joined_at' => now(),
        ]);

        return response()->json($group->load('owner', 'members'), 201);
    }

    public function show(Group $group): JsonResponse
    {
        return response()->json($group->load(['owner', 'members.user', 'events']));
    }

    public function join(Group $group): JsonResponse
    {
        $user = auth()->user();
        if ($group->isMember($user->id)) {
            return response()->json(['message' => 'Already a member'], 422);
        }

        $group->members()->create([
            'user_id' => $user->id,
            'role' => 'member',
            'status' => $group->is_public ? 'accepted' : 'pending',
            'subscribed' => true,
            'joined_at' => $group->is_public ? now() : null,
        ]);

        return response()->json(['message' => 'Joined successfully']);
    }

    public function subscribe(Group $group): JsonResponse
    {
        $user = auth()->user();
        $member = $group->members()->where('user_id', $user->id)->first();
        if (!$member) return response()->json(['message' => 'Not a member'], 403);

        $member->update(['subscribed' => true]);
        return response()->json(['message' => 'Subscribed']);
    }

    public function unsubscribe(Group $group): JsonResponse
    {
        $user = auth()->user();
        $member = $group->members()->where('user_id', $user->id)->first();
        if (!$member) return response()->json(['message' => 'Not a member'], 403);

        $member->update(['subscribed' => false]);
        return response()->json(['message' => 'Unsubscribed']);
    }
}
