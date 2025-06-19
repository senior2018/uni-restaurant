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

        return Inertia::render('Admin/MealCategories/Index', [
            'categories' => MealCategory::orderBy('name')->get(),
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


    // public function destroy(MealCategory $mealCategory)
    // {
    //     $this->authorize('delete', $mealCategory);

    //     try {
    //         $mealCategory->delete();
    //         return redirect()->back()->with('success', 'Category deleted successfully.');
    //     } catch (\Illuminate\Database\QueryException $e) {
    //         return redirect()->back()->with('error', 'Cannot delete this category. It is in use.');
    //     }
    // }

    public function destroy(MealCategory $mealCategory)
    {
        $this->authorize('delete', $mealCategory);

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
}
