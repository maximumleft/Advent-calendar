<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Event;
use App\Models\EventComment;
use Illuminate\Http\JsonResponse;

class EventCommentController extends Controller
{
    public function index(Event $event): JsonResponse
    {
        if (!$event->group->isMember(auth()->id())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($event->comments()->with('user', 'replies.user')->whereNull('parent_id')->get());
    }

    public function store(StoreCommentRequest $request, Event $event): JsonResponse
    {
        if (!$event->group->isMember(auth()->id())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment = $event->comments()->create(array_merge(
            $request->validated(),
            ['user_id' => auth()->id()]
        ));

        return response()->json($comment->load('user'), 201);
    }

    public function destroy(EventComment $comment): JsonResponse
    {
        if ($comment->user_id !== auth()->id() && !$comment->event->group->canEdit(auth()->id())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted']);
    }
}
