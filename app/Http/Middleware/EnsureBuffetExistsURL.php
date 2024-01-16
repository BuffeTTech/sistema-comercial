<?php

namespace App\Http\Middleware;

use App\Enums\BuffetStatus;
use App\Models\Buffet;
use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBuffetExistsURL
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();
        
        if(!$buffet || !$buffet_slug || $buffet->status == BuffetStatus::UNACTIVE->name) {
            if(auth()->user()) {
                dd('aaa');
                return redirect()->intended(RouteServiceProvider::HOME);
            }
            dd('bbb');  
            return route('home');
        }

        return $next($request);
    }
}
