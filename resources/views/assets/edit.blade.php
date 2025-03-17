@extends('layouts.app')

@section('title', 'Edit Asset')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Edit Asset</h1>
            <p class="text-muted">Update your asset details</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('assets.show', $asset) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Asset
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
            <form action="{{ route('assets.update', $asset) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="title" class="form-label">Asset Title</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $asset->title) }}" required>
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="type" class="form-label">Asset Type</label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Select asset type</option>
                                <option value="physical" {{ old('type', $asset->type) == 'physical' ? 'selected' : '' }}>Physical Item</option>
                                <option value="digital" {{ old('type', $asset->type) == 'digital' ? 'selected' : '' }}>Digital Item</option>
                                <option value="service" {{ old('type', $asset->type) == 'service' ? 'selected' : '' }}>Service</option>
                                <option value="crypto" {{ old('type', $asset->type) == 'crypto' ? 'selected' : '' }}>Cryptocurrency</option>
                                <option value="money" {{ old('type', $asset->type) == 'money' ? 'selected' : '' }}>Money/Currency</option>
                                <option value="other" {{ old('type', $asset->type) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="value" class="form-label">Value</label>
                                    <input type="number" step="0.01" class="form-control @error('value') is-invalid @enderror" id="value" name="value" value="{{ old('value', $asset->value) }}" required>
                                    @error('value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="currency" class="form-label">Currency</label>
                                    <select class="form-select @error('currency') is-invalid @enderror" id="currency" name="currency" required>
                                        <option value="USD" {{ old('currency', $asset->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                                        <option value="EUR" {{ old('currency', $asset->currency) == 'EUR' ? 'selected' : '' }}>EUR</option>
                                        <option value="GBP" {{ old('currency', $asset->currency) == 'GBP' ? 'selected' : '' }}>GBP</option>
                                        <option value="BTC" {{ old('currency', $asset->currency) == 'BTC' ? 'selected' : '' }}>BTC</option>
                                        <option value="ETH" {{ old('currency', $asset->currency) == 'ETH' ? 'selected' : '' }}>ETH</option>
                                        <option value="Other" {{ old('currency', $asset->currency) == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('currency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3 physical-field {{ $asset->type != 'physical' ? 'd-none' : '' }}">
                            <label for="condition" class="form-label">Condition</label>
                            <select class="form-select @error('condition') is-invalid @enderror" id="condition" name="condition">
                                <option value="">Select condition</option>
                                <option value="new" {{ old('condition', $asset->condition) == 'new' ? 'selected' : '' }}>New</option>
                                <option value="like_new" {{ old('condition', $asset->condition) == 'like_new' ? 'selected' : '' }}>Like New</option>
                                <option value="good" {{ old('condition', $asset->condition) == 'good' ? 'selected' : '' }}>Good</option>
                                <option value="fair" {{ old('condition', $asset->condition) == 'fair' ? 'selected' : '' }}>Fair</option>
                                <option value="poor" {{ old('condition', $asset->condition) == 'poor' ? 'selected' : '' }}>Poor</option>
                            </select>
                            @error('condition')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3 physical-field {{ $asset->type != 'physical' ? 'd-none' : '' }}">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location', $asset->location) }}">
                            @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description', $asset->description) }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="images" class="form-label">Images</label>
                            @if($asset->images && count($asset->images) > 0)
                            <div class="mb-2">
                                <div class="row">
                                    @foreach($asset->images as $index => $image)
                                    <div class="col-md-4 mb-2">
                                        <div class="position-relative">
                                            <img src="{{ $image }}" class="img-thumbnail" alt="Asset Image">
                                            <div class="form-check position-absolute" style="top: 5px; right: 5px;">
                                                <input class="form-check-input" type="checkbox" name="remove_images[]" value="{{ $index }}" id="removeImage{{ $index }}">
                                                <label class="form-check-label visually-hidden" for="removeImage{{ $index }}">Remove</label>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="form-text mb-2">Check the images you want to remove.</div>
                            </div>
                            @endif
                            <input type="file" class="form-control @error('images') is-invalid @enderror" id="images" name="images[]" multiple accept="image/*">
                            <div class="form-text">Upload up to 5 images of your asset. Maximum size: 2MB per image.</div>
                            @error('images')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div id="metadata-fields">
                            <!-- Dynamic metadata fields will be added here based on asset type -->
                            @if($asset->metadata && count($asset->metadata) > 0)
                                @foreach($asset->metadata as $key => $value)
                                <div class="mb-3">
                                    <label for="metadata_{{ $key }}" class="form-label">{{ ucfirst(str_replace('_', ' ', $key)) }}</label>
                                    <input type="text" class="form-control" id="metadata_{{ $key }}" name="metadata[{{ $key }}]" value="{{ $value }}">
                                </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-info mt-3">
                    <i class="bi bi-info-circle"></i> Updating your asset may require re-approval by our team if significant changes are made.
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="{{ route('assets.show', $asset) }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Asset</button>
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
        const currentMetadata = @json($asset->metadata ?? []);
        
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
                        <input type="text" class="form-control" id="metadata_format" name="metadata[format]" value="${currentMetadata.format || ''}" placeholder="e.g. PDF, MP3, JPG">
                    </div>
                    <div class="mb-3">
                        <label for="metadata_size" class="form-label">File Size</label>
                        <input type="text" class="form-control" id="metadata_size" name="metadata[size]" value="${currentMetadata.size || ''}" placeholder="e.g. 10MB, 1GB">
                    </div>
                `;
            } else if (selectedType === 'crypto') {
                metadataFields.innerHTML = `
                    <div class="mb-3">
                        <label for="metadata_network" class="form-label">Network</label>
                        <input type="text" class="form-control" id="metadata_network" name="metadata[network]" value="${currentMetadata.network || ''}" placeholder="e.g. Ethereum, Bitcoin, Solana">
                    </div>
                    <div class="mb-3">
                        <label for="metadata_wallet_type" class="form-label">Wallet Type</label>
                        <input type="text" class="form-control" id="metadata_wallet_type" name="metadata[wallet_type]" value="${currentMetadata.wallet_type || ''}" placeholder="e.g. MetaMask, Ledger">
                    </div>
                `;
            } else if (selectedType === 'service') {
                metadataFields.innerHTML = `
                    <div class="mb-3">
                        <label for="metadata_duration" class="form-label">Duration</label>
                        <input type="text" class="form-control" id="metadata_duration" name="metadata[duration]" value="${currentMetadata.duration || ''}" placeholder="e.g. 2 hours, 1 week">
                    </div>
                    <div class="mb-3">
                        <label for="metadata_delivery" class="form-label">Delivery Method</label>
                        <input type="text" class="form-control" id="metadata_delivery" name="metadata[delivery]" value="${currentMetadata.delivery || ''}" placeholder="e.g. Remote, In-person">
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