<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ContractorDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function upload(Request $request, Application $application)
    {
        // Only contractor who owns the application can upload
        if (Auth::user()->role !== 'contractor' || $application->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'document_type' => 'required|string',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240', // 10MB max
        ]);

        $file = $request->file('file');
        $path = $file->store('contractor-documents', 'public');

        $application->documents()->create([
            'document_type' => $request->document_type,
            'document_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => round($file->getSize() / 1024, 2), // KB
        ]);

        return redirect()->back()->with('success', 'Document uploaded successfully.');
    }

    public function download(ContractorDocument $document)
    {
        // Contractor (owner) or Supervisor can download
        $user = Auth::user();
        if ($user->role === 'contractor' && $document->application->user_id !== $user->id) {
            abort(403);
        }
        if ($user->role !== 'contractor' && $user->role !== 'supervisor' && $user->role !== 'owner') {
            abort(403);
        }

        return Storage::disk('public')->download($document->file_path, $document->document_name);
    }

    public function delete(ContractorDocument $document)
    {
        // Only contractor who owns it can delete (and only if not verified)
        if (Auth::user()->role !== 'contractor' || $document->application->user_id !== Auth::id()) {
            abort(403);
        }

        if ($document->is_verified) {
            return redirect()->back()->with('error', 'Cannot delete verified document.');
        }

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return redirect()->back()->with('success', 'Document deleted successfully.');
    }

    public function verify(Request $request, ContractorDocument $document)
    {
        // Only supervisor can verify
        if (Auth::user()->role !== 'supervisor') {
            abort(403);
        }

        $request->validate([
            'is_verified' => 'required|boolean',
            'verification_notes' => 'nullable|string',
        ]);

        $document->update([
            'is_verified' => $request->is_verified,
            'verification_notes' => $request->verification_notes,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Document verification updated.');
    }
}
