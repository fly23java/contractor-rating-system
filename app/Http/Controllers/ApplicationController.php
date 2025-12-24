<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Tender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    // Used by Supervisor to list specific queue? 
    // Logic handled in DashboardController for listing.

    public function index() {
        if (Auth::user()->role !== 'supervisor') abort(403);
        // Maybe detail list
        return redirect()->route('dashboard');
    }

    public function create(Tender $tender)
    {
        if (Auth::user()->role !== 'contractor') abort(403);
        
        // Check if already applied
        $alreadyApplied = $tender->applications()->where('user_id', Auth::id())->exists();
        if ($alreadyApplied) {
            return redirect()->route('dashboard')->with('error', 'You have already applied to this tender.');
        }
        
        return view('applications.create', compact('tender'));
    }

    public function store(Request $request, Tender $tender)
    {
        if (Auth::user()->role !== 'contractor') abort(403);

        $validated = $request->validate([
            'price' => 'required|numeric|min:0',
            'financial_capability' => 'required|numeric|min:0',
            'guarantees_value' => 'required|numeric|min:0',
            'experience_projects' => 'required|integer|min:0',
            'field_experience_projects' => 'required|integer|min:0|max:5',
            'post_service_months' => 'required|integer|min:0|max:24',
            'notes' => 'nullable|string',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        $application = $tender->applications()->create([
            'user_id' => Auth::id(),
            'price' => $validated['price'],
            'price_value' => $validated['price'], // Set initial price value for grading
            'financial_capability' => $validated['financial_capability'],
            'guarantees_value' => $validated['guarantees_value'],
            'experience_projects' => $validated['experience_projects'],
            'field_experience_projects' => $validated['field_experience_projects'],
            'post_service_months' => $validated['post_service_months'],
            'notes' => $validated['notes'] ?? '',
            'status' => 'PENDING',
        ]);

        // Handle document uploads
        // Handle document uploads
        if ($request->hasFile('documents')) {
            $files = $request->file('documents');
            
            // Handle regular single files
            foreach ($files as $docType => $content) {
                if ($docType === 'supporting_documents') {
                    // Handle array of files
                    foreach ($content as $index => $file) {
                        if ($file) {
                            $path = $file->store('contractor-documents', 'public');
                            $application->documents()->create([
                                'document_type' => 'supporting_document_' . ($index + 1),
                                'document_name' => $file->getClientOriginalName(),
                                'file_path' => $path,
                                'file_size' => round($file->getSize() / 1024, 2),
                            ]);
                        }
                    }
                } else {
                    // Handle single file inputs
                    $file = $content;
                    if ($file instanceof \Illuminate\Http\UploadedFile) {
                        $path = $file->store('contractor-documents', 'public');
                        $application->documents()->create([
                            'document_type' => $docType,
                            'document_name' => $file->getClientOriginalName(),
                            'file_path' => $path,
                            'file_size' => round($file->getSize() / 1024, 2),
                        ]);
                    }
                }
            }
        }

        return redirect()->route('dashboard')->with('success', 'Application submitted successfully.');
    }

    public function edit(Application $application)
    {
        if (Auth::user()->role !== 'supervisor') abort(403);
        return view('applications.grade', compact('application'));
    }

    public function update(Request $request, Application $application)
    {
        if (Auth::user()->role !== 'supervisor') abort(403);

        $validated = $request->validate([
            // Raw values for 10 criteria
            'price_value' => 'required|numeric|min:0',
            'quality_points' => 'required|numeric|min:0|max:100',
            'financial_capability' => 'required|numeric|min:0',
            'experience_projects' => 'required|integer|min:0',
            'contract_terms_points' => 'required|numeric|min:0|max:100',
            'field_experience_projects' => 'required|integer|min:0|max:5',
            'executive_capability_points' => 'required|numeric|min:0|max:100',
            'post_service_months' => 'required|integer|min:0|max:24',
            'guarantees_value' => 'required|numeric|min:0',
            'safety_points' => 'required|numeric|min:50|max:95',
            'status' => 'required|string|in:PENDING,APPROVED,REJECTED',
        ]);

        // Update raw values
        $application->update($validated);
        $application->evaluator_id = Auth::id();
        $application->save();

        // Trigger automatic calculation for all applications in this tender
        $service = new \App\Services\ApplicationService();
        $service->calculateTenderScores($application->tender);

        return redirect()->route('dashboard')->with('success', 'Application graded successfully. Scores calculated automatically.');
    }
}
