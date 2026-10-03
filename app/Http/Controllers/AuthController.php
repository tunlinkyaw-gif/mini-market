<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //Register page.
    public function registerPage (){
        return view ('auth.register');
    }
     //Register.
     public function register(Request $request){

     $request->validate([
        'name'=>'required|string',
        'email' => 'required|email|unique:users,email',
        'password'=>'required|string|confirmed',
        'password_confirmation'=>'required|string',
        'location'=>'required|string',
        'phone'=>'required|string',
     ]);
       User::create([
        'name'=>$request->name,
        'email'=>$request->email,
        'password'=>Hash::make($request->password),
        'location'=>$request->location,
        'phone'=>$request->phone,
       ]);

       return to_route('login');
     }


    //login page
    public function loginPage (){
        return view ('auth.login');
    }

    public function login(Request $request)
    {
        $credentials=$request->validate([
            'email' => 'required|email',
            'password'=>'required|string',
         ]);
         if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

}
