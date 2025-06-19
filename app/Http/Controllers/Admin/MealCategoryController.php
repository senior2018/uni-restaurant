<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MealCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MealCategoryController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', MealCategory::class);

        // Load categories with their meals and meal counts
        $categories = MealCategory::with(['meals' => function($query) {
                $query->orderBy('name'); // Order meals by name
            }])
            ->withCount('meals') // This gives us meals_count
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/MealCategories/Index', [
            'categories' => $categories
        ]);
    }

    // Show categories with trashed ones
    public function indexWithTrashed()
    {
        $this->authorize('viewAny', MealCategory::class);

        $categories = MealCategory::withTrashed()
            ->with(['meals' => function($query) {
                $query->orderBy('name');
            }])
            ->withCount('meals')
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/MealCategories/Index', [
            'categories' => $categories,
            'showingTrashed' => true
        ]);
    }

    // Show only trashed categories
    public function trashedOnly()
    {
        $this->authorize('viewAny', MealCategory::class);

        $categories = MealCategory::onlyTrashed()
            ->with(['meals' => function($query) {
                $query->orderBy('name');
            }])
            ->withCount('meals')
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/MealCategories/Index', [
            'categories' => $categories,
            'trashedOnly' => true
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', MealCategory::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        MealCategory::create($validated);

        return redirect()->back()->with('success', 'Category created!');
    }

    public function update(Request $request, MealCategory $mealCategory)
    {
        $this->authorize('update', $mealCategory);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $mealCategory->update($validated);

        return redirect()->back()->with('success', 'Category updated!');
    }

    public function destroy(MealCategory $mealCategory)
    {
        $this->authorize('delete', $mealCategory);

        // Check if category has meals before allowing deletion
        if ($mealCategory->meals()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete category. It has associated meals.');
        }

        try {
            $mealCategory->delete();
            session()->flash('success', 'Category deleted successfully.');
            // dd(session()->all()); // This will show you what's in the session
            return redirect()->back();
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', 'Cannot delete this category. It is in use.');
            // dd(session()->all()); // This will show you what's in the session
            return redirect()->back();
        }
    }

    // NEW: Restore a soft-deleted category
    public function restore($id)
    {
        $category = MealCategory::withTrashed()->findOrFail($id);
        $this->authorize('update', $category); // Use update permission for restore

        if (!$category->trashed()) {
            return redirect()->back()->with('error', 'Category is not deleted.');
        }

        $category->restore();
        return redirect()->back()->with('success', 'Category restored successfully!');
    }

    // NEW: Permanently delete a category
    public function forceDelete($id)
    {
        $category = MealCategory::withTrashed()->findOrFail($id);
        $this->authorize('delete', $category);

        // Check if it has meals even when soft deleted
        if ($category->meals()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot permanently delete category. It still has associated meals.');
        }

        $category->forceDelete(); // Permanently delete
        return redirect()->back()->with('success', 'Category permanently deleted.');
    }
}
