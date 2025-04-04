<?php

namespace GraigDev\Payment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wallet extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'balance',
        'currency',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the transactions for the wallet.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the user that owns the wallet.
     */
    public function user()
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }

    /**
     * Deposit amount to the wallet.
     *
     * @param float $amount
     * @param string $description
     * @return Transaction
     */
    public function deposit($amount, $description = 'Deposit')
    {
        $this->balance += $amount;
        $this->save();

        return $this->transactions()->create([
            'type' => config('payment.transaction_types.deposit'),
            'amount' => $amount,
            'description' => $description,
        ]);
    }

    /**
     * Withdraw amount from the wallet.
     *
     * @param float $amount
     * @param string $description
     * @return Transaction|bool
     */
    public function withdraw($amount, $description = 'Withdrawal')
    {
        if ($this->balance < $amount) {
            return false;
        }

        $this->balance -= $amount;
        $this->save();

        return $this->transactions()->create([
            'type' => config('payment.transaction_types.withdrawal'),
            'amount' => $amount,
            'description' => $description,
        ]);
    }
} 