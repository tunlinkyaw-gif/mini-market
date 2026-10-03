<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});


Route::middleware('guest ')->group(function(){
    //register page 
Route::get('register',[AuthController::class,'registerPage'])->name('register');
Route::post('register',[AuthController::class,'register'])->name('register.store');

//login page 
Route::get('login',[AuthController::class,'loginPage'])->name('login');
Route::post('login',[AuthController::class,'login'])->name('login.store');
});




//Admin Dashboard

Route::middleware('auth')->group(function(){
    Route::get('admin/dashboard',function(){
        return view('admin.dashboard');
    })-> name ('admin.dashboard');
});

