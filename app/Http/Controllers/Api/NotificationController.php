<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNotificationRequest;
use App\Models\Group;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function index(Group $group): JsonResponse
    {
        if (!$group->canEdit(auth()->id())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($group->notifications()->with('sender')->latest()->get());
    }

    public function store(StoreNotificationRequest $request, Group $group): JsonResponse
    {
        if (!$group->canEdit(auth()->id())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $notification = $group->notifications()->create([
            'user_id' => auth()->id(),
            'subject' => $request->subject,
            'message' => $request->message,
            'sent_at' => now(),
        ]);

        $members = $group->getSubscribedMembers();

        foreach ($members as $member) {
            NotificationRecipient::create([
                'notification_id' => $notification->id,
                'user_id' => $member->user_id,
            ]);
            
            \App\Jobs\SendGroupNotificationJob::dispatch($notification, $member->user);
        }

        return response()->json($notification->load('recipients'), 201);
    }

    public function myNotifications(): JsonResponse
    {
        $notifications = NotificationRecipient::where('user_id', auth()->id())
            ->with(['notification.group', 'notification.sender'])
            ->latest()
            ->get();

        return response()->json($notifications);
    }

    public function markAsRead(NotificationRecipient $recipient): JsonResponse
    {
        if ($recipient->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $recipient->update(['read_at' => now()]);

        return response()->json(['message' => 'Marked as read']);
    }
}
