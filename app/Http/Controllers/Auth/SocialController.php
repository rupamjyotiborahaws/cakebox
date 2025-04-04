<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use Auth;
use App\Models\User;

class SocialController extends Controller
{
    public function redirect(Request $request, $provider)
    {
        try {
	        Cookie::queue('url__', base64_encode($request->url), time() + (60 * 60 * 24 * 1));
            return  Socialite::driver($provider)->stateless()->redirect();
        } catch (\Exception $error) {
            return response()->json(['message' => 'something went wrong ' . $error->getMessage()]);
        }
    }

    // callback function call after redirect from google/facebook

    public function callback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
            $NameArray = explode(' ',$socialUser->getName());
            $users = User::where(['email' => $socialUser->getEmail()])->first();
            if ($users) {
                if(isset($users->provider)) {
                    if ($users->provider !== $provider) {
                        $user = User::find($users->id);
                        $user->name = $NameArray[0];
                        $user->provider_id = $socialUser->getId();
                        $user->provider = $provider;
			            $user->last_login = date('Y-m-d H:i:s');
                        $user->update();
                    }
                } else {
                    $user = User::find($users->id);
                    $user->name = $NameArray[0];
                    $user->provider_id = $socialUser->getId();
                    $user->provider = $provider;
		            $user->last_login = date('Y-m-d H:i:s');
                    $user->update();
                }
                Auth::login($users);
                // $user = auth()->user()->refresh();
        	    //     $setdata = json_encode($this->generateObject(
            	// 	$user->id,
            	// 	$user->email
        	    // ));
        	    $token = $user->createToken('web_app_token')->plainTextToken;
                $cookie1 = Cookie::make('auth_token', $token, 60 * 24 * 7, '/', null, false, true);
                $cookie2 = Cookie::make('last_login', base64_encode($user->last_login), 60 * 24 * 7, '/', null, false, true);
        	    return redirect('/dashboard')->cookie($cookie1)->cookie($cookie2);
            } else {
		        $last_login = date('Y-m-d H:i:s');
                $user = User::create([
                    'name'          => $NameArray[0],
                    'email'         => $socialUser->getEmail(),
                    'password'      => Hash::make($socialUser->getId()),
                    'provider_id'   => $socialUser->getId(),
                    'provider'      => $provider,
		            'last_login'    => $last_login,
		            'phone_no'	    => ''
                ]);
                Auth::login($user);
		        $token = $user->createToken('web_app_token')->plainTextToken;
                $cookie1 = Cookie::make('auth_token', $token, 60 * 24 * 7, '/', null, false, true);
                $cookie2 = Cookie::make('last_login', base64_encode($user->last_login), 60 * 24 * 7, '/', null, false, true);
                return redirect('/dashboard')->cookie($cookie1)->cookie($cookie2);
            }
        } catch (\Exception $error) {
            return redirect()->away('/');
        }
    }

    public function generateObject($userId, $userEmail)
    {
        return array(
            'userId'  => $userId,
            'userEmail' => $userEmail
        );
    }
}
