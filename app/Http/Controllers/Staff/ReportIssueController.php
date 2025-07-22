<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportIssueController extends Controller
{
    public function create(Request $request)
    {
        return Inertia::render('Staff/ReportIssue', [
            'user' => $request->user(),
        ]);
    }
}
