@extends('layouts.app')

@section('title', 'Asset Details')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Asset Details</h1>
            <p class="text-muted">View details of your asset</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Assets
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

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Asset Information</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        @if($asset->images && count($asset->images) > 0)
                        <div id="assetImageCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                @foreach($asset->images as $index => $image)
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                    <img src="{{ $image }}" class="d-block mx-auto img-fluid rounded" alt="Asset Image" style="max-height: 300px;">
                                </div>
                                @endforeach
                            </div>
                            @if(count($asset->images) > 1)
                            <button class="carousel-control-prev" type="button" data-bs-target="#assetImageCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#assetImageCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                            @endif
                        </div>
                        @else
                        <div class="bg-light d-flex align-items-center justify-content-center rounded" style="height: 300px;">
                            <i class="bi bi-box text-muted" style="font-size: 5rem;"></i>
                        </div>
                        @endif
                    </div>

                    <h3 class="mb-3">{{ $asset->title }}</h3>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge bg-primary">{{ ucfirst($asset->type) }}</span>
                        <span class="fw-bold fs-4">{{ $asset->value }} {{ $asset->currency }}</span>
                    </div>
                    
                    <div class="mb-4">
                        <h5>Description</h5>
                        <p>{{ $asset->description }}</p>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Status:</div>
                        <div class="col-md-8">
                            @if($asset->status == 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($asset->status == 'approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif($asset->status == 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @elseif($asset->status == 'exchanged')
                                <span class="badge bg-info">Exchanged</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Approval Status:</div>
                        <div class="col-md-8">
                            @if($asset->approval_status == 'pending')
                                <span class="badge bg-warning">Pending Approval</span>
                            @elseif($asset->approval_status == 'approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif($asset->approval_status == 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                                @if($asset->approval_notes)
                                <div class="mt-2 small text-danger">{{ $asset->approval_notes }}</div>
                                @endif
                            @endif
                        </div>
                    </div>
                    
                    @if($asset->condition)
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Condition:</div>
                        <div class="col-md-8">{{ ucfirst($asset->condition) }}</div>
                    </div>
                    @endif
                    
                    @if($asset->location)
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Location:</div>
                        <div class="col-md-8">{{ $asset->location }}</div>
                    </div>
                    @endif
                    
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Date Added:</div>
                        <div class="col-md-8">{{ $asset->created_at->format('M d, Y H:i:s') }}</div>
                    </div>
                    
                    @if($asset->metadata && count($asset->metadata) > 0)
                    <div class="mb-4">
                        <h5>Additional Details</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <tbody>
                                    @foreach($asset->metadata as $key => $value)
                                    <tr>
                                        <th class="bg-light">{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                                        <td>{{ is_array($value) ? implode(', ', $value) : $value }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            @if($asset->offeredInExchanges->isNotEmpty() || $asset->requestedInExchanges->isNotEmpty())
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Related Exchanges</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Other Asset</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($asset->offeredInExchanges as $exchange)
                                <tr>
                                    <td>#{{ $exchange->id }}</td>
                                    <td><span class="badge bg-primary">Offered</span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($exchange->requestedAsset->images && count($exchange->requestedAsset->images) > 0)
                                            <img src="{{ $exchange->requestedAsset->images[0] }}" alt="Asset" class="me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                            <div class="me-2 bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="bi bi-box text-muted"></i>
                                            </div>
                                            @endif
                                            <div>
                                                <div class="fw-medium">{{ Str::limit($exchange->requestedAsset->title, 20) }}</div>
                                                <small class="text-muted">{{ $exchange->requestedAsset->value }} {{ $exchange->requestedAsset->currency }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($exchange->status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                        @elseif($exchange->status == 'accepted')
                                        <span class="badge bg-primary">Accepted</span>
                                        @elseif($exchange->status == 'completed')
                                        <span class="badge bg-success">Completed</span>
                                        @elseif($exchange->status == 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                        @elseif($exchange->status == 'cancelled')
                                        <span class="badge bg-secondary">Cancelled</span>
                                        @endif
                                    </td>
                                    <td>{{ $exchange->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('exchanges.show', $exchange) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                                
                                @foreach($asset->requestedInExchanges as $exchange)
                                <tr>
                                    <td>#{{ $exchange->id }}</td>
                                    <td><span class="badge bg-info">Requested</span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($exchange->offeredAsset->images && count($exchange->offeredAsset->images) > 0)
                                            <img src="{{ $exchange->offeredAsset->images[0] }}" alt="Asset" class="me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                            <div class="me-2 bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="bi bi-box text-muted"></i>
                                            </div>
                                            @endif
                                            <div>
                                                <div class="fw-medium">{{ Str::limit($exchange->offeredAsset->title, 20) }}</div>
                                                <small class="text-muted">{{ $exchange->offeredAsset->value }} {{ $exchange->offeredAsset->currency }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($exchange->status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                        @elseif($exchange->status == 'accepted')
                                        <span class="badge bg-primary">Accepted</span>
                                        @elseif($exchange->status == 'completed')
                                        <span class="badge bg-success">Completed</span>
                                        @elseif($exchange->status == 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                        @elseif($exchange->status == 'cancelled')
                                        <span class="badge bg-secondary">Cancelled</span>
                                        @endif
                                    </td>
                                    <td>{{ $exchange->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('exchanges.show', $exchange) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Asset Actions</h5>
                </div>
                <div class="card-body">
                    @if($asset->status != 'exchanged' && $asset->approval_status == 'approved')
                    <a href="{{ route('exchanges.create', ['asset_id' => $asset->id]) }}" class="btn btn-primary w-100 mb-3">
                        <i class="bi bi-arrow-left-right"></i> Offer for Exchange
                    </a>
                    @endif
                    
                    @if($asset->user_id == Auth::id())
                    <div class="d-grid gap-2">
                        <a href="{{ route('assets.edit', $asset) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-pencil"></i> Edit Asset
                        </a>
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAssetModal">
                            <i class="bi bi-trash"></i> Delete Asset
                        </button>
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Owner Information</h5>