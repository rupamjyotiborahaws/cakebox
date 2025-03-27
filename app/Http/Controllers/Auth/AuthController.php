<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use App\Models\User;
use Auth;

class AuthController extends Controller
{
    public function index(Request $request) {
        return view('frontend.index');
    }
    
    public function register(Request $request) {
        $user = User::create([
            'name' => $request['name'],
            'password' => bcrypt($request['password']),
            'email' => $request['email'],
            'phone_no' => $request['phone_no']
        ]);
        Auth::login($user);
        $user = auth()->user()->refresh();
        $setdata = json_encode($this->generateObject(
            'Success',
            'Authenticated successfully',
            $user->createToken(env('TOKEN_SECRET') || 'cakeboxtokenforuserdata')->plainTextToken,
            $user->id,
            $user->email
        ));
        Cookie::queue('user_data', base64_encode($setdata), 600);
        Cookie::queue('last_login', base64_encode($user->last_login), 600);
        return redirect()->route('dashboard');
    }

    public function generateObject($status, $message, $token, $userId, $userEmail)
    {
        return array(
            'status'  => $status,
            'message' => $message,
            'token' => $token,
            'userId'  => $userId,
            'userEmail' => $userEmail
        );
    }

    public function dashboard(Request $request) {
        return view('users.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        Cookie::queue('user_data', false, -1);
        Cookie::queue('last_login', false, -1);
        // auth()->user()->tokens()->delete();
        // setcookie('user_data', false, -1, '/', env('COOKIE_URL'));
        redirect()->route('index');
    }
}
