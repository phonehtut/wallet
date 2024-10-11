<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function showProfile()
    {
        try {
            $user = \Auth::user();

            return response()->json([
                'message' => 'This is a user profile',
                'user' => $user,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th,
            ]);
        }
    }

    public function showInformation(){

        try {
            $user = \Auth::user()->information;

            return response()->json([
                'message' => 'This is a user security information',
                'user' => $user,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th,
            ]);
        }
    }

    public function updateProfile(Request $request)
    {
        $user = \Auth::user();

        $validator = \Validator::make($request->all(), [
            'name' => 'string|max:50',
            'email' => 'email|max:100|string|unique:users,email,'.$user->id,
            'phone' => 'string|max:50|unique:users,phone,'.$user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'pin' => 'nullable|string|min:5|max:5',
            'backup_code' => 'required_with:password|string|min:6', // Backup code required when changing password
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ], 422);
        }

        // If the password is being changed, verify the backup code
        if (!empty($request->password)) {
            // Assuming you have a `backup_codes` column in the user table or another table, you need to validate the backup code.
            // Let's say you store backup codes as JSON in the `backup_codes` column.

            $backupCodes = json_decode($user->security->backup_code, true);

            if (!in_array($request->backup_code, $backupCodes)) {
                return response()->json([
                    'error' => 'Invalid backup code.',
                ], 422);
            }

            // If the backup code is valid, change the password
            $user->password = bcrypt($request->password);
        }

        // Update other fields
        $user->name = $request->name ?? $user->name;
        $user->email = $request->email ?? $user->email;
        $user->phone = $request->phone ?? $user->phone;

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }

    public function updateInformation(Request $request)
    {
        $user = \Auth::user();

        $validator = \Validator::make($request->all(), [
            'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'address' => 'nullable|string|max:500',
            'nrc_front' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'nrc_back' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'nrc_number' => 'nullable|string|min:14|max:15|unique:user_informations,nrc_number,'.$user->information->id,
            'birth_date' => 'nullable|date',
            'work' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }

        $userInformation = $user->information;  // Access user_information relation

        if ($request->hasFile('profile')) {
            $profilePath = $request->file('profile')->store('profiles', 'public');
            $userInformation->profile = $profilePath;
        }

        if ($request->hasFile('nrc_front')) {
            $nrcFrontPath = $request->file('nrc_front')->store('nrc_photos/front', 'public');
            $userInformation->nrc_front = $nrcFrontPath;
        }

        if ($request->hasFile('nrc_back')) {
            $nrcBackPath = $request->file('nrc_back')->store('nrc_photos/back', 'public');
            $userInformation->nrc_back = $nrcBackPath;
        }

        // Calculate Age
        if ($request->filled('birth_date')) {
            $birthDate = Carbon::parse($request->birth_date);
            $age = $birthDate->diffInYears(Carbon::now());
            $userInformation->birth_date = $request->birth_date;
            $userInformation->age = $age;
        }

        // Update other fields
        $userInformation->gender = $request->gender ?? $userInformation->gender;
        $userInformation->address = $request->address ?? $userInformation->address;
        $userInformation->nrc_number = $request->nrc_number ?? $userInformation->nrc_number;
        $userInformation->work_id = $request->work ?? $userInformation->work_id;
        $userInformation->description = $request->description ?? $userInformation->description;

        // Save the updated information
        $userInformation->save();

        return response()->json([
            'message' => 'Information updated successfully',
            'user_information' => $userInformation,
        ], 200);
    }
}
