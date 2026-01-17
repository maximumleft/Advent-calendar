<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Models\Event;
use App\Models\Group;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    public function index(Group $group): JsonResponse
    {
        if (!$group->is_public && !$group->isMember(auth()->id())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($group->events()->with(['creator', 'participants.user'])->get());
    }

    public function store(StoreEventRequest $request, Group $group): JsonResponse
    {
        if (!$group->isMember(auth()->id())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $event = $group->events()->create(array_merge(
            $request->validated(),
            ['user_id' => auth()->id()]
        ));

        return response()->json($event->load('creator'), 201);
    }

    public function show(Event $event): JsonResponse
    {
        if (!$event->group->is_public && !$event->group->isMember(auth()->id())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($event->load(['creator', 'participants.user', 'comments.user']));
    }

    public function update(StoreEventRequest $request, Event $event): JsonResponse
    {
        if ($event->user_id !== auth()->id() && !$event->group->canEdit(auth()->id())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $event->update($request->validated());

        return response()->json($event);
    }

    public function destroy(Event $event): JsonResponse
    {
        if ($event->user_id !== auth()->id() && !$event->group->canEdit(auth()->id())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $event->delete();

        return response()->json(['message' => 'Event deleted']);
    }
}
