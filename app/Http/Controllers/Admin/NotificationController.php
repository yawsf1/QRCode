<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(protected NotificationService $notifications) {}

    public function markRead(Request $request, int $notification)
    {
        $this->notifications->markAsRead($notification, $request->user());

        return back();
    }

    public function markAllRead(Request $request)
    {
        $this->notifications->markAllAsRead($request->user()->id);

        return back();
    }
}
