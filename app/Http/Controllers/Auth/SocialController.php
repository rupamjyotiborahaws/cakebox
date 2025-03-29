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
                $setdata = json_encode($this->generateObject(
                        'Success',
                        'Authenticated successfully',
                        $user->createToken(env('TOKEN_SECRET') || 'cakeboxtokenforuserdata')->plainTextToken,
                        $user->id,
                        $user->email
                ));
                // sending welcome mail
                /*$welcomeEmailer = WelcomeEmailer::first();
                $latest_book = Product::where('parent_id', null)->where('id', $welcomeEmailer->product_id)->with('media')->orderBy('id', 'Desc')->first();
                if($latest_book){
                    $desc = Str::limit(strip_tags($latest_book->description), 35);
                    $isActive = true;
                    $slug = $latest_book->slug;
                    $name = $latest_book->name;
                    if ($latest_book->media !== null && $latest_book->media->first() !== null) {
                        $image_url = '';
                        foreach($latest_book->media as $img){
                            if($img->collection_name === "thumbnail"){
                                $image_url = $img->getUrl();
                            }
                        }
                    } else {
                        $image_url = '';
                    }
                }else{
                    $desc = '';
                    $isActive = false;
                    $image_url = '';
                    $name = '';
                    $slug = '';
                }
                $dataArray = ['email' => $socialUser->getEmail(), 'subject' => 'Welcome', 'image_url' => $image_url, 'desc' => $desc, 'name' => $name, 'slug'=> $slug, 'isActive' => $isActive];

                $sendMail = (new SendWelcomeEmail($dataArray));
                $this->dispatch($sendMail);*/

                //setcookie('__ajxd', base64_encode($setdata), time() + (60 * 60 * 24 * 7), '/', env('COOKIE_URL'));
		        Cookie::queue('user_data', base64_encode($setdata), time() + (60 * 60 * 24 * 1));
                Cookie::queue('last_login', base64_encode($user->last_login), time() + (60 * 60 * 24 * 1));
                return redirect()->route('dashboard');
            }
        } catch (\Exception $error) {
            return redirect()->away(env('FRONTEND_REDIRECT_URL'));
        }
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
}
