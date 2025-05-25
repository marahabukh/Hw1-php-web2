<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use Tymon\JWTAuth\Facades\JWTAuth;

class ApiController extends Controller
{
    /**
     * Register a new user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'data' => $user,
        ], 201);
    }
   public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'token' => $token,
        ]);
    }

public function profile(Request $request)
{
    try {
        $token = $request->bearerToken();
        return response()->json([
            'token_received' => $token,
            'user' => auth()->user()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
}
    /**
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsers(Request $request)
    {
        $limit = $request->input('limit', 15);
        $users = User::paginate($limit);
        
        return response()->json([
            'data' => $users->items(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    /**
     * Get a specific user by ID
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUser($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }
        
        return response()->json([
            'data' => $user,
        ]);
    }

    /**
     * Update a user
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8|confirmed',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        
        // Update name and email if provided
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        
        // Update password if provided
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();
        
        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user,
        ]);
    }

    /**
     * Delete a user
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteUser($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }
        
        $user->delete();
        
        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }
}
