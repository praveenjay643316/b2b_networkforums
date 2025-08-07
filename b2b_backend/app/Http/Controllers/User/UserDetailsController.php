<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserDetailsController extends Controller
{
    public function getAllUsers()
    {

        $users = User::with('company')->get();

        $formatted = $users->map(function ($user) {
            return [
                'profile_id' => $user->profile_id,
                'full_name'   => $user->first_name . ' ' . $user->last_name,
                'personal_mobile'  => $user->personal_mobile,
                'personal_email'  => $user->personal_email,
                'status' => $user->active ? 'Saved' : 'Pending',
                'company_name' => $user->company->company_name,
                'company_url'  => $user->company->company_url,
            ];
        });

        return response()->json(['data' => $formatted], 200);
    }
    public function getUserById($id)
    {
        $user = User::with('company')->where('profile_id', $id)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'firstName' => $user->first_name,
            'lastName' => $user->last_name,
            'email' => $user->personal_email,
            'mobile' => $user->personal_mobile,
            'companyname' => $user->company->company_name ?? '',
            'companyurl' => $user->company->company_url ?? ''
        ]);
    }
    public function updateUser(Request $request, $id)
    {
        $user = User::where('profile_id', $id)->first();

        if (!$user) return response()->json(['message' => 'User not found'], 404);

        $user->update([
            'first_name' => $request->firstName,
            'last_name' => $request->lastName,
            'personal_mobile' => $request->mobile
        ]);

        if ($user->company) {
            $user->company->update([
                'company_name' => $request->companyname,
                'company_url' => $request->companyurl,
            ]);
        }

        return response()->json(['message' => 'User updated successfully']);
    }
    public function deleteUser($id)
    {
        $user = User::where('profile_id', $id)->first();
        if (!$user) return response()->json(['message' => 'User not found'], 404);

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
