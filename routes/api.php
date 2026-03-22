<?php

use App\Http\Controllers\API\Auth\AuthenticationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// No Login Endpoints
Route::middleware(['throttle:api'])->as('api.')->group(function () {
    Route::get('/', function () {
        return [
            'version' => '1.0.0',
        ];
    })->name('root');

    Route::post('/login', [AuthenticationController::class, 'login'])->name('user.login');
});

// Login Endpoints
Route::middleware(['auth:sanctum', 'throttle:api'])->as('api.')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->name('me');

    // Only ADMIN (ability by role) can use this API endpoint
    // At least one valid ability can use this API endpoint
    Route::middleware(['ability:ADMIN'])->as('admin.')->group(function () {
        Route::get('/admin/dashboard', function () {
            return response()->json(['message' => 'Welcome Admin']);
        })->name('dashboard');
    });
});