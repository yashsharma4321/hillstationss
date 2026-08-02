<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class CustomerAuthController extends Controller
{
    // ─── OTP: STEP 1 — Send OTP ─────────────────────────────────────────────

    #[OA\Post(path: "/api/customer/send-otp", summary: "Send OTP to email for login/register", tags: ["Customer Auth"])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(
        properties: [
            new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com"),
        ]
    ))]
    #[OA\Response(response: 200, description: "OTP sent successfully")]
    #[OA\Response(response: 422, description: "Validation error")]
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $email = strtolower(trim($request->email));
        $otp   = (string) random_int(100000, 999999);          // 6-digit OTP
        $key   = 'otp_' . sha1($email);

        // Store in cache for 10 minutes (driver: database per .env)
        Cache::put($key, $otp, now()->addMinutes(10));

        // Send mail (log driver in dev — check storage/logs/laravel.log)
        Mail::to($email)->send(new OtpMail($otp, $email));

        return response()->json([
            'status'  => 'success',
            'message' => 'OTP sent to your email. Valid for 10 minutes.',
            'otp'     => $otp, // Added as requested
        ]);
    }

    // ─── OTP: STEP 2 — Verify OTP ───────────────────────────────────────────

    #[OA\Post(path: "/api/customer/verify-otp", summary: "Verify OTP and login/register customer", tags: ["Customer Auth"])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(
        properties: [
            new OA\Property(property: "email",       type: "string", format: "email"),
            new OA\Property(property: "otp",         type: "string", example: "123456"),
            new OA\Property(property: "device_name", type: "string", example: "mobile"),
        ]
    ))]
    #[OA\Response(response: 200, description: "Logged in / registered successfully")]
    #[OA\Response(response: 422, description: "Invalid or expired OTP")]
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|string|size:6',
        ]);

        $email  = strtolower(trim($request->email));
        $key    = 'otp_' . sha1($email);
        $cached = Cache::get($key);

        if (!$cached || $cached !== $request->otp) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid or expired OTP. Please request a new one.',
            ], 422);
        }

        // OTP verified — clear from cache
        Cache::forget($key);

        $isNew = false;
        $user  = User::where('email', $email)->first();

        if (!$user) {
            // Auto-register new customer
            $user  = User::create([
                'name'              => explode('@', $email)[0],   // name from email prefix
                'email'             => $email,
                'password'          => bcrypt(Str::random(24)),   // random password (OTP-only login)
                'role'              => 'customer',
                'is_active'         => true,
                'email_verified_at' => now(),                     // OTP = email verified
            ]);
            $isNew = true;
        } else {
            // Mark email verified if not already
            if (!$user->email_verified_at) {
                $user->update(['email_verified_at' => now()]);
            }
        }

        if (!$user->is_active) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Your account has been deactivated. Please contact support.',
            ], 403);
        }

        // Revoke old tokens and issue fresh one
        $user->tokens()->where('name', $request->device_name ?? 'customer_app')->delete();
        $token = $user->createToken($request->device_name ?? 'customer_app')->plainTextToken;

        return response()->json([
            'status'       => 'success',
            'is_new_user'  => $isNew,
            'message'      => $isNew ? 'Account created and logged in.' : 'Logged in successfully.',
            'token'        => $token,
            'user'         => $this->formatUser($user),
        ]);
    }

    // ─── GOOGLE OAuth ────────────────────────────────────────────────────────

    #[OA\Post(path: "/api/customer/google", summary: "Login or register with Google", tags: ["Customer Auth"])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(
        properties: [
            new OA\Property(property: "id_token",    type: "string", description: "Google ID token from frontend"),
            new OA\Property(property: "device_name", type: "string", example: "mobile"),
        ]
    ))]
    #[OA\Response(response: 200, description: "Logged in / registered successfully")]
    #[OA\Response(response: 401, description: "Invalid Google token")]
    public function googleLogin(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        // Verify Google ID token
        $client = new \Google\Client();
        $client->setClientId(config('services.google.client_id'));

        try {
            $payload = $client->verifyIdToken($request->id_token);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Google token verification failed.',
            ], 401);
        }

        if (!$payload) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid Google token.',
            ], 401);
        }

        $googleId = $payload['sub'];
        $email    = strtolower($payload['email'] ?? '');
        $name     = $payload['name'] ?? explode('@', $email)[0];
        $avatar   = $payload['picture'] ?? null;

        if (!$email) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Could not retrieve email from Google account.',
            ], 422);
        }

        $isNew = false;

        // Find by google_id first, then by email
        $user = User::where('google_id', $googleId)->first()
             ?? User::where('email', $email)->first();

        if (!$user) {
            $user  = User::create([
                'name'              => $name,
                'email'             => $email,
                'password'          => bcrypt(Str::random(24)),
                'role'              => 'customer',
                'is_active'         => true,
                'email_verified_at' => now(),
                'google_id'         => $googleId,
                'avatar'            => $avatar,
            ]);
            $isNew = true;
        } else {
            // Keep google_id + avatar updated
            $user->update([
                'google_id' => $googleId,
                'avatar'    => $avatar ?? $user->avatar,
                'email_verified_at' => $user->email_verified_at ?? now(),
            ]);
        }

        if (!$user->is_active) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Your account has been deactivated.',
            ], 403);
        }

        $user->tokens()->where('name', $request->device_name ?? 'customer_app')->delete();
        $token = $user->createToken($request->device_name ?? 'customer_app')->plainTextToken;

        return response()->json([
            'status'      => 'success',
            'is_new_user' => $isNew,
            'message'     => $isNew ? 'Account created via Google.' : 'Logged in via Google.',
            'token'       => $token,
            'user'        => $this->formatUser($user),
        ]);
    }

    // ─── PROFILE (protected) ─────────────────────────────────────────────────

    #[OA\Get(path: "/api/customer/profile", summary: "Get authenticated customer profile", tags: ["Customer Auth"], security: [["bearerAuth" => []]])]
    #[OA\Response(response: 200, description: "Customer profile")]
    public function profile(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'user'   => $this->formatUser($request->user()),
        ]);
    }

    // ─── UPDATE PROFILE (protected) ──────────────────────────────────────────

    #[OA\Put(path: "/api/customer/profile", summary: "Update customer profile", tags: ["Customer Auth"], security: [["bearerAuth" => []]])]
    #[OA\RequestBody(required: true, content: new OA\MediaType(
        mediaType: "multipart/form-data",
        schema: new OA\Schema(properties: [
            new OA\Property(property: "name",   type: "string",  example: "John Doe"),
            new OA\Property(property: "phone",  type: "string",  example: "+919876543210"),
            new OA\Property(property: "avatar", type: "string",  format: "binary", description: "Profile photo (jpeg/png/jpg/webp, max 2MB)"),
        ])
    ))]
    #[OA\Response(response: 200, description: "Profile updated")]
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name'   => 'sometimes|required|string|max:255',
            'phone'  => 'sometimes|nullable|string|max:20',
            'avatar' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $user = $request->user();
        $data = $request->only(['name', 'phone']);

        if ($request->hasFile('avatar')) {
            // Delete old avatar if it's a stored file (not a Google URL)
            if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Profile updated successfully.',
            'user'    => $this->formatUser($user->fresh()),
        ]);
    }

    // ─── LOGOUT (protected) ──────────────────────────────────────────────────

    #[OA\Post(path: "/api/customer/logout", summary: "Logout customer", tags: ["Customer Auth"], security: [["bearerAuth" => []]])]
    #[OA\Response(response: 200, description: "Logged out")]
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Logged out successfully.',
        ]);
    }

    // ─── Helper ──────────────────────────────────────────────────────────────

    private function formatUser(User $user): array
    {
        return [
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'phone'      => $user->phone,
            'avatar'     => $user->avatar,
            'role'       => $user->role,
            'verified'   => (bool) $user->email_verified_at,
        ];
    }
}
