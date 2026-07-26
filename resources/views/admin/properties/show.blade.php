@extends('layouts.admin')

@section('header', 'Property Details')

@section('content')
<div class="content-card">
    <div class="card-header">
        <h2>{{ $property->name }}</h2>
        <a href="{{ route('admin.properties.index') }}" class="btn" style="background:#f1f5f9;color:var(--text-main);">Back</a>
    </div>
    <div style="padding: 2rem;">
        <p><strong>Vendor:</strong> {{ $property->vendor->business_name ?? 'N/A' }}</p>
        <p><strong>Category:</strong> {{ $property->category->name ?? 'N/A' }}</p>
        <p><strong>Status:</strong> {{ $property->status }}</p>
        <p><strong>Price/Amount:</strong> ₹{{ number_format($property->amount, 2) }}</p>
        <div style="margin-top: 1rem;">
            <a href="{{ route('admin.properties.edit', $property->id) }}" class="btn btn-primary">Edit Property</a>
        </div>
    </div>
</div>
@endsection
