<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Membership Platform Integration Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for connecting this Booking Engine BFF to the multi-tenant
    | Membership Platform API using OAuth 2.1 client credentials.
    |
    */

    'api_url' => env('MEMBERSHIP_API_URL', 'http://localhost:8000'),
    'tenant_domain' => env('MEMBERSHIP_TENANT_DOMAIN', 'jeevawasa.localhost'),
    'client_id' => env('MEMBERSHIP_CLIENT_ID', ''),
    'client_secret' => env('MEMBERSHIP_CLIENT_SECRET', ''),
];
