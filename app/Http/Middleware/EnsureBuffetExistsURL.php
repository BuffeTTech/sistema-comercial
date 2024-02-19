<?php

namespace App\Http\Middleware;

use App\Enums\BuffetStatus;
use App\Models\Buffet;
use App\Models\BuffetSubscription;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                // $buffet_subscription = BuffetSubscription::where('buffet_id', $buffet->id)->with('subscription')->latest()->first();
                // if($buffet_subscription->expires_in < Carbon::now()) {
                //     return redirect(RouteServiceProvider::NOT_FOUND);
                // }

                // Implementar o loggout aqui
                return redirect()->intended(RouteServiceProvider::HOME);
            }
            return redirect(RouteServiceProvider::NOT_FOUND);
        }
        
        return $next($request);
    }
}
