<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Services\MembershipOAuthService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected MembershipOAuthService $oauthService;

    public function __construct(MembershipOAuthService $oauthService)
    {
        $this->oauthService = $oauthService;
    }

    /**
     * Resolve target property by property_id.
     */
    protected function resolveProperty(Request $request): Property
    {
        $propertyId = $request->input('property_id') ?: $request->query('property_id');

        if (!$propertyId) {
            // Default to first active property
            $property = Property::where('has_membership', true)->first();
        } else {
            $property = Property::find($propertyId);
        }

        if (!$property) {
            throw new Exception('Invalid or missing property_id for authentication.');
        }

        return $property;
    }

    /**
     * Get dynamic property context and branding from Membership API.
     */
    public function propertyContext(Request $request): JsonResponse
    {
        try {
            $property = $this->resolveProperty($request);
            $response = $this->oauthService->getPropertyContext($property);

            return response()->json($response);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Member login using email and password.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'property_id' => 'nullable|integer',
        ]);

        try {
            $property = $this->resolveProperty($request);
            $response = $this->oauthService->loginWithPassword(
                $property,
                $request->input('email'),
                $request->input('password')
            );

            return response()->json($response);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 401);
        }
    }

    /**
     * Request OTP for passwordless login.
     */
    public function requestOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'property_id' => 'nullable|integer',
        ]);

        try {
            $property = $this->resolveProperty($request);
            $response = $this->oauthService->requestOtp(
                $property,
                $request->input('email')
            );

            return response()->json($response);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Verify OTP code for passwordless login.
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string',
            'property_id' => 'nullable|integer',
        ]);

        try {
            $property = $this->resolveProperty($request);
            $response = $this->oauthService->verifyOtp(
                $property,
                $request->input('email'),
                $request->input('otp')
            );

            return response()->json($response);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 401);
        }
    }

    /**
     * Login or auto-register with Google ID Token.
     */
    public function googleLogin(Request $request): JsonResponse
    {
        $request->validate([
            'id_token' => 'required|string',
            'referral_code' => 'nullable|string',
            'property_id' => 'nullable|integer',
        ]);

        try {
            $property = $this->resolveProperty($request);
            $response = $this->oauthService->loginWithGoogle(
                $property,
                $request->input('id_token'),
                $request->input('referral_code')
            );

            return response()->json($response);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 401);
        }
    }

    /**
     * Request OTP for registration.
     */
    public function registerOtpRequest(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'property_id' => 'nullable|integer',
        ]);

        try {
            $property = $this->resolveProperty($request);
            $response = $this->oauthService->requestRegisterOtp(
                $property,
                $request->input('email')
            );

            return response()->json($response);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Verify registration OTP.
     */
    public function registerOtpVerify(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string',
            'property_id' => 'nullable|integer',
        ]);

        try {
            $property = $this->resolveProperty($request);
            $response = $this->oauthService->verifyRegisterOtp(
                $property,
                $request->input('email'),
                $request->input('otp')
            );

            return response()->json($response);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Register new member.
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string',
            'password' => 'required|string|min:6',
            'registration_token' => 'required|string',
            'referral_code' => 'nullable|string|max:50',
            'property_id' => 'nullable|integer',
        ]);

        try {
            $property = $this->resolveProperty($request);
            $response = $this->oauthService->registerMember(
                $property,
                $request->only([
                    'first_name',
                    'last_name',
                    'email',
                    'phone',
                    'password',
                    'registration_token',
                    'title',
                    'referral_code',
                ])
            );

            return response()->json($response);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Retrieve live member profile and loyalty points.
     */
    public function me(Request $request): JsonResponse
    {
        $bearerToken = $request->bearerToken();

        if (!$bearerToken) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated. Bearer token is missing.',
            ], 401);
        }

        try {
            $property = $this->resolveProperty($request);
            $response = $this->oauthService->getMemberProfile($property, $bearerToken);

            return response()->json($response);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 401);
        }
    }

    /**
     * Retrieve authenticated member transactions.
     */
    public function transactions(Request $request): JsonResponse
    {
        $bearerToken = $request->bearerToken();

        if (!$bearerToken) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated. Bearer token is missing.',
            ], 401);
        }

        try {
            $property = $this->resolveProperty($request);
            $page = max(1, (int) $request->query('page', 1));
            $limit = min(50, max(1, (int) $request->query('limit', 5)));

            $response = $this->oauthService->getMemberTransactions($property, $bearerToken, $page, $limit);

            return response()->json($response);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Logout and revoke session.
     */
    public function logout(Request $request): JsonResponse
    {
        $bearerToken = $request->bearerToken();

        if (!$bearerToken) {
            return response()->json([
                'status' => true,
                'message' => 'Already logged out.',
            ]);
        }

        try {
            $property = $this->resolveProperty($request);
            $response = $this->oauthService->logout($property, $bearerToken);

            return response()->json($response);
        } catch (Exception $e) {
            return response()->json([
                'status' => true,
                'message' => 'Logged out locally.',
            ]);
        }
    }
}
