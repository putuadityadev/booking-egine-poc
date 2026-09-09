<?php

namespace App\Services;

use App\Models\Property;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MembershipOAuthService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.membership.api_url', env('MEMBERSHIP_API_URL', 'http://myapp:8000')), '/');
    }

    /**
     * Resolve membership integration credentials for a given property.
     */
    public function getCredentials(Property $property): array
    {
        $integration = $property->membershipProperty;

        if (!$integration || !$integration->is_active) {
            throw new Exception("Membership is not configured or active for property: {$property->name}");
        }

        return [
            'tenant_domain' => $integration->x_tenant_domain,
            'client_id' => $integration->client_id,
            'client_secret' => $integration->client_secret,
            'merchant_id' => $integration->merchant_id,
            'corporate_id' => $integration->corporate_id,
        ];
    }

    /**
     * Standard HTTP request headers for multi-tenant OAuth calls.
     */
    protected function getHeaders(string $tenantDomain, ?string $bearerToken = null): array
    {
        $headers = [
            'Host' => "{$tenantDomain}:8000",
            'X-Tenant-Domain' => $tenantDomain,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        if ($bearerToken) {
            $headers['Authorization'] = "Bearer {$bearerToken}";
        }

        return $headers;
    }

    /**
     * Fetch public property context discovery (branding, capabilities, login methods).
     */
    public function getPropertyContext(Property $property): array
    {
        $creds = $this->getCredentials($property);

        $response = Http::withHeaders($this->getHeaders($creds['tenant_domain']))
            ->get("{$this->baseUrl}/api/v2/oauth/property-context/{$creds['client_id']}");

        return $this->handleResponse($response, 'OAUTH.PROPERTY_CONTEXT_FAILED');
    }

    /**
     * Test OAuth 2.1 credentials and tenant connection health.
     */
    public function testConnection(string $clientId, string $clientSecret, string $tenantDomain, ?string $merchantId = null): array
    {
        $response = Http::withHeaders($this->getHeaders($tenantDomain))
            ->get("{$this->baseUrl}/api/v2/oauth/property-context/{$clientId}");

        $context = $this->handleResponse($response, 'OAUTH.CONNECTION_TEST_FAILED');
        $data = $context['data'] ?? $context;

        // Auto-resolve merchant_id from branch.id or merchant.id if not provided
        $resolvedMerchantId = !empty($merchantId)
            ? $merchantId
            : ($data['branch']['id'] ?? $data['merchant']['id'] ?? null);

        $resolvedCorporateId = $data['corporate']['id'] ?? null;

        return [
            'connected' => true,
            'tenant_domain' => $tenantDomain,
            'client_id' => $clientId,
            'merchant_id' => $resolvedMerchantId,
            'corporate_id' => $resolvedCorporateId,
            'context' => $data,
        ];
    }

    /**
     * Authenticate member using email and password.
     */
    public function loginWithPassword(Property $property, string $email, string $password): array
    {
        $creds = $this->getCredentials($property);

        $response = Http::withHeaders($this->getHeaders($creds['tenant_domain']))
            ->post("{$this->baseUrl}/api/v2/oauth/member/login", [
                'client_id' => $creds['client_id'],
                'client_secret' => $creds['client_secret'],
                'merchant_id' => $creds['merchant_id'],
                'email' => $email,
                'password' => $password,
            ]);

        return $this->handleResponse($response, 'OAUTH.LOGIN_FAILED');
    }

    /**
     * Request OTP email for passwordless login.
     */
    public function requestOtp(Property $property, string $email): array
    {
        $creds = $this->getCredentials($property);

        $response = Http::withHeaders($this->getHeaders($creds['tenant_domain']))
            ->post("{$this->baseUrl}/api/v2/oauth/member/otp/request", [
                'client_id' => $creds['client_id'],
                'client_secret' => $creds['client_secret'],
                'merchant_id' => $creds['merchant_id'],
                'email' => $email,
            ]);

        return $this->handleResponse($response, 'OAUTH.OTP_REQUEST_FAILED');
    }

    /**
     * Verify OTP for passwordless login.
     */
    public function verifyOtp(Property $property, string $email, string $otp): array
    {
        $creds = $this->getCredentials($property);

        $response = Http::withHeaders($this->getHeaders($creds['tenant_domain']))
            ->post("{$this->baseUrl}/api/v2/oauth/member/otp/verify", [
                'client_id' => $creds['client_id'],
                'client_secret' => $creds['client_secret'],
                'merchant_id' => $creds['merchant_id'],
                'email' => $email,
                'otp' => $otp,
            ]);

        return $this->handleResponse($response, 'OAUTH.OTP_VERIFY_FAILED');
    }

    /**
     * Authenticate using Google ID Token.
     */
    public function loginWithGoogle(Property $property, string $idToken, ?string $referralCode = null): array
    {
        $creds = $this->getCredentials($property);

        $payload = [
            'client_id' => $creds['client_id'],
            'client_secret' => $creds['client_secret'],
            'merchant_id' => $creds['merchant_id'],
            'id_token' => $idToken,
        ];

        if (!empty($referralCode)) {
            $payload['referral_code'] = $referralCode;
        }

        $response = Http::withHeaders($this->getHeaders($creds['tenant_domain']))
            ->post("{$this->baseUrl}/api/v2/oauth/member/google", $payload);

        return $this->handleResponse($response, 'OAUTH.GOOGLE_LOGIN_FAILED');
    }

    /**
     * Request OTP for new member registration.
     */
    public function requestRegisterOtp(Property $property, string $email): array
    {
        $creds = $this->getCredentials($property);

        $response = Http::withHeaders($this->getHeaders($creds['tenant_domain']))
            ->post("{$this->baseUrl}/api/v2/oauth/register/otp/request", [
                'client_id' => $creds['client_id'],
                'client_secret' => $creds['client_secret'],
                'merchant_id' => $creds['merchant_id'],
                'email' => $email,
            ]);

        return $this->handleResponse($response, 'OAUTH.REGISTER_OTP_REQUEST_FAILED');
    }

    /**
     * Verify registration OTP and obtain registration token.
     */
    public function verifyRegisterOtp(Property $property, string $email, string $otp): array
    {
        $creds = $this->getCredentials($property);

        $response = Http::withHeaders($this->getHeaders($creds['tenant_domain']))
            ->post("{$this->baseUrl}/api/v2/oauth/register/otp/verify", [
                'client_id' => $creds['client_id'],
                'client_secret' => $creds['client_secret'],
                'merchant_id' => $creds['merchant_id'],
                'email' => $email,
                'otp' => $otp,
            ]);

        return $this->handleResponse($response, 'OAUTH.REGISTER_OTP_VERIFY_FAILED');
    }

    /**
     * Complete new member registration.
     */
    public function registerMember(Property $property, array $registerData): array
    {
        $creds = $this->getCredentials($property);

        $payload = array_merge($registerData, [
            'client_id' => $creds['client_id'],
            'client_secret' => $creds['client_secret'],
            'merchant_id' => $creds['merchant_id'],
        ]);

        $response = Http::withHeaders($this->getHeaders($creds['tenant_domain']))
            ->post("{$this->baseUrl}/api/v2/oauth/register", $payload);

        return $this->handleResponse($response, 'OAUTH.REGISTER_FAILED');
    }

    /**
     * Retrieve authenticated member profile via Bearer Token.
     */
    public function getMemberProfile(Property $property, string $bearerToken): array
    {
        $creds = $this->getCredentials($property);

        $response = Http::withHeaders($this->getHeaders($creds['tenant_domain'], $bearerToken))
            ->get("{$this->baseUrl}/api/v2/oauth/member/self", [
                'client_id' => $creds['client_id'],
            ]);

        return $this->handleResponse($response, 'OAUTH.GET_PROFILE_FAILED');
    }

    /**
     * Retrieve authenticated member transaction history via Bearer Token.
     */
    public function getMemberTransactions(Property $property, string $bearerToken, int $page = 1, int $limit = 5): array
    {
        $creds = $this->getCredentials($property);

        $response = Http::withHeaders($this->getHeaders($creds['tenant_domain'], $bearerToken))
            ->get("{$this->baseUrl}/api/v2/oauth/member/transactions", [
                'client_id' => $creds['client_id'],
                'page' => $page,
                'limit' => $limit,
            ]);

        return $this->handleResponse($response, 'OAUTH.GET_TRANSACTIONS_FAILED');
    }

    /**
     * Revoke access token and invalidate member session.
     */
    public function logout(Property $property, string $bearerToken): array
    {
        $creds = $this->getCredentials($property);

        $response = Http::withHeaders($this->getHeaders($creds['tenant_domain'], $bearerToken))
            ->post("{$this->baseUrl}/api/v2/oauth/token/revoke", [
                'client_id' => $creds['client_id'],
                'client_secret' => $creds['client_secret'],
            ]);

        return $this->handleResponse($response, 'OAUTH.LOGOUT_FAILED');
    }

    /**
     * Push completed transaction to Membership Platform (Dual-Auth Gateway V2).
     */
    public function pushTransaction(Property $property, array $transactionData): array
    {
        $creds = $this->getCredentials($property);

        // Inject OAuth 2.1 credentials and merchant ID into transaction payload
        $payload = array_merge($transactionData, [
            'client_id' => $creds['client_id'],
            'client_secret' => $creds['client_secret'],
            'merchant_id' => $creds['merchant_id'],
            'source' => 'booking_engine',
        ]);

        $response = Http::withHeaders($this->getHeaders($creds['tenant_domain']))
            ->post("{$this->baseUrl}/api/transaction/push/v2", $payload);

        return $this->handleResponse($response, 'TRANSACTION.PUSH_V2_FAILED');
    }

    /**
     * Normalize HTTP responses and throw structured exceptions on error.
     */
    protected function handleResponse($response, string $defaultErrorCode): array
    {
        $json = $response->json();

        if ($response->successful()) {
            return $json;
        }

        $message = $json['message'] ?? $response->body();
        $errorCode = $json['error_code'] ?? $defaultErrorCode;

        Log::warning('MEMBERSHIP_API_CALL_FAILED', [
            'status' => $response->status(),
            'error_code' => $errorCode,
            'message' => $message,
            'body' => $json,
        ]);

        throw new Exception($message, $response->status());
    }
}
