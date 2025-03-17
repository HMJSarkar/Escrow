@extends('layouts.app')

@section('title', 'Transaction Details')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Transaction Details</h1>
            <p class="text-muted">View details and manage your transaction</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Transactions
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Transaction Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Transaction ID:</div>
                        <div class="col-md-8 fw-bold">#{{ $transaction->id }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Type:</div>
                        <div class="col-md-8">
                            @if($transaction->type == 'deposit')
                                <span class="badge bg-primary">Deposit</span>
                            @elseif($transaction->type == 'withdrawal')
                                <span class="badge bg-success">Withdrawal</span>
                            @elseif($transaction->type == 'fee')
                                <span class="badge bg-warning">Fee</span>
                            @elseif($transaction->type == 'refund')
                                <span class="badge bg-info">Refund</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Amount:</div>
                        <div class="col-md-8">{{ $transaction->amount }} {{ $transaction->currency }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Status:</div>
                        <div class="col-md-8">
                            @if($transaction->status == 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($transaction->status == 'completed')
                                <span class="badge bg-success">Completed</span>
                            @elseif($transaction->status == 'failed')
                                <span class="badge bg-danger">Failed</span>
                            @elseif($transaction->status == 'cancelled')
                                <span class="badge bg-secondary">Cancelled</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Date:</div>
                        <div class="col-md-8">{{ $transaction->created_at->format('M d, Y H:i:s') }}</div>
                    </div>
                    @if($transaction->payment_method)
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Payment Method:</div>
                        <div class="col-md-8">{{ ucfirst($transaction->payment_method) }}</div>
                    </div>
                    @endif
                    @if($transaction->reference)
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Reference:</div>
                        <div class="col-md-8">{{ $transaction->reference }}</div>
                    </div>
                    @endif
                    @if($transaction->notes)
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Notes:</div>
                        <div class="col-md-8">{{ $transaction->notes }}</div>
                    </div>
                    @endif
                </div>
            </div>

            @if($transaction->exchange)
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Related Exchange</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Exchange ID:</div>
                        <div class="col-md-8">
                            <a href="{{ route('exchanges.show', $transaction->exchange) }}">#{{ $transaction->exchange->id }}</a>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Status:</div>
                        <div class="col-md-8">
                            @if($transaction->exchange->status == 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($transaction->exchange->status == 'accepted')
                                <span class="badge bg-primary">Accepted</span>
                            @elseif($transaction->exchange->status == 'completed')
                                <span class="badge bg-success">Completed</span>
                            @elseif($transaction->exchange->status == 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @elseif($transaction->exchange->status == 'cancelled')
                                <span class="badge bg-secondary">Cancelled</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Escrow Status:</div>
                        <div class="col-md-8">
                            @if($transaction->exchange->escrow_status == 'waiting_initiator')
                                <span class="badge bg-warning">Waiting for Initiator Deposit</span>
                            @elseif($transaction->exchange->escrow_status == 'waiting_responder')
                                <span class="badge bg-warning">Waiting for Responder Deposit</span>
                            @elseif($transaction->exchange->escrow_status == 'both_deposited')
                                <span class="badge bg-primary">Both Assets Deposited</span>
                            @elseif($transaction->exchange->escrow_status == 'released')
                                <span class="badge bg-success">Assets Released</span>
                            @elseif($transaction->exchange->escrow_status == 'refunded')
                                <span class="badge bg-info">Assets Refunded</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            @if($transaction->status == 'pending')
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Process Payment</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('transactions.payment', $transaction) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <select class="form-select" id="payment_method" name="payment_method" required>
                                <option value="">Select payment method</option>
                                <option value="stripe">Credit Card (Stripe)</option>
                                <option value="crypto">Cryptocurrency</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            </select>
                        </div>

                        <div id="stripe_details" class="payment-details d-none">
                            <div class="mb-3">
                                <label for="card_number" class="form-label">Card Number</label>
                                <input type="text" class="form-control" id="card_number" name="payment_details[card_number]" placeholder="4242 4242 4242 4242">
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="expiry_date" class="form-label">Expiry Date</label>
                                    <input type="text" class="form-control" id="expiry_date" name="payment_details[expiry_date]" placeholder="MM/YY">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="cvc" class="form-label">CVC</label>
                                    <input type="text" class="form-control" id="cvc" name="payment_details[cvc]" placeholder="123">
                                </div>
                            </div>
                        </div>

                        <div id="crypto_details" class="payment-details d-none">
                            <div class="mb-3">
                                <label for="wallet_address" class="form-label">Wallet Address</label>
                                <input type="text" class="form-control" id="wallet_address" name="payment_details[wallet_address]" placeholder="Your wallet address">
                            </div>
                            <div class="mb-3">
                                <label for="transaction_hash" class="form-label">Transaction Hash</label>
                                <input type="text" class="form-control" id="transaction_hash" name="payment_details[transaction_hash]" placeholder="Transaction hash">
                            </div>
                        </div>

                        <div id="bank_details" class="payment-details d-none">
                            <div class="mb-3">
                                <label for="bank_name" class="form-label">Bank Name</label>
                                <input type="text" class="form-control" id="bank_name" name="payment_details[bank_name]">
                            </div>
                            <div class="mb-3">
                                <label for="account_number" class="form-label">Account Number</label>
                                <input type="text" class="form-control" id="account_number" name="payment_details[account_number]">
                            </div>
                            <div class="mb-3">
                                <label for="reference_number" class="form-label">Reference Number</label>
                                <input type="text" class="form-control" id="reference_number" name="payment_details[reference_number]">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Process Payment</button>
                    </form>
                </div>
            </div>
            @endif

            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Help & Support</h5>
                </div>
                <div class="card-body">
                    <p>Having issues with this transaction?</p>
                    <a href="#" class="btn btn-outline-secondary w-100 mb-2">
                        <i class="bi bi-question-circle"></i> Get Help
                    </a>
                    <a href="#" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-chat-dots"></i> Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethodSelect = document.getElementById('payment_method');
        const paymentDetails = document.querySelectorAll('.payment-details');
        
        if (paymentMethodSelect) {
            paymentMethodSelect.addEventListener('change', function() {
                // Hide all payment details sections
                paymentDetails.forEach(section => {
                    section.classList.add('d-none');
                });
                
                // Show the selected payment method details
                const selectedMethod = this.value;
                if (selectedMethod) {
                    document.getElementById(selectedMethod + '_details').classList.remove('d-none');
                }
            });
        }
    });
</script>
@endsection
@endsection