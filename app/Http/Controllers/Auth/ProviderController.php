<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Inertia\Inertia;

class ProviderController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
            if (User::where('email', $socialUser->getEmail())
                ->whereNull('provider_id')
                ->exists()
            ) {
                return redirect()->route('welcome')->withErrors(['email' => 'This email uses different method to login']);
            }

            $user = User::where([
                'provider' => $provider,
                'provider_id' => $socialUser->id

            ])->first();

            if (!$user) {
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'provider_token' => $socialUser->token,
                ]);
                $user->markEmailAsVerified();
            }


            $permissions = $user->permissions;
            if ($user->hasAnyPermission($permissions)) {
                // O usuário tem pelo menos uma das permissões
                Auth::login($user);
                return redirect()->route(RouteServiceProvider::HOME);
            }
            return Inertia::render('Auth/Login', [
                'status' => 'Please wait until your access permissions are defined. You will receive an email once this is completed'
            ]);
        } catch (\Exception $e) {
            return redirect()->route('login',['message' => 'Exception Error']);
        }
    }
}
