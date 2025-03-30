<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use App\Models\User;
use Auth;

class AuthController extends Controller
{    
    public function register(Request $request) {
        $user = User::create([
            'name' => $request['name'],
            'password' => bcrypt($request['password']),
            'email' => $request['email'],
            'phone_no' => $request['phone_no'],
            'last_login' => date('Y-m-d H:i:s')
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
        Cookie::queue('user_data', base64_encode($setdata), time() + (60 * 60 * 24 * 1));
        Cookie::queue('last_login', base64_encode($user->last_login), time() + (60 * 60 * 24 * 1));
        return redirect()->route('dashboard');
    }

    public function login(Request $request) {
        $attr = [
            'email' => $request['email'],
            'password' => $request['password']
        ];
        $user = User::where('email', $request['email'])->first();
        if($user) {
            if (!Auth::attempt($attr)) {
                return redirect()->route('user-login')->with('error', 'Credentials does not matched!');
            }
            $user = auth()->user()->refresh();
            User::where(['id'=>$user->id])->update(['last_login'=>date('Y-m-d H:i:s')]);
            $setdata = json_encode($this->generateObject(
                'Success',
                'Authenticated successfully',
                $user->createToken(env('TOKEN_SECRET') || 'cakeboxtokenforuserdata')->plainTextToken,
                $user->id,
                $user->email
            ));
            Cookie::queue('user_data', base64_encode($setdata), time() + (60 * 60 * 24 * 1));
            Cookie::queue('last_login', base64_encode($user->last_login), time() + (60 * 60 * 24 * 1));
            return redirect()->route('dashboard');
        } else {
            return redirect()->route('user-login')->with('error', 'User not found');
        }
    }

    public function userLogin(Request $request) {
        return view('frontend.login');
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
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Cookie::queue('user_data', false, -1);
        Cookie::queue('last_login', false, -1);
        return redirect()->route('user-login')->with('success', 'You are succesfully logged out!');
    }
}
