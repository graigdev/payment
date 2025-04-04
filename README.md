# Payment Package for Laravel

A Laravel package that generates payment codes for subscriptions with wallet management.

## Features

- Wallet management system
- Payment code generation and redemption
- Transaction tracking
- Configurable settings

## Installation

You can install the package via composer:

```bash
composer require graigdev/payment
```

Then publish the config file:

```bash
php artisan vendor:publish --tag="payment-config"
```

### Optional: Publish Migrations and Models

You can publish the migrations to customize them before running:

```bash
php artisan vendor:publish --tag="payment-migrations"
```

#### Publishing Models

You have two options to publish the models:

**Option 1**: Using the vendor:publish command:
```bash
php artisan vendor:publish --tag="payment-models"
```

**Option 2**: Using the custom command (recommended):
```bash
php artisan payment:publish-models
```

The custom command automatically adjusts namespaces and references within the models. Use the `--force` flag to overwrite existing models:
```bash
php artisan payment:publish-models --force
```

If you publish the models, all references in your code should be updated from `GraigDev\Payment\Models` to `App\Models\Payment`.

Finally, run the migrations:

```bash
php artisan migrate
```

## Usage

### Wallet Management

Each user has a wallet that can be used to manage their balance.

```php
// Get a user's wallet
$wallet = \GraigDev\Payment\Models\Wallet::where('user_id', auth()->id())->first();

// If you published the models, use this instead:
// $wallet = \App\Models\Payment\Wallet::where('user_id', auth()->id())->first();

// Deposit funds
$wallet->deposit(100, 'Manual deposit');

// Withdraw funds
$wallet->withdraw(50, 'Manual withdrawal');
```

### Payment Codes

Generate and redeem payment codes:

```php
// Generate a payment code
$paymentCode = \GraigDev\Payment\Models\PaymentCode::create([
    'code' => 'PAY-' . strtoupper(Str::random(10)),
    'amount' => 100,
    'description' => 'Subscription payment',
    'generated_by' => auth()->id(),
]);

// Redeem a payment code
$code = 'PAY-ABCDEFGHIJ';
$paymentCode = \GraigDev\Payment\Models\PaymentCode::where('code', $code)
    ->unused()
    ->valid()
    ->first();

if ($paymentCode) {
    $paymentCode->markAsUsed(auth()->id());
    $wallet->deposit($paymentCode->amount, 'Redeemed code: ' . $code);
}
```

### Transactions

View transaction history:

```php
// Get all transactions for a wallet
$transactions = $wallet->transactions;

// Get transactions of a specific type
$deposits = $wallet->transactions()->ofType('DEPOSIT')->get();
```

## Configuration

You can configure the package by editing the `config/payment.php` file:

```php
return [
    // Payment code settings
    'code_prefix' => 'PAY',
    'code_length' => 10,
    
    // Wallet settings
    'currency' => 'USD',
    'min_withdrawal' => 10,
    
    // Transaction types
    'transaction_types' => [
        'deposit' => 'DEPOSIT',
        'withdrawal' => 'WITHDRAWAL',
        'transfer' => 'TRANSFER',
        'payment' => 'PAYMENT',
        'refund' => 'REFUND',
    ],
];
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information. 