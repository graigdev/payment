<?php

namespace GraigDev\Payment\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use GraigDev\Payment\Models\Wallet;
use GraigDev\Payment\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Display a listing of the transactions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $wallet = Wallet::where('user_id', Auth::id())->firstOrFail();
        
        $query = $wallet->transactions();
        
        // Filter by transaction type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }
        
        // Sort by date
        $query->latest();
        
        $transactions = $query->paginate(15);
        
        // Get available transaction types from config
        $transactionTypes = config('payment.transaction_types');
        
        return view('payment::transactions.index', compact('transactions', 'wallet', 'transactionTypes'));
    }
} 