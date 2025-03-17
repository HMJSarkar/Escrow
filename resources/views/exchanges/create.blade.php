@extends('layouts.app')

@section('title', 'Create Exchange')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Create Exchange</h1>
            <p class="text-muted">Propose an exchange of assets with another user</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('exchanges.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Exchanges
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
            <h5 class="mb-0">Exchange Details</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('exchanges.store') }}" method="POST">
                @csrf
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Your Asset to Offer</h5>
                        <div class="mb-3">
                            <label for="offered_asset_id" class="form-label">Select Asset to Offer</label>
                            <select class="form-select @error('offered_asset_id') is-invalid @enderror" id="offered_asset_id" name="offered_asset_id" required>
                                <option value="">Select an asset to offer</option>
                                @foreach($userAssets as $asset)
                                <option value="{{ $asset->id }}" {{ (old('offered_asset_id') == $asset->id || (isset($offeredAsset) && $offeredAsset->id == $asset->id)) ? 'selected' : '' }}>
                                    {{ $asset->title }} ({{ $asset->value }} {{ $asset->currency }})
                                </option>
                                @endforeach
                            </select>
                            @error('offered_asset_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            @if($userAssets->isEmpty())
                            <div class="mt-2">
                                <a href="{{ route('assets.create') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-plus-circle"></i> Create New Asset
                                </a>
                            </div>
                            @endif
                        </div>
                        
                        <div id="offered-asset-preview" class="card d-none mt-3">
                            <div class="card-body">
                                <div class="text-center mb-3" id="offered-asset-image">
                                    <div class="bg-light d-flex align-items-center justify-content-center rounded" style="height: 120px;">
                                        <i class="bi bi-box text-muted" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                                <h6 id="offered-asset-title">Asset Title</h6>
                                <p class="text-muted small" id="offered-asset-description">Asset description will appear here.</p>
                                <div class="row mb-2">
                                    <div class="col-md-4 text-muted small">Type:</div>
                                    <div class="col-md-8 small" id="offered-asset-type">-</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 text-muted small">Value:</div>
                                    <div class="col-md-8 small" id="offered-asset-value">-</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <h5>Asset You Want</h5>
                        <div class="mb-3">
                            <label for="requested_asset_id" class="form-label">Select Asset You Want</label>
                            <select class="form-select @error('requested_asset_id') is-invalid @enderror" id="requested_asset_id" name="requested_asset_id" required>
                                <option value="">Select an asset you want</option>
                                @foreach($availableAssets as $asset)
                                <option value="{{ $asset->id }}" {{ old('requested_asset_id') == $asset->id ? 'selected' : '' }}>
                                    {{ $asset->title }} ({{ $asset->value }} {{ $asset->currency }}) - Owner: {{ $asset->user->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('requested_asset_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div id="requested-asset-preview" class="card d-none mt-3">
                            <div class="card-body">
                                <div class="text-center mb-3" id="requested-asset-image">
                                    <div class="bg-light d-flex align-items-center justify-content-center rounded" style="height: 120px;">
                                        <i class="bi bi-box text-muted" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                                <h6 id="requested-asset-title">Asset Title</h6>
                                <p class="text-muted small" id="requested-asset-description">Asset description will appear here.</p>
                                <div class="row mb-2">
                                    <div class="col-md-4 text-muted small">Type:</div>
                                    <div class="col-md-8 small" id="requested-asset-type">-</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 text-muted small">Value:</div>
                                    <div class="col-md-8 small" id="requested-asset-value">-</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 text-muted small">Owner:</div>
                                    <div class="col-md-8 small" id="requested-asset-owner">-</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="notes" class="form-label">Additional Notes</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Add any additional information or terms for this exchange">{{ old('notes') }}</textarea>
                    @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> By creating this exchange, you agree to the platform's terms and conditions for asset exchanges. Once the exchange is accepted, both parties will need to deposit their assets into escrow before the exchange can be completed.
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('exchanges.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Exchange</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Asset data for previews
        const userAssets = @json($userAssets);
        const availableAssets = @json($availableAssets);
        
        // Offered asset selection
        const offeredAssetSelect = document.getElementById('offered_asset_id');
        const offeredAssetPreview = document.getElementById('offered-asset-preview');
        
        offeredAssetSelect.addEventListener('change', function() {
            const assetId = this.value;
            if (assetId) {
                const asset = userAssets.find(a => a.id == assetId);
                if (asset) {
                    // Update preview
                    document.getElementById('offered-asset-title').textContent = asset.title;
                    document.getElementById('offered-asset-description').textContent = asset.description;
                    document.getElementById('offered-asset-type').textContent = asset.type.charAt(0).toUpperCase() + asset.type.slice(1);
                    document.getElementById('offered-asset-value').textContent = `${asset.value} ${asset.currency}`;
                    
                    // Update image if available
                    const imageContainer = document.getElementById('offered-asset-image');
                    if (asset.images && asset.images.length > 0) {
                        imageContainer.innerHTML = `<img src="${asset.images[0]}" alt="Asset" class="img-fluid rounded" style="max-height: 120px;">`;
                    } else {
                        imageContainer.innerHTML = `<div class="bg-light d-flex align-items-center justify-content-center rounded" style="height: 120px;">
                            <i class="bi bi-box text-muted" style="font-size: 2rem;"></i>
                        </div>`;
                    }
                    
                    offeredAssetPreview.classList.remove('d-none');
                }
            } else {
                offeredAssetPreview.classList.add('d-none');
            }
        });
        
        // Requested asset selection
        const requestedAssetSelect = document.getElementById('requested_asset_id');
        const requestedAssetPreview = document.getElementById('requested-asset-preview');
        
        requestedAssetSelect.addEventListener('change', function() {
            const assetId = this.value;
            if (assetId) {
                const asset = availableAssets.find(a => a.id == assetId);
                if (asset) {
                    // Update preview
                    document.getElementById('requested-asset-title').textContent = asset.title;
                    document.getElementById('requested-asset-description').textContent = asset.description;
                    document.getElementById('requested-asset-type').textContent = asset.type.charAt(0).toUpperCase() + asset.type.slice(1);
                    document.getElementById('requested-asset-value').textContent = `${asset.value} ${asset.currency}`;
                    document.getElementById('requested-asset-owner').textContent = asset.user.name;
                    
                    // Update image if available
                    const imageContainer = document.getElementById('requested-asset-image');
                    if (asset.images && asset.images.length > 0) {
                        imageContainer.innerHTML = `<img src="${asset.images[0]}" alt="Asset" class="img-fluid rounded" style="max-height: 120px;">`;
                    } else {
                        imageContainer.innerHTML = `<div class="bg-light d-flex align-items-center justify-content-center rounded" style="height: 120px;">
                            <i class="bi bi-box text-muted" style="font-size: 2rem;"></i>
                        </div>`;
                    }
                    
                    requestedAssetPreview.classList.remove('d-none');
                }
            } else {
                requestedAssetPreview.classList.add('d-none');
            }
        });
        
        // Trigger change event if values are pre-selected
        if (offeredAssetSelect.value) {
            offeredAssetSelect.dispatchEvent(new Event('change'));
        }
        
        if (requestedAssetSelect.value) {
            requestedAssetSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection
@endsection