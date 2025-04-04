<?php

namespace GraigDev\Payment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentCode extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'amount',
        'is_used',
        'used_by',
        'used_at',
        'expires_at',
        'description',
        'generated_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'is_used' => 'boolean',
        'used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user that used the payment code.
     */
    public function usedByUser()
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'used_by');
    }

    /**
     * Get the user that generated the payment code.
     */
    public function generatedByUser()
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'generated_by');
    }

    /**
     * Get the related transaction.
     */
    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'reference', 'code');
    }

    /**
     * Scope a query to only include unused codes.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnused($query)
    {
        return $query->where('is_used', false);
    }

    /**
     * Scope a query to only include valid (non-expired) codes.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeValid($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Mark the code as used.
     *
     * @param int $userId
     * @return bool
     */
    public function markAsUsed($userId)
    {
        $this->is_used = true;
        $this->used_by = $userId;
        $this->used_at = now();
        
        return $this->save();
    }
} 