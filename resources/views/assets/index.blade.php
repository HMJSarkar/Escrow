@extends('layouts.app')

@section('title', 'My Assets')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>My Assets</h1>
            <p class="text-muted">View and manage your assets</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('assets.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add New Asset
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Your Assets</h5>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-secondary btn-sm active">All</button>
                <button type="button" class="btn btn-outline-secondary btn-sm">Available</button>
                <button type="button" class="btn btn-outline-secondary btn-sm">Pending</button>
                <button type="button" class="btn btn-outline-secondary btn-sm">Exchanged</button>
            </div>
        </div>
        <div class="card-body p-0">
            @if($assets->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-box text-muted" style="font-size: 3rem;"></i>
                <p class="mt-3 mb-0">You don't have any assets yet.</p>
                <p class="text-muted">Add a new asset to get started.</p>
                <a href="{{ route('assets.create') }}" class="btn btn-primary mt-2">
                    <i class="bi bi-plus-circle"></i> Add New Asset
                </a>
            </div>
            @else
            <div class="row p-3">
                @foreach($assets as $asset)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="position-relative">
                            @if($asset->images && count($asset->images) > 0)
                            <img src="{{ $asset->images[0] }}" class="card-img-top" alt="{{ $asset->title }}" style="height: 180px; object-fit: cover;">
                            @else
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                                <i class="bi bi-box text-muted" style="font-size: 3rem;"></i>
                            </div>
                            @endif
                            <div class="position-absolute top-0 end-0 p-2">
                                @if($asset->approval_status == 'pending')
                                <span class="badge bg-warning">Pending Approval</span>
                                @elseif($asset->approval_status == 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                                @endif
                                
                                @if($asset->status == 'exchanged')
                                <span class="badge bg-info">Exchanged</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $asset->title }}</h5>
                            <p class="card-text text-muted small">{{ Str::limit($asset->description, 100) }}</p>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-primary">{{ ucfirst($asset->type) }}</span>
                                <span class="fw-bold">{{ $asset->value }} {{ $asset->currency }}</span>
                            </div>
                            @if($asset->condition)
                            <div class="small mb-2">
                                <span class="text-muted">Condition:</span> {{ ucfirst($asset->condition) }}
                            </div>
                            @endif
                            @if($asset->location)
                            <div class="small mb-2">
                                <span class="text-muted">Location:</span> {{ $asset->location }}
                            </div>
                            @endif
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('assets.show', $asset) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <div>
                                    <a href="{{ route('assets.edit', $asset) }}" class="btn btn-sm btn-outline-secondary me-1">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAssetModal{{ $asset->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Delete Modal for each asset -->
                <div class="modal fade" id="deleteAssetModal{{ $asset->id }}" tabindex="-1" aria-labelledby="deleteAssetModalLabel{{ $asset->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteAssetModalLabel{{ $asset->id }}">Confirm Delete</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to delete the asset "{{ $asset->title }}"? This action cannot be undone.
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <form action="{{ route('assets.destroy', $asset) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="p-3">
                {{ $assets->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection