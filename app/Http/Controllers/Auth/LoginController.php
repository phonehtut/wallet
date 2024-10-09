<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // Validate the incoming request
        $validator = \Validator::make($request->all(), [
            'phone' => 'required',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Attempt to log the user in
        if (Auth::attempt(['phone' => $request->phone, 'password' => $request->password])) {
            $user = Auth::user(); // Get the authenticated user

            // Create a token for the authenticated user
            $token = $user->createToken('API Token')->plainTextToken; // For Sanctum

            // Return a success response with the token
            return response()->json([
                'message' => 'Login successful!',
                'token' => $token,
                'user' => $user,
            ]);
        }

        // If the login attempt was unsuccessful
        return response()->json(['message' => 'Invalid phone or password.'], 401);
    }
}
