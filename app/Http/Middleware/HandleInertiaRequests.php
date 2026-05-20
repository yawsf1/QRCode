<?php

namespace App\Http\Middleware;

use App\Services\NotificationService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => fn () => [
                'user' => $user,
            ],
            'checkedInToday' => fn () => $user?->isEmploye()
                ? $user->hasCheckedInToday()
                : false,
            'flash' => fn () => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'warning' => $request->session()->get('warning'),
            ],
            'notifications' => fn () => $request->user()?->isAdmin()
                ? [
                    'items' => app(NotificationService::class)->unreadForAdmin($request->user()->id),
                    'count' => app(NotificationService::class)->unreadCountForAdmin($request->user()->id),
                ]
                : ['items' => [], 'count' => 0],
        ];
    }
}
