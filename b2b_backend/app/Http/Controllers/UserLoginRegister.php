<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class UserLoginRegister extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'personal_mobile' => 'required|string|max:20',
            'personal_email' => 'required|email|max:255',
            'company_name' => 'required|string|max:255',
            'company_url' => 'nullable|url|max:255',
        ]);
        try {
            // Create user
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name ?? '',
                'personal_mobile' => $request->personal_mobile,
                'personal_email' => $request->personal_email,
                'user_name' => $request->personal_email,
            ]);

            // Create company
            Company::create([
                'profile_id' => $user->profile_id,
                'company_name' => $request->company_name,
                'company_url' => $request->company_url ?? '',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User and Company created successfully'
            ], 201);
        } catch (\Exception $e) {
            // ❌ Catch and return any unexpected exceptions
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong during registration.',
                'error' => $e->getMessage() // Optional: remove in production
            ], 500);
        }
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Retrieve user by email (using user_name field)
        $user = \App\Models\User::where('user_name', $request->email)->first();

        // Check if user exists and password matches (plain text comparison)
        if (!$user || $user->password !== $request->password) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        // Manually log in the user
        Auth::login($user);

        // Generate API token
        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $token,
        ]);
    }
}
