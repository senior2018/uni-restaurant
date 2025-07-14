<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Requests\MealRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class MealController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Meal::class);

        $meals = Meal::withTrashed()
            ->with('category')
            ->orderByRaw('deleted_at IS NULL DESC') // Non-deleted first
            ->orderByRaw('is_available DESC') // Available first within non-deleted
            ->orderBy('created_at', 'desc') // Most recent first within each group
            ->get();

        return Inertia::render('Admin/Meals/Index', [
            'meals' => $meals,
            'categories' => \App\Models\MealCategory::orderBy('name')->get()
        ]);
    }

    public function staffIndex()
    {
        $this->authorize('viewAny', Meal::class);

        $meals = Meal::with('category')
            ->orderByRaw('is_available DESC') // Available first
            ->orderBy('created_at', 'desc') // Most recent first
            ->get(); // Only non-trashed meals

        $categories = \App\Models\MealCategory::orderBy('name')->get();

        return Inertia::render('Staff/Meals/Index', [
            'meals' => $meals,
            'categories' => $categories,
            'user' => auth()->user(),
            'userRole' => auth()->user()->role,
            'permissions' => [
                'canToggle' => auth()->user()->can('toggleAvailability', new Meal()),
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd('Store Mathod hit');

        $this->authorize('create', Meal::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:meal_categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048', // max 2MB
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('meals', 'public');
            $validated['image_url'] = '/storage/' . $path;
        }

        Meal::create($validated);

        return redirect()->back()->with('success', 'Meal created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Meal $meal)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Meal $meal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MealRequest $request, Meal $meal)
    {
        // dd('Update hits');
        $this->authorize('update', $meal);

        $data = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('meals', 'public');
            $data['image_url'] = '/storage/' . $path;
        }

        $meal->update($data);

        return redirect()->back()->with('success', 'Meal updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Meal $meal)
    {
        // dd('Delete hit');

        $this->authorize('delete', $meal);

        $meal->delete();

        return redirect()->back()->with('success', 'Meal moved to trash!');
    }

    /**
     * Restore a soft deleted meal.
     */
    public function restore($id)
    {
        $meal = Meal::withTrashed()->findOrFail($id);

        $this->authorize('restore', $meal);

        $meal->restore();

        return redirect()->back()->with('success', 'Meal restored successfully!');
    }

    /**
     * Permanently delete the specified resource.
     */
    public function forceDelete($id)
    {
        $meal = Meal::withTrashed()->findOrFail($id);

        $this->authorize('forceDelete', $meal);

        // Delete the image file if exists
        if ($meal->image_url) {
            $imagePath = str_replace('/storage/', '', $meal->image_url);
            \Storage::disk('public')->delete($imagePath);
        }

        $meal->forceDelete();

        return redirect()->back()->with('success', 'Meal permanently deleted!');
    }


    public function toggleAvailability(Meal $meal)
    {
        $this->authorize('toggleAvailability', $meal);

        $meal->update(['is_available' => !$meal->is_available]);

        return redirect()->back()->with('success', 'Meal availability updated!');
    }
}
