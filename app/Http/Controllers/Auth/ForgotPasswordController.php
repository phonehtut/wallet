<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'new_password' => 'required|string|min:8',
            'backup_code' => 'required|string',
        ]);

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        // Decode the backup codes stored in JSON format
        $backupCodes = json_decode($user->security->backup_code, true);

        // Check if the backup code is valid
        if (in_array($request->backup_code, $backupCodes)) {
            // Update the password
            $user->password = \Hash::make($request->new_password);
            $user->save();

            // Optionally: Remove the used backup code
            $backupCodes = array_diff($backupCodes, [$request->backup_code]);
            $user->security->backup_code = json_encode(array_values($backupCodes));
            $user->security->save();

            return response()->json(['message' => 'Password reset successfully.'], 200);
        }

        return response()->json(['message' => 'Invalid backup code.'], 400);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'backup_code' => 'required|string',
        ]);

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        // Decode the backup codes stored in JSON format
        $backupCodes = json_decode($user->security->backup_code, true);

        // Check if the backup code is valid
        if (in_array($request->backup_code, $backupCodes)) {
            return response()->json(['message' => 'Backup code is valid.'], 200);
        }

        return response()->json(['message' => 'Invalid backup code.'], 400);
    }
}
