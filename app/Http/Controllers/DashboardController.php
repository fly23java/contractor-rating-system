<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tender;
use App\Models\Application;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'owner') {
            $tenders = Tender::where('user_id', $user->id)->get();
            return view('owner.dashboard', compact('tenders'));
        }

        if ($user->role === 'contractor') {
            $tenders = Tender::where('status', 'OPEN')->get(); // Available tenders
            $myApplications = Application::where('user_id', $user->id)->with('tender')->get();
            return view('contractor.dashboard', compact('tenders', 'myApplications'));
        }

        if ($user->role === 'supervisor') {
            $tenders = Tender::withCount('applications')->orderByDesc('created_at')->get();
            $applications = Application::with(['tender', 'contractor'])->orderBy('status')->get();
            return view('supervisor.dashboard', compact('applications', 'tenders'));
        }

        return abort(403);
    }
}
