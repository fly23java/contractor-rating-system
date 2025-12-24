@extends('layouts.app')
@section('content')
<h1>Create New Tender</h1>

<form action="{{ route('tenders.store') }}" method="POST">
    @csrf
    <div class="card">
        <h3>Tender Details</h3>
        
        <div class="form-group">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" required value="{{ old('title') }}">
        </div>

        <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4" required>{{ old('description') }}</textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label class="form-label">Min Price ($)</label>
                <input type="number" name="min_price" class="form-control" step="0.01" required value="{{ old('min_price') }}">
            </div>

            <div class="form-group">
                <label class="form-label">Max Price ($)</label>
                <input type="number" name="max_price" class="form-control" step="0.01" required value="{{ old('max_price') }}">
            </div>

            <div class="form-group">
                <label class="form-label">Deadline</label>
                <input type="date" name="deadline" class="form-control" required value="{{ old('deadline') }}">
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem; margin-top: 2rem;">
        Publish Tender
    </button>
</form>
@endsection
