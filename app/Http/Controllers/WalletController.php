<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $wallet = Wallet::where('user_id', $userId)->first();

        if (!$wallet) {
            return response()->json(['message' => 'Wallet not found.'], 404);
        }

        return response()->json([
            'wallet' => $wallet,
        ]);
    }

    public function send(Request $request)
    {
        // Validate the request
        $request->validate([
            'recipient_user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
        ]);

        // Get authenticated user's wallet
        $userId = Auth::id();
        $senderWallet = Wallet::where('user_id', $userId)->first();
        $recipientWallet = Wallet::where('user_id', $request->recipient_user_id)->first();

        DB::beginTransaction();

        try {
            // Calculate service charge (3% of the amount)
            $serviceCharge = $request->amount * 0.03;

            // Total amount to deduct from sender (amount + service charge)
            $totalDeduct = $request->amount + $serviceCharge;

            // Check if sender has sufficient balance
            if ($senderWallet->amount < $totalDeduct) {
                return response()->json(['message' => 'Insufficient balance.'], 400);
            }

            // Deduct the total amount from sender's wallet
            $senderWallet->amount -= $totalDeduct;
            $senderWallet->save();

            // Add the amount (excluding service charge) to recipient's wallet
            $recipientWallet->amount += $request->amount;
            $recipientWallet->save();

            // Record the transaction
            $transaction = Transaction::create([
                'amount' => $request->amount,
                'pay_user_id' => $userId,
                'recipient_user_id' => $request->recipient_user_id,
                'transaction_number' => uniqid(), // Generate a unique transaction number
                'transaction_date' => Carbon::now(),
                'service_charge' => $serviceCharge, // Store the service charge
                'remarks' => $request->remark,
                'total_amount' => $totalDeduct, // Total deducted from sender (amount + service charge)
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Transfer successful!',
                'service_charge' => $transaction
            ]);
        } catch (\Exception $e) {
            // Rollback the transaction if something fails
            DB::rollBack();
            return response()->json([
                'message' => 'Transaction failed. Please try again.',
                'error' => $e->getMessage()], 500);
        }
    }
}
