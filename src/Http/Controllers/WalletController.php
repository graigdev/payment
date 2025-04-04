<?php

namespace GraigDev\Payment\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use GraigDev\Payment\Models\Wallet;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    /**
     * Display the wallet details.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $wallet = Wallet::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'balance' => 0,
                'currency' => config('payment.currency', 'USD'),
                'is_active' => true,
            ]
        );

        $transactions = $wallet->transactions()->latest()->paginate(10);

        return view('payment::wallet.index', compact('wallet', 'transactions'));
    }

    /**
     * Deposit to the wallet.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        $wallet = Wallet::where('user_id', Auth::id())->firstOrFail();
        
        if (!$wallet->is_active) {
            return redirect()->back()->with('error', 'Your wallet is currently inactive.');
        }

        $description = $request->description ?? 'Manual deposit';
        $wallet->deposit($request->amount, $description);

        return redirect()->route('payment.wallet.index')->with('success', 'Deposit successful.');
    }

    /**
     * Withdraw from the wallet.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:' . config('payment.min_withdrawal', 10),
            'description' => 'nullable|string|max:255',
        ]);

        $wallet = Wallet::where('user_id', Auth::id())->firstOrFail();
        
        if (!$wallet->is_active) {
            return redirect()->back()->with('error', 'Your wallet is currently inactive.');
        }

        if ($wallet->balance < $request->amount) {
            return redirect()->back()->with('error', 'Insufficient balance.');
        }

        $description = $request->description ?? 'Manual withdrawal';
        $wallet->withdraw($request->amount, $description);

        return redirect()->route('payment.wallet.index')->with('success', 'Withdrawal successful.');
    }
} 