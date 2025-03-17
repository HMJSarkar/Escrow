@extends('layouts.app')

@section('title', 'My Transactions')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>My Transactions</h1>
            <p class="text-muted">View and manage your transaction history</p>
        </div>
    </div>

    @if($transactions->isEmpty())
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i> You don't have any transactions yet.
        </div>
    @else
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
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
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $transactions->links() }}
        </div>
    @endif
</div>
@endsection