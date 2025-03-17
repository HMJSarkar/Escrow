<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Exchange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display a listing of the user's transactions.
     */
    public function index()
    {
        $transactions = Transaction::where('user_id', Auth::id())
            ->with('exchange')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('transactions.index', [
            'transactions' => $transactions,
        ]);
    }

    /**
     * Display the specified transaction.
     */
    public function show(Transaction $transaction)
    {
        // Check if user owns this transaction or is admin
        if ($transaction->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('transactions.show', [
            'transaction' => $transaction->load('exchange'),
        ]);
    }

    /**
     * Process a payment for a transaction.
     */
    public function processPayment(Request $request, Transaction $transaction)
    {
        // Check if user owns this transaction
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Check if transaction is still pending
        if ($transaction->status !== 'pending') {
            return redirect()->route('transactions.show', $transaction)
                ->with('error', 'This transaction is no longer pending.');
        }
        
        $request->validate([
            'payment_method' => 'required|in:stripe,crypto,bank_transfer',
            'payment_details' => 'required|array',
        ]);
        
        // Update transaction with payment method
        $transaction->payment_method = $request->payment_method;
        $transaction->save();
        
        try {
            // Process the payment using the appropriate method
            $success = $transaction->processPayment($request->payment_details);
            
            if ($success) {
                return redirect()->route('transactions.show', $transaction)
                    ->with('success', 'Payment processed successfully.');
            } else {
                return redirect()->route('transactions.show', $transaction)
                    ->with('error', 'Payment processing failed.');
            }
        } catch (\Exception $e) {
            return redirect()->route('transactions.show', $transaction)
                ->with('error', 'Payment processing error: ' . $e->getMessage());
        }
    }

    /**
     * Admin approval for transactions.
     */
    public function adminApprove(Transaction $transaction)
    {
        // Check if user is admin
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        
        // Check if transaction is still pending
        if ($transaction->status !== 'pending') {
            return redirect()->route('admin.transactions.index')
                ->with('error', 'This transaction is no longer pending.');
        }
        
        DB::beginTransaction();
        
        try {
            // Update transaction status
            $transaction->status = 'completed';
            $transaction->save();
            
            // If this is a deposit transaction for an exchange, update the exchange status
            if ($transaction->type === 'deposit' && $transaction->exchange_id) {
                $exchange = $transaction->exchange;
                
                // Determine if this is the initiator or responder depositing
                $isInitiator = ($transaction->user_id === $exchange->initiator_id);
                
                if ($isInitiator && $exchange->escrow_status === 'waiting_initiator') {
                    $exchange->escrow_status = 'waiting_responder';
                } elseif (!$isInitiator && $exchange->escrow_status === 'waiting_responder') {
                    $exchange->escrow_status = 'both_deposited';
                }
                
                $exchange->save();
            }
            
            DB::commit();
            
            return redirect()->route('admin.transactions.index')
                ->with('success', 'Transaction approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('admin.transactions.index')
                ->with('error', 'Failed to approve transaction: ' . $e->getMessage());
        }
    }

    /**
     * Admin rejection for transactions.
     */
    public function adminReject(Request $request, Transaction $transaction)
    {
        // Check if user is admin
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        
        // Check if transaction is still pending
        if ($transaction->status !== 'pending') {
            return redirect()->route('admin.transactions.index')
                ->with('error', 'This transaction is no longer pending.');
        }
        
        $request->validate([
            'notes' => 'required|string',
        ]);
        
        // Update transaction status
        $transaction->status = 'failed';
        $transaction->notes = $request->notes;
        $transaction->save();
        
        return redirect()->route('admin.transactions.index')
            ->with('success', 'Transaction rejected successfully.');
    }
}