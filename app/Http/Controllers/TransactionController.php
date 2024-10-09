<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function index()
    {
        // Get the logged-in user's ID
        $userId = Auth::id();

        // Fetch transactions where the user is either the payer or the recipient
        $transactions = Transaction::with(['payUser', 'recipientUser'])
            ->where(function ($query) use ($userId) {
                $query->where('pay_user_id', $userId)
                    ->orWhere('recipient_user_id', $userId);
            })
            ->get();

        // Group transactions by type
        $groupedTransactions = [
            'payer' => $transactions->where('pay_user_id', $userId),
            'recipient' => $transactions->where('recipient_user_id', $userId),
        ];

        // Return grouped transactions as JSON
        return response()->json([
            'transactions' => $groupedTransactions,
        ]);
    }
}
