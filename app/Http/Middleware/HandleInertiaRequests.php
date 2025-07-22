<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
            ],
            'notifications' => $user ? $user->unreadNotifications()->take(50)->get()->map(function($n) {
                return [
                    'id' => $n->id,
                    'type' => $n->data['type'] ?? '',
                    'data' => $n->data,
                    'created_at' => $n->created_at->toDateTimeString(),
                    'read_at' => $n->read_at,
                ];
            }) : [],
        ];
    }
}
