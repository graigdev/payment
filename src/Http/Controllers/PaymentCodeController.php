<?php

namespace GraigDev\Payment\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use GraigDev\Payment\Models\Wallet;
use GraigDev\Payment\Models\PaymentCode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PaymentCodeController extends Controller
{
    /**
     * Display a listing of the payment codes.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $generatedCodes = PaymentCode::where('generated_by', Auth::id())
            ->latest()
            ->paginate(10);
            
        $redeemedCodes = PaymentCode::where('used_by', Auth::id())
            ->latest()
            ->paginate(10);

        return view('payment::codes.index', compact('generatedCodes', 'redeemedCodes'));
    }

    /**
     * Generate a new payment code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generate(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $wallet = Wallet::where('user_id', Auth::id())->firstOrFail();
        
        if (!$wallet->is_active) {
            return redirect()->back()->with('error', 'Your wallet is currently inactive.');
        }

        if ($wallet->balance < $request->amount) {
            return redirect()->back()->with('error', 'Insufficient balance.');
        }

        // Generate unique code
        $codePrefix = config('payment.code_prefix', 'PAY');
        $codeLength = config('payment.code_length', 10);
        $code = $this->generateUniqueCode($codePrefix, $codeLength);

        // Create payment code
        $paymentCode = PaymentCode::create([
            'code' => $code,
            'amount' => $request->amount,
            'description' => $request->description,
            'expires_at' => $request->expires_at,
            'generated_by' => Auth::id(),
        ]);

        // Deduct from wallet
        $wallet->withdraw($request->amount, 'Generated payment code: ' . $code);

        return redirect()->route('payment.codes.index')->with('success', 'Payment code generated successfully.');
    }

    /**
     * Redeem a payment code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function redeem(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:payment_codes,code',
        ]);

        $paymentCode = PaymentCode::where('code', $request->code)
            ->unused()
            ->valid()
            ->first();

        if (!$paymentCode) {
            return redirect()->back()->with('error', 'Invalid or expired payment code.');
        }

        $wallet = Wallet::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'balance' => 0,
                'currency' => config('payment.currency', 'USD'),
                'is_active' => true,
            ]
        );

        if (!$wallet->is_active) {
            return redirect()->back()->with('error', 'Your wallet is currently inactive.');
        }

        // Mark code as used
        $paymentCode->markAsUsed(Auth::id());

        // Add to wallet
        $wallet->deposit($paymentCode->amount, 'Redeemed payment code: ' . $paymentCode->code);

        return redirect()->route('payment.wallet.index')->with('success', 'Payment code redeemed successfully.');
    }

    /**
     * Generate a unique code.
     *
     * @param  string  $prefix
     * @param  int  $length
     * @return string
     */
    protected function generateUniqueCode($prefix, $length)
    {
        do {
            $code = $prefix . '-' . strtoupper(Str::random($length));
        } while (PaymentCode::where('code', $code)->exists());

        return $code;
    }
} 