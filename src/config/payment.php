<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment Code Settings
    |--------------------------------------------------------------------------
    |
    | Configure the payment code generation settings
    |
    */
    'code_prefix' => env('PAYMENT_CODE_PREFIX', 'PAY'),
    'code_length' => env('PAYMENT_CODE_LENGTH', 10),
    
    /*
    |--------------------------------------------------------------------------
    | Wallet Settings
    |--------------------------------------------------------------------------
    |
    | Configure wallet related settings
    |
    */
    'currency' => env('PAYMENT_CURRENCY', 'USD'),
    'min_withdrawal' => env('MIN_WITHDRAWAL', 10),
    
    /*
    |--------------------------------------------------------------------------
    | Transaction Settings
    |--------------------------------------------------------------------------
    |
    | Configure transaction related settings
    |
    */
    'transaction_types' => [
        'deposit' => 'DEPOSIT',
        'withdrawal' => 'WITHDRAWAL',
        'transfer' => 'TRANSFER',
        'payment' => 'PAYMENT',
        'refund' => 'REFUND',
    ],
]; 