<?php

namespace App\Http\Controllers\API;

use App\Models\Admin;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;


use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AdminController extends Controller
{
    public function adregister(Request $request)
    {
        try {
            $attributes = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255',
                'password' => 'required|string|min:6',
                'number' => 'required',
                'position' => 'required|string',
                
            ]);

            $attributes['password'] = bcrypt($attributes['password']);
            $attributes['role'] = 'Admin'; 
            $admin = Admin::create($attributes);
            $token = JWTAuth::fromUser($admin); // Use JWTAuth facade

            return response()->json([
                'message' => 'Admin created successfully',
                'admin' => $admin,
                'token' => $token,
            ]);
        } catch (Exception $e) {
            Log::error('Error during admin registration: ' . $e->getMessage());
            return response()->json(['error' => 'Exception occurred: ' . $e->getMessage()], 403);
        }
    }

    public function adlogin(Request $request)
    {
        // Validate incoming request
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        
        Log::info('Attempting admin login with credentials: ', $credentials);
        
        try {
            // Attempt to authenticate using the 'admin' guard
            if (!$token = auth('admins')->attempt($credentials)) {
                return response()->json([
                    'message' => 'Invalid credentials',
                ], 401);
            }
        
            // Retrieve the authenticated admin user
            $admin = auth('admins')->user();
        
            return response()->json([
                'admins' => $admin,
                'token' => $token,
                'type' => 'bearer',
            ]);
        } catch (Exception $e) {
            Log::error('Error during admin login: ' . $e->getMessage());
            return response()->json(['error' => 'Exception occurred: ' . $e->getMessage()], 500);
        }
    }
    public function adlogout()
    {
        $admin = auth('admins')->user();

        if ($admin) {
            auth('admins')->logout();
            return response()->json([
                'message' => 'Admin successfully logged out',
            ]);
        }

        return response()->json([
            'message' => 'No admin is logged in',
        ], 401);
    }
  
    public static function getadmin()
    {
        $admin = auth('admins')->user();
        return response()->json([
            'user' => [$admin],
        ]);
    }
}
