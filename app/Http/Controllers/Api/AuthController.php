<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login and return access token.
     *
     * Authenticates a user with email and password credentials and returns an access token
     * that can be used for subsequent API requests.
     *
     * @param  LoginRequest  $request  The login request containing email, password, and optional device_name
     * @return JsonResponse Returns access token and user information on success
     *
     * @response 200 {
     *   "access_token": "1|abc123...",
     *   "token_type": "Bearer",
     *   "user": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "john@example.com",
     *     "whatsapp": "+628123456789",
     *     "affiliate_code": "AFF123",
     *     "status": "active"
     *   }
     * }
     * @response 403 {
     *   "message": "Your account is not active. Please contact the administrator."
     * }
     * @response 422 {
     *   "message": "The provided credentials are incorrect.",
     *   "errors": {
     *     "email": ["The provided credentials are incorrect."]
     *   }
     * }
     */
    public function login(LoginRequest $request): JsonResponse
    {

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check if user is active
        if ($user->status !== UserStatus::ACTIVE) {
            return response()->json([
                'message' => 'Your account is not active. Please contact the administrator.',
            ], 403);
        }

        // Create token
        $deviceName = $request->device_name ?? $request->userAgent() ?? 'unknown';
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'whatsapp' => $user->whatsapp,
                'affiliate_code' => $user->affiliate_code,
                'status' => $user->status->value,
            ],
        ]);
    }

    /**
     * Logout and revoke current token.
     *
     * Revokes the current access token, effectively logging out the user.
     * This endpoint requires authentication.
     *
     * @param  Request  $request  The authenticated request
     * @return JsonResponse Returns success message
     *
     * @response 200 {
     *   "message": "Successfully logged out."
     * }
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     */
    public function logout(Request $request): JsonResponse
    {
        // Revoke the current token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out.',
        ]);
    }

    /**
     * Get authenticated user information.
     *
     * Returns the profile information of the currently authenticated user.
     * This endpoint requires authentication.
     *
     * @param  Request  $request  The authenticated request
     * @return JsonResponse Returns user profile information
     *
     * @response 200 {
     *   "user": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "john@example.com",
     *     "whatsapp": "+628123456789",
     *     "affiliate_code": "AFF123",
     *     "status": "active",
     *     "profile_photo": "https://example.com/photo.jpg",
     *     "created_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'whatsapp' => $user->whatsapp,
                'affiliate_code' => $user->affiliate_code,
                'status' => $user->status->value,
                'profile_photo' => $user->profile_photo,
                'created_at' => $user->created_at,
            ],
        ]);
    }
}
