<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ExtranetController;
use App\Http\Controllers\PropertyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Booking Engine BFF API Routes
|--------------------------------------------------------------------------
*/

Route::get('/status', function () {
    return response()->json([
        'service' => 'Booking Engine BFF POC API',
        'status' => 'online',
        'environment' => config('app.env'),
        'timestamp' => now()->toIso8601String(),
    ]);
});

// Property & Room Catalog (Public)
Route::get('/properties', [PropertyController::class, 'index']);
Route::get('/properties/{id}', [PropertyController::class, 'show']);

// OAuth 2.1 Member Authentication & Discovery (BFF Proxy to Membership API)
Route::prefix('auth')->group(function () {
    Route::get('/property-context', [AuthController::class, 'propertyContext']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/otp/request', [AuthController::class, 'requestOtp']);
    Route::post('/otp/verify', [AuthController::class, 'verifyOtp']);
    Route::post('/google', [AuthController::class, 'googleLogin']);
    Route::post('/register/otp/request', [AuthController::class, 'registerOtpRequest']);
    Route::post('/register/otp/verify', [AuthController::class, 'registerOtpVerify']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Booking & Reservation
Route::prefix('booking')->group(function () {
    Route::post('/reserve', [BookingController::class, 'reserve']);
    Route::get('/history', [BookingController::class, 'history']);
    Route::get('/{bookingCode}', [BookingController::class, 'show']);
});

// Extranet Property & Connection Management
Route::prefix('extranet')->group(function () {
    Route::get('/properties', [ExtranetController::class, 'index']);
    Route::post('/properties', [ExtranetController::class, 'store']);
    Route::get('/properties/{id}', [ExtranetController::class, 'show']);
    Route::put('/properties/{id}', [ExtranetController::class, 'update']);
    Route::post('/properties/{id}/test-connection', [ExtranetController::class, 'testConnection']);
});
