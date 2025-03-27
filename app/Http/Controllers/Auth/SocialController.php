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
            setcookie('url__', $request->url, time() + (60 * 60 * 24 * 1));
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
                        $user->update();
                    }
                } else {
                    $user = User::find($users->id);
                    $user->name = $NameArray[0];
                    $user->provider_id = $socialUser->getId();
                    $user->provider = $provider;
                    $user->update();
                }
                Auth::login($users);
                // $setdata = json_encode($this->generateObject(
                //     'Success',
                //     'Authenticated successfully',
                //     auth()->user()->createToken('SECRETTOKENOFWICMEHASBEENCREATEDHEREDONEWITHIT')->plainTextToken,
                //     auth()->user()
                // ));
                //setcookie('__ajxd', base64_encode($setdata), time() + (60 * 60 * 24 * 7), '/', env('COOKIE_URL'));
                return redirect()->away(env('FRONTEND_REDIRECT_URL'));
            } else {
                $user = User::create([
                    'name'          => $NameArray[0],
                    'email'         => $socialUser->getEmail(),
                    'password'      => Hash::make($socialUser->getId()),
                    'provider_id'   => $socialUser->getId(),
                    'provider'      => $provider,
                ]);
                Auth::login($user);
                // $setdata = json_encode($this->generateObject(
                //     'Success',
                //     'Authenticated successfully',
                //     auth()->user()->createToken('SECRETTOKENOFWICMEHASBEENCREATEDHEREDONEWITHIT')->plainTextToken,
                //     auth()->user()
                // ));
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
                return redirect()->away(env('FRONTEND_REDIRECT_URL'));
            }
        } catch (\Exception $error) {
            return redirect()->away(env('FRONTEND_REDIRECT_URL'));
        }
    }
}
