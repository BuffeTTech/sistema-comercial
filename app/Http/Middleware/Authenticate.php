<?php

namespace App\Http\Middleware;

use App\Models\Buffet;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();
        if(!$buffet || !$buffet_slug) {
            return null;
        }
            // buffet exists
        return $request->expectsJson() ? null : route('login', ['buffet'=>$buffet_slug]);
    }
}
