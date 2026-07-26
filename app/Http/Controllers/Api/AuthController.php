<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use OpenApi\Attributes as OA;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    #[OA\Post(path: "/api/login", summary: "User & Vendor Login", tags: ["Authentication"])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(
        properties: [
            new OA\Property(property: "email", type: "string", format: "email"),
            new OA\Property(property: "password", type: "string"),
            new OA\Property(property: "device_name", type: "string", default: "web_token")
        ]
    ))]
    #[OA\Response(response: 200, description: "Login successful")]
    #[OA\Response(response: 401, description: "Invalid credentials")]
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'nullable|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check if user is active
        if (!$user->is_active) {
            return response()->json([
                'message' => 'Your account is deactivated.'
            ], 403);
        }

        $token = $user->createToken($request->device_name ?? 'web_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'token' => $token,
        ]);
    }

    #[OA\Post(path: "/api/admin/login", summary: "Admin Login", tags: ["Authentication"])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(
        properties: [
            new OA\Property(property: "email", type: "string", format: "email"),
            new OA\Property(property: "password", type: "string")
        ]
    ))]
    #[OA\Response(response: 200, description: "Login successful")]
    #[OA\Response(response: 401, description: "Invalid credentials")]
    public function adminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)
                    ->where('role', 'admin')
                    ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid admin credentials.'
            ], 401);
        }

        $token = $user->createToken('admin_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'token' => $token,
        ]);
    }

    #[OA\Get(path: "/api/user", summary: "Get authenticated user details", tags: ["Authentication"], security: [["bearerAuth" => []]])]
    #[OA\Response(response: 200, description: "User details")]
    public function user(Request $request)
    {
        return $request->user();
    }

    #[OA\Post(path: "/api/logout", summary: "Logout User", tags: ["Authentication"], security: [["bearerAuth" => []]])]
    #[OA\Response(response: 200, description: "Logged out successfully")]
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.'
        ]);
    }
}
