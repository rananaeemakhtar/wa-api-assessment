<?php

namespace App\Http\Controllers\Api;

use Auth;
use Hash;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthContoller extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="User login",
     *     description="Authenticate user and return a token for API access.",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User logged in successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Login successful."),
     *             @OA\Property(property="token", type="string", example="1|AbCdEf12345")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid email or password.")
     *         ),
     *     )
     * )
    */
    function login(Request $request) 
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    
        $request->session()->regenerate();
    
        return response()->json(['message' => 'Logged In', 'status' => true], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="User logout",
     *     description="Log the user out and invalidate their token.",
     *     tags={"Authentication"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="User logged out successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User logged out successfully.")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         ),
     *     )
     * )
    */
    function logout(Request $request) 
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();

        return response()->json(['message' => 'Logged Out', 'status' => true], 200);
    }

    /**
         * @OA\Post(
         *     path="/api/register",
         *     summary="Register a new user",
         *     description="Creates a new user account and returns the user details.",
         *     tags={"Authentication"},
         *     @OA\RequestBody(
         *         required=true,
         *         @OA\JsonContent(
         *             required={"name", "email", "password"},
         *             @OA\Property(property="name", type="string", example="John Doe"),
         *             @OA\Property(property="email", type="string", example="johndoe@example.com"),
         *             @OA\Property(property="password", type="string", example="password123"),
         *             @OA\Property(property="password_confirmation", type="string", example="password123")
         *         ),
         *     ),
         *     @OA\Response(
         *         response=201,
         *         description="User registered successfully",
         *         @OA\JsonContent(
         *             @OA\Property(property="message", type="string", example="User registered successfully."),
         *             @OA\Property(property="user", type="object", 
         *                 @OA\Property(property="id", type="integer", example=1),
         *                 @OA\Property(property="name", type="string", example="John Doe"),
         *                 @OA\Property(property="email", type="string", example="johndoe@example.com")
         *             ),
         *         ),
         *     ),
         *     @OA\Response(
         *         response=422,
         *         description="Validation Error",
         *         @OA\JsonContent(
         *             @OA\Property(property="message", type="string", example="The given data was invalid."),
         *             @OA\Property(property="errors", type="object", example={"email": {"The email has already been taken."}})
         *         ),
         *     )
         * )
    */
    function register(Request $request)  
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'email_verified_at' => now(),
            'password' => Hash::make($request->input('password'))
        ]);

        // Optionally, log in the user after registration
        auth()->login($user);

        // Return the user data as JSON
        return response()->json([
            'status' => true,
            'message' => 'Registration successful',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
            ],
        ], 201);    
    }
}
