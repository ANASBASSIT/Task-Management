<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;




class AuthController extends Controller
{   

  

       
        
    
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
    
        $token = auth('api')->attempt($credentials);
    
        if (!$token) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }
    
        $user = auth('api')->user(); // Use 'api' guard to get the authenticated user
    
        return response()->json([
            'user' => $user,
            'token' => $token,
            'type' => 'bearer',
        ]);
    }
    

    public function register(Request $request)
    {
        try {
            $attributes = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string',
                'email' => 'required|string|email|max:255',
                'number' => 'required',
                'password' => 'required|string|min:6',
            ]);
    
            // Hash the password before saving
            $attributes['password'] = bcrypt($attributes['password']);
            $attributes['role'] = 'User'; 

            // Create the user
            $user = User::create($attributes);
    
            // Prepare credentials for token generation
            $credentials = $request->only('email', 'password');
            
            // Generate token
            $token = auth('api')->attempt($credentials);
    
            // Check if token was created successfully
            if (!$token) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
    
            return response()->json([
                'message' => 'User created successfully',
                'user' => $user,
                'token' => $token,
                'type' => 'bearer',
            ]);
    
        } catch (Exception $e) {
            
            return response()->json(["Exception occurred: " => $e->getMessage()], 403);
        }
    }
    
        public function logout()
        {   
            $user=auth('api')->user();
            if($user){
                auth('api')->logout();
                return response()->json([
                    'message' => 'user successfully logged out',
                ]);
            }
            return response()->json([
                'message' => 'No user is logged in',
            ], 401);
        }
    
        
 
        
    public static function getUser()
    {
        $user = auth('api')->user();
        return response()->json([
            'user' => [$user],
        ]);
    }
}