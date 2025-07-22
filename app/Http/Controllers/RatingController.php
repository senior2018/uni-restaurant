<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Order;
use App\Models\Meal;
use App\Models\User;
use App\Notifications\NewRatingNotification;
use App\Notifications\RatingRespondedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RatingController extends Controller
{
    use AuthorizesRequests;
    // List all ratings (admin/staff)
    public function index(Request $request)
    {
        $this->authorize('viewAny', Rating::class);
        $query = Rating::with(['user', 'order', 'meal']);
        if ($request->filled('meal_id')) {
            $query->where('meal_id', $request->meal_id);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }
        $ratings = $query->latest()->paginate(20);
        return Inertia::render('Admin/Ratings/Index', [
            'ratings' => $ratings
        ]);
    }

    // Show a single rating
    public function show(Rating $rating)
    {
        $this->authorize('view', $rating);
        $rating->load(['user', 'order', 'meal']);
        return Inertia::render('Admin/Ratings/Show', [
            'rating' => $rating
        ]);
    }

    // Customer submits a rating
    public function store(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'meal_id' => 'nullable|exists:meals,id',
        ]);
        $order = Order::findOrFail($validated['order_id']);
        $this->authorize('create', [Rating::class, $order]);
        // Only allow rating if order is delivered and belongs to user
        if ($order->user_id !== $user->id || $order->status !== 'delivered') {
            abort(403, 'You can only rate your delivered orders.');
        }
        // If meal_id is present, treat as meal rating (legacy)
        if (!empty($validated['meal_id'])) {
            $meal = Meal::findOrFail($validated['meal_id']);
            // Prevent duplicate rating for same meal/order
            if (Rating::where('user_id', $user->id)->where('order_id', $order->id)->where('meal_id', $meal->id)->exists()) {
                return back()->with('error', 'You have already rated this meal for this order.');
            }
            $rating = Rating::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'meal_id' => $meal->id,
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
            ]);
        } else {
            // Whole order rating: only one per order per user
            if (Rating::where('user_id', $user->id)->where('order_id', $order->id)->whereNull('meal_id')->exists()) {
                return back()->with('error', 'You have already rated this order.');
            }
            $rating = Rating::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'meal_id' => null,
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
            ]);
        }
        // Notify admin/staff (to be filled in)
        $staffAndAdmins = User::whereIn('role', ['admin', 'staff', 'super_admin'])->get();
        \Notification::send($staffAndAdmins, new NewRatingNotification($rating));
        return back()->with('success', 'Thank you for your feedback!');
    }

    // Admin/staff respond to a rating
    public function respond(Request $request, Rating $rating)
    {
        $this->authorize('respond', $rating);
        $validated = $request->validate([
            'response_comment' => 'required|string|max:1000',
        ]);
        $rating->response_staff_id = $request->user()->id;
        $rating->response_comment = $validated['response_comment'];
        $rating->responded_at = now();
        $rating->save();
        // Notify customer (to be filled in)
        $rating->user->notify(new RatingRespondedNotification($rating));
        return back()->with('success', 'Response sent to customer.');
    }
}
