@extends('layouts.app')

@section('title', 'Add New Asset')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Add New Asset</h1>
            <p class="text-muted">Create a new asset to exchange</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Assets
            </a>
        </div>
    </div>

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">Asset Details</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('assets.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="title" class="form-label">Asset Title</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="type" class="form-label">Asset Type</label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Select asset type</option>
                                <option value="physical" {{ old('type') == 'physical' ? 'selected' : '' }}>Physical Item</option>
                                <option value="digital" {{ old('type') == 'digital' ? 'selected' : '' }}>Digital Item</option>
                                <option value="service" {{ old('type') == 'service' ? 'selected' : '' }}>Service</option>
                                <option value="crypto" {{ old('type') == 'crypto' ? 'selected' : '' }}>Cryptocurrency</option>
                                <option value="money" {{ old('type') == 'money' ? 'selected' : '' }}>Money/Currency</option>
                                <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="value" class="form-label">Value</label>
                                    <input type="number" step="0.01" class="form-control @error('value') is-invalid @enderror" id="value" name="value" value="{{ old('value') }}" required>
                                    @error('value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="currency" class="form-label">Currency</label>
                                    <select class="form-select @error('currency') is-invalid @enderror" id="currency" name="currency" required>
                                        <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                                        <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                                        <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP</option>
                                        <option value="BTC" {{ old('currency') == 'BTC' ? 'selected' : '' }}>BTC</option>
                                        <option value="ETH" {{ old('currency') == 'ETH' ? 'selected' : '' }}>ETH</option>
                                        <option value="Other" {{ old('currency') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('currency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3 physical-field">
                            <label for="condition" class="form-label">Condition</label>
                            <select class="form-select @error('condition') is-invalid @enderror" id="condition" name="condition">
                                <option value="">Select condition</option>
                                <option value="new" {{ old('condition') == 'new' ? 'selected' : '' }}>New</option>
                                <option value="like_new" {{ old('condition') == 'like_new' ? 'selected' : '' }}>Like New</option>
                                <option value="good" {{ old('condition') == 'good' ? 'selected' : '' }}>Good</option>
                                <option value="fair" {{ old('condition') == 'fair' ? 'selected' : '' }}>Fair</option>
                                <option value="poor" {{ old('condition') == 'poor' ? 'selected' : '' }}>Poor</option>
                            </select>
                            @error('condition')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3 physical-field">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location') }}">
                            @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="images" class="form-label">Images</label>
                            <input type="file" class="form-control @error('images') is-invalid @enderror" id="images" name="images[]" multiple accept="image/*">
                            <div class="form-text">Upload up to 5 images of your asset. Maximum size: 2MB per image.</div>
                            @error('images')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div id="metadata-fields">
                            <!-- Dynamic metadata fields will be added here based on asset type -->
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-info mt-3">
                    <i class="bi bi-info-circle"></i> All assets must be approved by our team before they can be used for exchanges. This process typically takes 1-2 business days.
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Asset</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('type');
        const physicalFields = document.querySelectorAll('.physical-field');
        const metadataFields = document.getElementById('metadata-fields');
        
        // Show/hide fields based on asset type
        typeSelect.addEventListener('change', function() {
            const selectedType = this.value;
            
            // Handle physical item specific fields
            if (selectedType === 'physical') {
                physicalFields.forEach(field => {
                    field.classList.remove('d-none');
                });
            } else {
                physicalFields.forEach(field => {
                    field.classList.add('d-none');
                });
            }
            
            // Clear previous metadata fields
            metadataFields.innerHTML = '';
            
            // Add type-specific metadata fields
            if (selectedType === 'digital') {
                metadataFields.innerHTML = `
                    <div class="mb-3">
                        <label for="metadata_format" class="form-label">File Format</label>
                        <input type="text" class="form-control" id="metadata_format" name="metadata[format]" placeholder="e.g. PDF, MP3, JPG">
                    </div>
                    <div class="mb-3">
                        <label for="metadata_size" class="form-label">File Size</label>
                        <input type="text" class="form-control" id="metadata_size" name="metadata[size]" placeholder="e.g. 10MB, 1GB">
                    </div>
                `;
            } else if (selectedType === 'crypto') {
                metadataFields.innerHTML = `
                    <div class="mb-3">
                        <label for="metadata_network" class="form-label">Network</label>
                        <input type="text" class="form-control" id="metadata_network" name="metadata[network]" placeholder="e.g. Ethereum, Bitcoin, Solana">
                    </div>
                    <div class="mb-3">
                        <label for="metadata_wallet_type" class="form-label">Wallet Type</label>
                        <input type="text" class="form-control" id="metadata_wallet_type" name="metadata[wallet_type]" placeholder="e.g. MetaMask, Ledger">
                    </div>
                `;
            } else if (selectedType === 'service') {
                metadataFields.innerHTML = `
                    <div class="mb-3">
                        <label for="metadata_duration" class="form-label">Duration</label>
                        <input type="text" class="form-control" id="metadata_duration" name="metadata[duration]" placeholder="e.g. 2 hours, 1 week">
                    </div>
                    <div class="mb-3">
                        <label for="metadata_delivery" class="form-label">Delivery Method</label>
                        <input type="text" class="form-control" id="metadata_delivery" name="metadata[delivery]" placeholder="e.g. Remote, In-person">
                    </div>
                `;
            }
        });
        
        // Trigger change event if type is pre-selected
        if (typeSelect.value) {
            typeSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection
@endsection