<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Buffet;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request)
    {        
        $buffet_slug = $request->slug;
        $buffet = Buffet::where('slug', $buffet_slug)->first();
        if(!$buffet || !$buffet_slug) {
            return redirect()->route('home');
            //redirecionar para a landing page do sistema administrativo
        } else {
            // buffet exists
            return view('auth.login', compact('buffet'));
        }
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = auth()->user();
        if($user->buffet_id === null) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }
        // Caso seja um buffet
        $buffet = Buffet::find($user->buffet_id);
        return redirect()->route('dashboard_buffet', $buffet->slug);

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
}
