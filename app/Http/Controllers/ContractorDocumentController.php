<?php

namespace App\Http\Controllers;

use App\Models\ContractorDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractorDocumentController extends Controller
{
    public function index()
    {
        if (Auth::user()->role !== 'contractor') {
            abort(403);
        }

        // Get contractor's profile documents
        $documents = Auth::user()->documents;
        
        // Get all required document types
        $requiredDocuments = ContractorDocument::REQUIRED_DOCUMENTS;

        return view('contractor.documents', compact('documents', 'requiredDocuments'));
    }
}
