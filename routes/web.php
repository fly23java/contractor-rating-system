<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TenderController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ContractorDocumentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Owner Routes
    Route::resource('tenders', TenderController::class);
    Route::get('tenders/{tender}/export-pdf', [TenderController::class, 'exportPdf'])->name('tenders.export-pdf');
    Route::get('tenders/{tender}/print-pdf', [TenderController::class, 'printPdf'])->name('tenders.print-pdf');
    
    // Contractor Routes
    Route::get('tenders/{tender}/apply', [ApplicationController::class, 'create'])->name('applications.create');
    Route::post('tenders/{tender}/apply', [ApplicationController::class, 'store'])->name('applications.store');
    Route::get('/my-documents', [ContractorDocumentController::class, 'index'])->name('contractor.documents');
    
    // Document Routes (Application-based, optional)
    Route::post('applications/{application}/documents', [DocumentController::class, 'upload'])->name('documents.upload');
    Route::get('documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::delete('documents/{document}', [DocumentController::class, 'delete'])->name('documents.delete');
    Route::post('documents/{document}/verify', [DocumentController::class, 'verify'])->name('documents.verify');
    
    // Supervisor Routes
    Route::get('applications', [ApplicationController::class, 'index'])->name('applications.index');
    Route::get('applications/{application}/grade', [ApplicationController::class, 'edit'])->name('applications.grade');
    Route::put('applications/{application}', [ApplicationController::class, 'update'])->name('applications.update');
    
    // Supervisor Tender Management
    Route::get('tenders/{tender}/weights', [TenderController::class, 'editWeights'])->name('tenders.weights.edit');
    Route::put('tenders/{tender}/weights', [TenderController::class, 'updateWeights'])->name('tenders.weights.update');

    // User Management
    Route::resource('users', \App\Http\Controllers\UserController::class);
});
