<?php

namespace App\Http\Controllers\Auth;

use App\Enums\BuffetStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Buffet;
use App\Models\BuffetSubscription;
use App\Models\OneTimeToken;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request)
    {   
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();
        if(!$buffet || !$buffet_slug || $buffet->status == BuffetStatus::UNACTIVE->name) {
            return redirect(RouteServiceProvider::NOT_FOUND);
            //redirecionar para a landing page do sistema administrativo
        } else {
            // buffet exists
            $buffet_subscription = BuffetSubscription::where('buffet_id', $buffet->id)->with('subscription')->latest()->first();
            if($buffet_subscription->expires_in < Carbon::now()) {
                return redirect(RouteServiceProvider::NOT_FOUND);
            }
            return view('auth.login', compact('buffet'));
        }
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // $buffet_slug = $request->buffet;
        // $buffet = Buffet::where('slug', $buffet_slug)->first();
        // if(!$buffet || !$buffet_slug || $buffet->status == BuffetStatus::UNACTIVE->name) {
        //     return redirect()->back();
        // }

        $request->authenticate();

        $request->session()->regenerate();

        $user = auth()->user();
        if($user->buffet_id === null) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }
        // Caso seja um buffet
        $buffet = Buffet::find($user->buffet_id);
        return redirect()->route('buffet.dashboard', $buffet->slug);

    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function login_api(Request $request) {
        $token = $request->token;

        $exists_token = OneTimeToken::where('token',$token)->first();
        if($exists_token) {
            // verificar se ainda nao expirou
        }

        try {
            $payload = JWTAuth::setToken($token)->getPayload();

            $email = $payload['email'];
            $name = $payload['name'];
            $document = $payload['document'];
            
            $user = User::where('email', $email)->where('name', $name)->where('document', $document)->where('buffet_id', null)->first();
            Auth::login($user);
            OneTimeToken::create([
                'token'=>$token,
                'user_id'=>$user->id,
                'expires_at'=>date('Y-m-d H:i:s', $payload['exp']),
            ]);

            return redirect()->route('dashboard');
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            dd($e);
            // return redirect()->route('login')->withErrors(['error' => 'Token inválido ou expirado']);
            dd("Erro");
        }
    }
}
