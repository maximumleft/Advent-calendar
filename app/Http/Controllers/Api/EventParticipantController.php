<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ParticipateEventRequest;
use App\Models\Event;
use App\Models\EventParticipant;
use Illuminate\Http\JsonResponse;

class EventParticipantController extends Controller
{
    public function participate(ParticipateEventRequest $request, Event $event): JsonResponse
    {
        if (!$event->group->isMember(auth()->id())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $participant = $event->participants()->updateOrCreate(
            ['user_id' => auth()->id()],
            ['status' => $request->status]
        );

        return response()->json($participant);
    }

    public function index(Event $event): JsonResponse
    {
        if (!$event->group->isMember(auth()->id())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($event->participants()->with('user')->get());
    }
}
