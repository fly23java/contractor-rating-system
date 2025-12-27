<?php

namespace App\Http\Controllers;

use App\Models\Tender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenderController extends Controller
{
    public function index()
    {
        // Used by owner mostly via Dashboard, but if accessed directly:
        if (Auth::user()->role === 'owner') {
            return redirect()->route('dashboard');
        }
        return redirect()->route('dashboard');
    }

    public function create()
    {
        if (Auth::user()->role !== 'owner') abort(403);
        return view('tenders.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'owner') abort(403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'min_price' => 'nullable|numeric',
            'max_price' => 'nullable|numeric',
            'deadline' => 'required|date',
        ]);

        $request->user()->tenders()->create($validated);

        return redirect()->route('dashboard')->with('success', 'Tender created successfully.');
    }

    public function show(Tender $tender)
    {
        // If owner, show applicants
        if (Auth::user()->role === 'owner') {
             if ($tender->user_id !== Auth::id()) abort(403);
             return view('tenders.show', compact('tender'));
        }
        
        // If contractor, show details (implemented as modal in dashboard for simplicity, or we can add a view)
        // keeping simple redirect for now or implementing a view if needed.
        // For MVP, Contractor applies from dashboard using modal.
        return redirect()->route('dashboard');
    }


    public function editWeights(Tender $tender)
    {
        if (Auth::user()->role !== 'supervisor') abort(403);
        return view('supervisor.tenders.edit_weights', compact('tender'));
    }

    public function updateWeights(Request $request, Tender $tender)
    {
        if (Auth::user()->role !== 'supervisor') abort(403);

        $validated = $request->validate([
            'weight_price' => 'required|numeric|min:0|max:100',
            'weight_quality' => 'required|numeric|min:0|max:100',
            'weight_financial_capability' => 'required|numeric|min:0|max:100',
            'weight_experience' => 'required|numeric|min:0|max:100',
            'weight_contract_terms' => 'required|numeric|min:0|max:100',
            'weight_field_experience' => 'required|numeric|min:0|max:100',
            'weight_executive_capability' => 'required|numeric|min:0|max:100',
            'weight_post_service' => 'required|numeric|min:0|max:100',
            'weight_guarantees' => 'required|numeric|min:0|max:100',
            'weight_safety' => 'required|numeric|min:0|max:100',
        ]);

        $tender->update($validated);

        return redirect()->route('dashboard')->with('success', 'Tender weights updated successfully.');
    }

    public function exportPdf(Tender $tender)
    {
        if (Auth::user()->role !== 'owner') abort(403);
        if ($tender->user_id !== Auth::id()) abort(403);

        $accepted = $tender->applications()->where('is_excluded', false)
            ->orderBy('weighted_total', 'desc')
            ->get();
        
        $excluded = $tender->applications()->where('is_excluded', true)->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('tenders.pdf', [
            'tender' => $tender,
            'accepted' => $accepted,
            'excluded' => $excluded,
        ]);

        return $pdf->download('tender-' . $tender->id . '-ranking.pdf');
    }

    public function printPdf(Tender $tender)
    {
        if (Auth::user()->role !== 'owner') abort(403);
        if ($tender->user_id !== Auth::id()) abort(403);

        $accepted = $tender->applications()->where('is_excluded', false)
            ->orderBy('weighted_total', 'desc')
            ->get();
        
        $excluded = $tender->applications()->where('is_excluded', true)->get();

        return view('tenders.print', compact('tender', 'accepted', 'excluded'));
    }
}
