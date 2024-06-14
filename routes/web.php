<?php

use App\Http\Controllers\StripePaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    // payement
    Route::controller(StripePaymentController::class)->group(function () {
        Route::get('/stripe', 'stripe')->name('stripe');
        Route::post('/stripe', 'stripePost')->name('stripe.post');
    });
    Route::get('/test-env', function () {
        return response()->json([
            'STRIPE_KEY' => env('STRIPE_KEY'),
            'STRIPE_SECRET' => env('STRIPE_SECRET')
        ]);
    });
});
