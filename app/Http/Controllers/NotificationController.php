<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function markAsRead(Request $request, $id = null)
    {
        $user = $request->user();
        $ids = $request->input('ids');
        if (is_array($ids) && count($ids)) {
            $user->notifications()->whereIn('id', $ids)->whereNull('read_at')->update(['read_at' => now()]);
            if ($request->wantsJson()) {
                return response()->noContent();
            }
            return back();
        }
        if ($id) {
            $notification = $user->notifications()->where('id', $id)->first();
            if ($notification && !$notification->read_at) {
                $notification->markAsRead();
            }
        }
        if ($request->wantsJson()) {
            return response()->noContent();
        }
        return back();
    }
}
