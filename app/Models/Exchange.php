<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exchange extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'initiator_id',
        'responder_id',
        'offered_asset_id',
        'requested_asset_id',
        'status', // pending, accepted, rejected, completed, cancelled
        'escrow_status', // waiting_initiator, waiting_responder, both_deposited, released, refunded
        'fee_amount',
        'fee_currency',
        'fee_paid',
        'notes',
        'completion_date',
        'cancellation_reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'completion_date' => 'datetime',
        'fee_paid' => 'boolean',
    ];

    /**
     * Get the initiator of the exchange.
     */
    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiator_id');
    }

    /**
     * Get the responder of the exchange.
     */
    public function responder()
    {
        return $this->belongsTo(User::class, 'responder_id');
    }

    /**
     * Get the offered asset.
     */
    public function offeredAsset()
    {
        return $this->belongsTo(Asset::class, 'offered_asset_id');
    }

    /**
     * Get the requested asset.
     */
    public function requestedAsset()
    {
        return $this->belongsTo(Asset::class, 'requested_asset_id');
    }

    /**
     * Get the transactions associated with this exchange.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Check if both parties have deposited their assets.
     */
    public function bothAssetsDeposited()
    {
        return $this->escrow_status === 'both_deposited';
    }

    /**
     * Check if exchange is completed.
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Calculate fee based on asset types.
     */
    public function calculateFee()
    {
        // Get the monetary fee percentage from config
        $monetaryFeePercentage = config('escrow.fee_monetary', 10);
        
        // Get the default fee percentage for non-monetary exchanges
        $defaultFeePercentage = config('escrow.fee_default', 5);
        
        // Check if this is a monetary transaction
        if ($this->offeredAsset->type === 'money' || $this->offeredAsset->type === 'crypto' ||
            $this->requestedAsset->type === 'money' || $this->requestedAsset->type === 'crypto') {
            
            // For monetary transactions, calculate 10% of the value
            if ($this->offeredAsset->type === 'money' || $this->offeredAsset->type === 'crypto') {
                $feeAmount = $this->offeredAsset->value * ($monetaryFeePercentage / 100);
                $feeCurrency = $this->offeredAsset->currency;
            } else {
                $feeAmount = $this->requestedAsset->value * ($monetaryFeePercentage / 100);
                $feeCurrency = $this->requestedAsset->currency;
            }
        } else {
            // For non-monetary exchanges, use the default fee percentage
            // We'll use the offered asset's value for calculation
            $feeAmount = $this->offeredAsset->value * ($defaultFeePercentage / 100);
            $feeCurrency = $this->offeredAsset->currency;
        }
        
        return [
            'amount' => $feeAmount,
            'currency' => $feeCurrency
        ];
    }
}