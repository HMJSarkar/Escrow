<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Exchange;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExchangeController extends Controller
{
    /**
     * Display a listing of the exchanges.
     */
    public function index()
    {        
        // Get exchanges where the user is either initiator or responder
        $exchanges = Exchange::where('initiator_id', Auth::id())
            ->orWhere('responder_id', Auth::id())
            ->with(['initiator', 'responder', 'offeredAsset', 'requestedAsset'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('exchanges.index', [
            'exchanges' => $exchanges,
        ]);
    }

    /**
     * Show the form for creating a new exchange.
     */
    public function create(Request $request)
    {        
        // Get the asset to be offered if provided
        $offeredAsset = null;
        if ($request->has('asset_id')) {
            $offeredAsset = Asset::where('id', $request->asset_id)
                ->where('user_id', Auth::id())
                ->where('approval_status', 'approved')
                ->where('status', 'approved')
                ->first();
                
            if (!$offeredAsset) {
                return redirect()->route('assets.index')
                    ->with('error', 'Invalid asset selected for exchange.');
            }
        }
        
        // Get all available assets for requesting (excluding user's own assets)
        $availableAssets = Asset::where('user_id', '!=', Auth::id())
            ->where('approval_status', 'approved')
            ->where('status', 'approved')
            ->with('user')
            ->get();
        
        // Get user's own assets for offering
        $userAssets = Asset::where('user_id', Auth::id())
            ->where('approval_status', 'approved')
            ->where('status', 'approved')
            ->get();
        
        return view('exchanges.create', [
            'offeredAsset' => $offeredAsset,
            'userAssets' => $userAssets,
            'availableAssets' => $availableAssets,
        ]);
    }

    /**
     * Store a newly created exchange in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'offered_asset_id' => 'required|exists:assets,id',
            'requested_asset_id' => 'required|exists:assets,id|different:offered_asset_id',
            'notes' => 'nullable|string',
        ]);
        
        // Verify the offered asset belongs to the user
        $offeredAsset = Asset::where('id', $request->offered_asset_id)
            ->where('user_id', Auth::id())
            ->where('approval_status', 'approved')
            ->where('status', 'approved')
            ->first();
            
        if (!$offeredAsset) {
            return redirect()->route('exchanges.create')
                ->with('error', 'Invalid asset selected for offering.');
        }
        
        // Verify the requested asset is available
        $requestedAsset = Asset::where('id', $request->requested_asset_id)
            ->where('user_id', '!=', Auth::id())
            ->where('approval_status', 'approved')
            ->where('status', 'approved')
            ->first();
            
        if (!$requestedAsset) {
            return redirect()->route('exchanges.create')
                ->with('error', 'Invalid asset selected for requesting.');
        }
        
        // Create the exchange
        $exchange = new Exchange([
            'initiator_id' => Auth::id(),
            'responder_id' => $requestedAsset->user_id,
            'offered_asset_id' => $offeredAsset->id,
            'requested_asset_id' => $requestedAsset->id,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);
        
        // Calculate fee
        $fee = $exchange->calculateFee();
        $exchange->fee_amount = $fee['amount'];
        $exchange->fee_currency = $fee['currency'];
        
        $exchange->save();
        
        return redirect()->route('exchanges.show', $exchange)
            ->with('success', 'Exchange request created successfully.');
    }

    /**
     * Display the specified exchange.
     */
    public function show(Exchange $exchange)
    {
        // Check if user is part of this exchange
        if ($exchange->initiator_id !== Auth::id() && $exchange->responder_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        
        $exchange->load(['initiator', 'responder', 'offeredAsset', 'requestedAsset', 'transactions']);
        
        return view('exchanges.show', [
            'exchange' => $exchange,
        ]);
    }

    /**
     * Accept an exchange request.
     */
    public function accept(Exchange $exchange)
    {
        // Check if user is the responder
        if ($exchange->responder_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Check if exchange is still pending
        if ($exchange->status !== 'pending') {
            return redirect()->route('exchanges.show', $exchange)
                ->with('error', 'This exchange request is no longer pending.');
        }
        
        // Update exchange status
        $exchange->status = 'accepted';
        $exchange->escrow_status = 'waiting_initiator';
        $exchange->save();
        
        return redirect()->route('exchanges.show', $exchange)
            ->with('success', 'Exchange request accepted. Please wait for the initiator to deposit their asset.');
    }

    /**
     * Reject an exchange request.
     */
    public function reject(Request $request, Exchange $exchange)
    {
        // Check if user is the responder
        if ($exchange->responder_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Check if exchange is still pending
        if ($exchange->status !== 'pending') {
            return redirect()->route('exchanges.show', $exchange)
                ->with('error', 'This exchange request is no longer pending.');
        }
        
        $request->validate([
            'cancellation_reason' => 'nullable|string',
        ]);
        
        // Update exchange status
        $exchange->status = 'rejected';
        $exchange->cancellation_reason = $request->cancellation_reason;
        $exchange->save();
        
        return redirect()->route('exchanges.index')
            ->with('success', 'Exchange request rejected successfully.');
    }

    /**
     * Cancel an exchange.
     */
    public function cancel(Request $request, Exchange $exchange)
    {
        // Check if user is part of this exchange
        if ($exchange->initiator_id !== Auth::id() && $exchange->responder_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Check if exchange can be cancelled
        if (!in_array($exchange->status, ['pending', 'accepted'])) {
            return redirect()->route('exchanges.show', $exchange)
                ->with('error', 'This exchange cannot be cancelled.');
        }
        
        // If assets are already in escrow, don't allow cancellation
        if ($exchange->escrow_status === 'both_deposited') {
            return redirect()->route('exchanges.show', $exchange)
                ->with('error', 'Cannot cancel exchange after both assets are deposited.');
        }
        
        $request->validate([
            'cancellation_reason' => 'nullable|string',
        ]);
        
        // Update exchange status
        $exchange->status = 'cancelled';
        $exchange->cancellation_reason = $request->cancellation_reason;
        
        // If any assets are in escrow, refund them
        if (in_array($exchange->escrow_status, ['waiting_responder', 'waiting_initiator'])) {
            $exchange->escrow_status = 'refunded';
            
            // Process refunds if needed
            // This would involve creating refund transactions
        }
        
        $exchange->save();
        
        return redirect()->route('exchanges.index')
            ->with('success', 'Exchange cancelled successfully.');
    }

    /**
     * Deposit asset to escrow.
     */
    public function deposit(Exchange $exchange)
    {
        // Check if user is part of this exchange
        if ($exchange->initiator_id !== Auth::id() && $exchange->responder_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Check if exchange is accepted
        if ($exchange->status !== 'accepted') {
            return redirect()->route('exchanges.show', $exchange)
                ->with('error', 'This exchange is not in accepted status.');
        }
        
        // Determine which user is depositing
        $isInitiator = ($exchange->initiator_id === Auth::id());
        
        // Check if it's the user's turn to deposit
        if ($isInitiator && $exchange->escrow_status !== 'waiting_initiator') {
            return redirect()->route('exchanges.show', $exchange)
                ->with('error', 'It is not your turn to deposit.');
        }
        
        if (!$isInitiator && $exchange->escrow_status !== 'waiting_responder') {
            return redirect()->route('exchanges.show', $exchange)
                ->with('error', 'It is not your turn to deposit.');
        }
        
        // Process the deposit
        DB::beginTransaction();
        
        try {
            // Create a deposit transaction
            $transaction = new Transaction([
                'user_id' => Auth::id(),
                'exchange_id' => $exchange->id,
                'type' => 'deposit',
                'amount' => $isInitiator ? $exchange->offeredAsset->value : $exchange->requestedAsset->value,
                'currency' => $isInitiator ? $exchange->offeredAsset->currency : $exchange->requestedAsset->currency,
                'status' => 'completed',
                'payment_method' => $isInitiator ? $exchange->offeredAsset->type : $exchange->requestedAsset->type,
                'notes' => 'Asset deposited to escrow',
            ]);
            
            $transaction->save();
            
            // Update the exchange escrow status
            if ($isInitiator) {
                $exchange->escrow_status = 'waiting_responder';
                
                // If this is a monetary transaction, also collect the fee
                if ($exchange->offeredAsset->type === 'money' || $exchange->offeredAsset->type === 'crypto') {
                    $feeTransaction = new Transaction([
                        'user_id' => Auth::id(),
                        'exchange_id' => $exchange->id,
                        'type' => 'fee',
                        'amount' => $exchange->fee_amount,
                        'currency' => $exchange->fee_currency,
                        'status' => 'completed',
                        'payment_method' => $exchange->offeredAsset->type,
                        'notes' => 'Exchange fee',
                    ]);
                    
                    $feeTransaction->save();
                    $exchange->fee_paid = true;
                }
            } else {
                $exchange->escrow_status = 'both_deposited';
            }
            
            $exchange->save();
            
            DB::commit();
            
            return redirect()->route('exchanges.show', $exchange)
                ->with('success', 'Asset deposited to escrow successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('exchanges.show', $exchange)
                ->with('error', 'Failed to deposit asset: ' . $e->getMessage());
        }
    }

    /**
     * Complete the exchange and release assets.
     */
    public function complete(Exchange $exchange)
    {
        // Check if user is part of this exchange or admin
        if ($exchange->initiator_id !== Auth::id() && $exchange->responder_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        
        // Check if both assets are deposited
        if ($exchange->escrow_status !== 'both_deposited') {
            return redirect()->route('exchanges.show', $exchange)
                ->with('error', 'Both assets must be deposited before completing the exchange.');
        }
        
        // Process the exchange completion
        DB::beginTransaction();
        
        try {
            // Update the assets status
            $exchange->offeredAsset->status = 'exchanged';
            $exchange->offeredAsset->save();
            
            $exchange->requestedAsset->status = 'exchanged';
            $exchange->requestedAsset->save();
            
            // Create withdrawal transactions for both parties
            $initiatorWithdrawal = new Transaction([
                'user_id' => $exchange->initiator_id,
                'exchange_id' => $exchange->id,
                'type' => 'withdrawal',
                'amount' => $exchange->requestedAsset->value,
                'currency' => $exchange->requestedAsset->currency,
                'status' => 'completed',
                'payment_method' => $exchange->requestedAsset->type,
                'notes' => 'Asset received from exchange',
            ]);
            
            $responderWithdrawal = new Transaction([
                'user_id' => $exchange->responder_id,
                'exchange_id' => $exchange->id,
                'type' => 'withdrawal',
                'amount' => $exchange->offeredAsset->value,
                'currency' => $exchange->offeredAsset->currency,
                'status' => 'completed',
                'payment_method' => $exchange->offeredAsset->type,
                'notes' => 'Asset received from exchange',
            ]);
            
            $initiatorWithdrawal->save();
            $responderWithdrawal->save();
            
            // Update the exchange status
            $exchange->status = 'completed';
            $exchange->escrow_status = 'released';
            $exchange->completion_date = now();
            $exchange->save();
            
            DB::commit();
            
            return redirect()->route('exchanges.show', $exchange)
                ->with('success', 'Exchange completed successfully. Assets have been transferred to both parties.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('exchanges.show', $exchange)
                ->with('error', 'Failed to complete exchange: ' . $e->getMessage());
        }
    }

    /**
     * Admin approval for exchanges.
     */
    public function adminApprove(Exchange $exchange)
    {
        // Check if user is admin
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        
        // Check if exchange is in a state that can be approved
        if (!in_array($exchange->status, ['pending', 'accepted'])) {
            return redirect()->route('admin.exchanges.index')
                ->with('error', 'This exchange cannot be approved.');
        }
        
        // Update exchange status
        $exchange->status = 'accepted';
        $exchange->escrow_status = 'waiting_initiator';
        $exchange->save();
        
        return redirect()->route('admin.exchanges.index')
            ->with('success', 'Exchange approved successfully.');
    }

    /**
     * Admin rejection for exchanges.
     */
    public function adminReject(Request $request, Exchange $exchange)
    {
        // Check if user is admin
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        
        // Check if exchange is in a state that can be rejected
        if (!in_array($exchange->status, ['pending', 'accepted'])) {
            return redirect()->route('admin.exchanges.index')
                ->with('error', 'This exchange cannot be rejected.');
        }
        
        $request->validate([
            'cancellation_reason' => 'required|string',
        ]);
        
        // Update exchange status
        $exchange->status = 'rejected';
        $exchange->cancellation_reason = $request->cancellation_reason;
        $exchange->save();
        
        return redirect()->route('admin.exchanges.index')
            ->with('success', 'Exchange rejected successfully.');
    }
}