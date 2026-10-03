<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MarketplaceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    // register page
    Route::get('register', [AuthController::class, 'registerPage'])->name('register');
    Route::post('register', [AuthController::class, 'register'])->name('register.store');

    // login page
    Route::get('login', [AuthController::class, 'loginPage'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.store');
});

// User Dashboard and Marketplace flow
Route::middleware('auth')->group(function () {
    Route::get('dashboard', [MarketplaceController::class, 'dashboard'])->name('dashboard');
    Route::get('products/{slug}', [MarketplaceController::class, 'showProduct'])->name('products.show');
    Route::post('products/{slug}/favorite', [MarketplaceController::class, 'toggleFavorite'])->name('favorites.toggle');
    Route::get('seller/{id}', [MarketplaceController::class, 'sellerProfile'])->name('seller.profile');
    Route::get('favorites', [MarketplaceController::class, 'favorites'])->name('favorites');
    Route::get('my-listings', [MarketplaceController::class, 'myListings'])->name('my-listings');
    Route::get('my-listings/{id}/edit', [MarketplaceController::class, 'editListing'])->name('listings.edit');
    Route::put('my-listings/{id}', [MarketplaceController::class, 'updateListing'])->name('listings.update');
    Route::patch('my-listings/{id}/status', [MarketplaceController::class, 'markListingSold'])->name('listings.status');
    Route::delete('my-listings/{id}', [MarketplaceController::class, 'deleteListing'])->name('listings.destroy');
    Route::get('sell', [MarketplaceController::class, 'sell'])->name('sell');
    Route::post('sell', [MarketplaceController::class, 'storeListing'])->name('sell.store');
    Route::get('messages', [MarketplaceController::class, 'messages'])->name('messages');
    Route::post('messages/{id}', [MarketplaceController::class, 'sendMessage'])->name('messages.send');
    Route::get('messages/{id}', [MarketplaceController::class, 'conversation'])->name('messages.show');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('user/dashboard', [MarketplaceController::class, 'dashboard'])->name('user.dashboard');

    Route::get('admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});
