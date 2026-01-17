<?php

namespace App\Jobs;

use App\Mail\GroupNotificationMail;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendGroupNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected Notification $notification,
        protected User $user
    ) {}

    public function handle(): void
    {
        Mail::to($this->user->email)->send(new GroupNotificationMail($this->notification, $this->user));
    }
}
