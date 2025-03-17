@extends('layouts.app')

@section('title', 'My Exchanges')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>My Exchanges</h1>
            <p class="text-muted">View and manage your asset exchanges</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('exchanges.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> New Exchange
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
            <h5 class="mb-0">Your Exchanges</h5>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-secondary btn-sm active">All</button>
                <button type="button" class="btn btn-outline-secondary btn-sm">Pending</button>
                <button type="button" class="btn btn-outline-secondary btn-sm">Active</button>
                <button type="button" class="btn btn-outline-secondary btn-sm">Completed</button>
            </div>
        </div>
        <div class="card-body p-0">
            @if($exchanges->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-arrow-left-right text-muted" style="font-size: 3rem;"></i>
                <p class="mt-3 mb-0">You don't have any exchanges yet.</p>
                <p class="text-muted">Create a new exchange to get started.</p>
                <a href="{{ route('exchanges.create') }}" class="btn btn-primary mt-2">
                    <i class="bi bi-plus-circle"></i> New Exchange
                </a>
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Offered Asset</th>
                            <th scope="col">Requested Asset</th>
                            <th scope="col">Status</th>
                            <th scope="col">Escrow Status</th>
                            <th scope="col">Date</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($exchanges as $exchange)
                        <tr>
                            <td>#{{ $exchange->id }}</td>
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
                            <td>
                                @if($exchange->escrow_status == 'waiting_initiator')
                                <span class="badge bg-warning">Waiting for Initiator</span>
                                @elseif($exchange->escrow_status == 'waiting_responder')
                                <span class="badge bg-warning">Waiting for Responder</span>
                                @elseif($exchange->escrow_status == 'both_deposited')
                                <span class="badge bg-primary">Both Deposited</span>
                                @elseif($exchange->escrow_status == 'released')
                                <span class="badge bg-success">Released</span>
                                @elseif($exchange->escrow_status == 'refunded')
                                <span class="badge bg-info">Refunded</span>
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
            <div class="p-3">
                {{ $exchanges->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection