<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerMenuController extends Controller
{
    public function index()
    {
        $meals = Meal::with('category')
            ->where('is_available', true)
            ->get();

        return Inertia::render('Customer/Menu', [
            'meals' => $meals,
            'user' => request()->user(),
        ]);
    }
}
