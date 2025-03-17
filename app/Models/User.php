<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'kyc_status',
        'kyc_documents',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'kyc_documents' => 'array',
    ];

    /**
     * Get the assets for the user.
     */
    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    /**
     * Get the transactions for the user.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the exchanges where the user is the initiator.
     */
    public function initiatedExchanges()
    {
        return $this->hasMany(Exchange::class, 'initiator_id');
    }

    /**
     * Get the exchanges where the user is the responder.
     */
    public function respondedExchanges()
    {
        return $this->hasMany(Exchange::class, 'responder_id');
    }

    /**
     * Check if user is verified.
     */
    public function isVerified()
    {
        return $this->kyc_status === 'verified';
    }
}