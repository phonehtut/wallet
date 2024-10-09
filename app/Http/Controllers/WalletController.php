<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
