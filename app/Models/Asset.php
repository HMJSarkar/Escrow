<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'type', // money, crypto, physical, service, etc.
        'value',
        'currency',
        'condition', // for physical items
        'location', // for physical items
        'images',
        'status', // pending, approved, rejected, exchanged
        'approval_status', // pending, approved, rejected
        'approval_notes',
        'metadata', // additional data based on asset type
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'images' => 'array',
        'metadata' => 'array',
    ];
    
    /**
     * Get the images attribute with proper storage URLs.
     *
     * @param  mixed  $value
     * @return array
     */
    public function getImagesAttribute($value)
    {
        if (!$value) {
            return null;
        }
        
        $images = json_decode($value, true);
        
        return array_map(function ($path) {
            return asset('storage/' . $path);
        }, $images);
    }

    /**
     * Get the user that owns the asset.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the exchanges where this asset is offered.
     */
    public function offeredInExchanges()
    {
        return $this->hasMany(Exchange::class, 'offered_asset_id');
    }

    /**
     * Get the exchanges where this asset is requested.
     */
    public function requestedInExchanges()
    {
        return $this->hasMany(Exchange::class, 'requested_asset_id');
    }

    /**
     * Get the category that the asset belongs to.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Check if asset is approved.
     */
    public function isApproved()
    {
        return $this->approval_status === 'approved';
    }

    /**
     * Check if asset is available for exchange.
     */
    public function isAvailable()
    {
        return $this->status === 'approved' && $this->approval_status === 'approved';
    }
}