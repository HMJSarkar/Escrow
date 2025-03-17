@extends('layouts.app')

@section('title', 'Exchange Details')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Exchange Details</h1>
            <p class="text-muted">View details and manage your exchange</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('exchanges.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Exchanges
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
                    <h5 class="mb-0">Exchange Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Exchange ID:</div>
                        <div class="col-md-8 fw-bold">#{{ $exchange->id }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Status:</div>
                        <div class="col-md-8">
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
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Escrow Status:</div>
                        <div class="col-md-8">
                            @if($exchange->escrow_status == 'waiting_initiator')
                                <span class="badge bg-warning">Waiting for Initiator Deposit</span>
                            @elseif($exchange->escrow_status == 'waiting_responder')
                                <span class="badge bg-warning">Waiting for Responder Deposit</span>
                            @elseif($exchange->escrow_status == 'both_deposited')
                                <span class="badge bg-primary">Both Assets Deposited</span>
                            @elseif($exchange->escrow_status == 'released')
                                <span class="badge bg-success">Assets Released</span>
                            @elseif($exchange->escrow_status == 'refunded')
                                <span class="badge bg-info">Assets Refunded</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Date Created:</div>
                        <div class="col-md-8">{{ $exchange->created_at->format('M d, Y H:i:s') }}</div>
                    </div>
                    @if($exchange->completion_date)
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Date Completed:</div>
                        <div class="col-md-8">{{ $exchange->completion_date->format('M d, Y H:i:s') }}</div>
                    </div>
                    @endif
                    @if($exchange->notes)
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Notes:</div>
                        <div class="col-md-8">{{ $exchange->notes }}</div>
                    </div>
                    @endif
                    @if($exchange->cancellation_reason)
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Cancellation Reason:</div>
                        <div class="col-md-8">{{ $exchange->cancellation_reason }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Offered Asset</h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                @if($exchange->offeredAsset->images && count($exchange->offeredAsset->images) > 0)
                                <img src="{{ $exchange->offeredAsset->images[0] }}" alt="Asset" class="img-fluid rounded" style="max-height: 150px;">
                                @else
                                <div class="bg-light d-flex align-items-center justify-content-center rounded" style="height: 150px;">
                                    <i class="bi bi-box text-muted" style="font-size: 3rem;"></i>
                                </div>
                                @endif
                            </div>
                            <h5>{{ $exchange->offeredAsset->title }}</h5>
                            <p class="text-muted">{{ Str::limit($exchange->offeredAsset->description, 100) }}</p>
                            <div class="row mb-2">
                                <div class="col-md-4 text-muted">Type:</div>
                                <div class="col-md-8">{{ ucfirst($exchange->offeredAsset->type) }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 text-muted">Value:</div>
                                <div class="col-md-8">{{ $exchange->offeredAsset->value }} {{ $exchange->offeredAsset->currency }}</div>
                            </div>
                            @if($exchange->offeredAsset->condition)
                            <div class="row mb-2">
                                <div class="col-md-4 text-muted">Condition:</div>
                                <div class="col-md-8">{{ ucfirst($exchange->offeredAsset->condition) }}</div>
                            </div>
                            @endif
                            @if($exchange->offeredAsset->location)
                            <div class="row mb-2">
                                <div class="col-md-4 text-muted">Location:</div>
                                <div class="col-md-8">{{ $exchange->offeredAsset->location }}</div>
                            </div>
                            @endif
                            <div class="row mb-2">
                                <div class="col-md-4 text-muted">Owner:</div>
                                <div class="col-md-8">{{ $exchange->initiator->name }}</div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('assets.show', $exchange->offeredAsset) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View Asset Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Requested Asset</h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                @if($exchange->requestedAsset->images && count($exchange->requestedAsset->images) > 0)
                                <img src="{{ $exchange->requestedAsset->images[0] }}" alt="Asset" class="img-fluid rounded" style="max-height: 150px;">
                                @else
                                <div class="bg-light d-flex align-items-center justify-content-center rounded" style="height: 150px;">
                                    <i class="bi bi-box text-muted" style="font-size: 3rem;"></i>
                                </div>
                                @endif
                            </div>
                            <h5>{{ $exchange->requestedAsset->title }}</h5>
                            <p class="text-muted">{{ Str::limit($exchange->requestedAsset->description, 100) }}</p>
                            <div class="row mb-2">
                                <div class="col-md-4 text-muted">Type:</div>
                                <div class="col-md-8">{{ ucfirst($exchange->requestedAsset->type) }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 text-muted">Value:</div>
                                <div class="col-md-8">{{ $exchange->requestedAsset->value }} {{ $exchange->requestedAsset->currency }}</div>
                            </div>
                            @if($exchange->requestedAsset->condition)
                            <div class="row mb-2">
                                <div class="col-md-4 text-muted">Condition:</div>
                                <div class="col-md-8">{{ ucfirst($exchange->requestedAsset->condition) }}</div>
                            </div>
                            @endif
                            @if($exchange->requestedAsset->location)
                            <div class="row mb-2">
                                <div class="col-md-4 text-muted">Location:</div>
                                <div class="col-md-8">{{ $exchange->requestedAsset->location }}</div>
                            </div>
                            @endif
                            <div class="row mb-2">
                                <div class="col-md-4 text-muted">Owner:</div>
                                <div class="col-md-8">{{ $exchange->responder ? $exchange->responder->name : 'N/A' }}</div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('assets.show', $exchange->requestedAsset) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View Asset Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($exchange->transactions->isNotEmpty())
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Related Transactions</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($exchange->transactions as $transaction)
                                <tr>
                                    <td>#{{ $transaction->id }}</td>
                                    <td>
                                        @if($transaction->type == 'deposit')
                                            <span class="badge bg-primary">Deposit</span>
                                        @elseif($transaction->type == 'withdrawal')
                                            <span class="badge bg-success">Withdrawal</span>
                                        @elseif($transaction->type == 'fee')
                                            <span class="badge bg-warning">Fee</span>
                                        @elseif($transaction->type == 'refund')
                                            <span class="badge bg-info">Refund</span>
                                        @endif
                                    </td>
                                    <td>{{ $transaction->amount }} {{ $transaction->currency }}</td>
                                    <td>
                                        @if($transaction->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($transaction->status == 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($transaction->status == 'failed')
                                            <span class="badge bg-danger">Failed</span>
                                        @elseif($transaction->status == 'cancelled')
                                            <span class="badge bg-secondary">Cancelled</span>
                                        @endif
                                    </td>
                                    <td>{{ $transaction->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('transactions.show', $transaction) }}" class="btn btn-sm btn-outline-primary">
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
                    <h5 class="mb-0">Exchange Actions</h5>
                </div>
                <div class="card-body">
                    @if($exchange->status == 'pending' && Auth::id() == $exchange->responder_id)
                    <div class="d-grid gap-2 mb-3">
                        <form action="{{ route('exchanges.accept', $exchange) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 mb-2">
                                <i class="bi bi-check-circle"></i> Accept Exchange
                            </button>
                        </form>
                        <form action="{{ route('exchanges.reject', $exchange) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-x-circle"></i> Reject Exchange
                            </button>
                        </form>
                    </div>
                    @endif

                    @if($exchange->status == 'pending' && Auth::id() == $exchange->initiator_id)
                    <div class="d-grid gap-2 mb-3">
                        <form action="{{ route('exchanges.cancel', $exchange) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-secondary w-100">
                                <i class="bi bi-x-circle"></i> Cancel Exchange Request
                            </button>
                        </form>
                    </div>
                    @endif

                    @if($exchange->status == 'accepted')
                        @if($exchange->escrow_status == 'waiting_initiator' && Auth::id() == $exchange->initiator_id)
                        <div class="alert alert-info mb-3">
                            <i class="bi bi-info-circle"></i> You need to deposit your asset to proceed with this exchange.
                        </div>
                        <form action="{{ route('exchanges.deposit', $exchange) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="bi bi-box-arrow-in-down"></i> Deposit My Asset
                            </button>
                        </form>
                        @endif

                        @if($exchange->escrow_status == 'waiting_responder' && Auth::id() == $exchange->responder_id)
                        <div class="alert alert-info mb-3">
                            <i class="bi bi-info-circle"></i> The initiator has deposited their asset. You need to deposit your asset to proceed.
                        </div>
                        <form action="{{ route('exchanges.deposit', $exchange) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="bi bi-box-arrow-in-down"></i> Deposit My Asset
                            </button>
                        </form>
                        @endif

                        @if($exchange->escrow_status == 'both_deposited')
                        <div class="alert alert-success mb-3">
                            <i class="bi bi-check-circle"></i> Both assets have been deposited. The exchange can now be completed.
                        </div>
                        <form action="{{ route('exchanges.complete', $exchange) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 mb-3">
                                <i class="bi bi-check2-all"></i> Complete Exchange
                            </button>
                        </form>
                        @endif

                        @if($exchange->escrow_status == 'released' || $exchange->escrow_status == 'refunded')
                        <div class="alert alert-success mb-3">
                            <i class="bi bi-check-circle"></i> This exchange has been processed successfully.
                        </div>
                        @endif
                    @endif

                    @if($exchange->status == 'completed')
                    <div class="alert alert-success mb-3">
                        <i class="bi bi-check-circle"></i> This exchange has been completed successfully.
                    </div>
                    @endif

                    @if($exchange->status == 'rejected')
                    <div class="alert alert-danger mb-3">
                        <i class="bi bi-x-circle"></i> This exchange has been rejected.
                    </div>
                    @endif

                    @if($exchange->status == 'cancelled')
                    <div class="alert alert-secondary mb-3">
                        <i class="bi bi-x-circle"></i> This exchange has been cancelled.
                    </div>
                    @endif