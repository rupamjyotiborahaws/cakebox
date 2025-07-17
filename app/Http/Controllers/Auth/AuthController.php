<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use App\Traits\ApiResponse;
use App\Models\User;
use Auth;

class AuthController extends Controller
{    
    use ApiResponse;
    
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
        if($user) {
            $token = $user->createToken('web_app_token')->plainTextToken;
            $cookie1 = Cookie::make('auth_token', $token, 60 * 24 * 7, '/', null, false, true);
            $cookie2 = Cookie::make('last_login', base64_encode($user->last_login), 60 * 24 * 7, '/', null, false, true);
            return response()->json([
                'status' => 'success',
                'landing' => $user->isAdmin === 0 ? 'dashboard' : 'admin/dashboard',
                'message' => 'Account created successfully.'
            ])->withCookie($cookie1)->withCookie($cookie2);
        } else {
            return $this->error('Could not create the account', 'failed');   
        }
    }

    public function login(Request $request) {
        $attr = [
            'email' => $request['email'],
            'password' => $request['password']
        ];
        $user = User::where('email', $request['email'])->first();
        if($user) {
            if (!Auth::attempt($attr)) {
                return $this->error('Credentials does not matched', 'failed');
            }
            $user = auth()->user()->refresh();
            User::where(['id'=>$user->id])->update(['last_login'=>date('Y-m-d H:i:s')]);
            if($user) {
                $token = $user->createToken('web_app_token')->plainTextToken;
                $cookie1 = Cookie::make('auth_token', $token, 60 * 24 * 7, '/', null, false, true);
                $cookie2 = Cookie::make('last_login', base64_encode($user->last_login), 60 * 24 * 7, '/', null, false, true);
                return response()->json([
                    'status' => 'success',
                    'landing' => '/',
                    'message' => 'Login successfully.'
                ])->withCookie($cookie1)->withCookie($cookie2);
            } else {
                return $this->error('Could not logged in', 'failed');   
            }
        } else {
            return $this->error('User not found', 'failed');
        }
    }

    public function userLogin(Request $request) {
        return view('frontend.login');
    }

    public function dashboard(Request $request) {
        return view('users.dashboard');
    }

    public function profile(Request $request) {
        $user = User::where(['id' => Auth::user()->id])->get()[0];
        unset($user['password']);
        unset($user['provider_id']);
        unset($user['provider']);
        unset($user['last_login']);
        unset($user['remember_token']);
        unset($user['created_at']);
        unset($user['updated_at']);
        return view('users.profile',compact('user'));
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->tokens()->delete();
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Cookie::queue('auth_token', false, -1);
        Cookie::queue('last_login', false, -1);
        return redirect()->route('user-login')->with('success', 'You are succesfully logged out!');
    }
}
