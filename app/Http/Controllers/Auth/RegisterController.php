<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Security;
use App\Models\User;
use App\Models\UserInformation;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RegisterController extends Controller
{

    public function register(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'name' => 'required|string|max:50',
                'email' => 'required|string|email|max:100|unique:users',
                'phone' => 'required|string|max:20|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'password_confirmation' => 'required|string|min:8',
                'pin' => 'min:5|max:5',
                'nrc_front' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'nrc_back' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'nrc_number' => 'required',
                'birth_date' => 'required|date',
                'work' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            // Store the NRC images
            $nrcFrontPath = $request->file('nrc_front')->store('nrc_photos/front', 'public');
            $nrcBackPath = $request->file('nrc_back')->store('nrc_photos/back', 'public');

            // Create the user and save basic info in the `users` table
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => bcrypt($request->password),
            ]);

            // Create user information in the `user_informations` table
            $userInformation = new UserInformation([
                'user_id' => $user->id,
                'nrc_front' => $nrcFrontPath,
                'nrc_back' => $nrcBackPath,
                'nrc_number' => $request->nrc_number,
                'birth_date' => $request->birth_date,
                'work_id' => $request->work,
                'pin' => $request->pin,
            ]);

            $userInformation->save(); // Save the user information

            function generateBackupCodes($count = 10)
            {
                $codes = [];
                while (count($codes) < $count) {
                    $code = Str::random(8); // Generate a random 8-character string
                    if (!in_array($code, $codes)) {
                        $codes[] = $code; // Ensure the code is unique
                    }
                }
                return $codes;
            }

            $backupCodes = generateBackupCodes();

            $backup = Security::create([
                'user_id' => $user->id,
                'backup_code' => json_encode($backupCodes),
            ]);

            $wallet = Wallet::create([
                'user_id' => $user->id,
                'amount' => 0,
                'payin' => 0,
                'payout' => 0,
            ]);

            // Generate a token for the user (assuming using Sanctum or Passport)
            $token = $user->createToken('API Token')->plainTextToken; // For Sanctum
            // $token = $user->createToken('API Token')->accessToken; // For Passport

            // Return the token and user details
            return response()->json([
                'message' => 'User registered and logged in successfully!',
                'token' => $token,
                'user' => $user,
                'backup' => $backup,
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
}
