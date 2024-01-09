<?php

namespace App\Http\Controllers;

use App\Models\Buffet;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function dashboard() {
        $user = auth()->user();
        if($user->isBuffet()) {
            $buffet = Buffet::find($user->buffet_id);
            return redirect()->route('dashboard_buffet', ['buffet'=>$buffet->slug]);
        }
        if($user->isOwner()) {
            return view('dashboard');
        }
        // Não tem permissão
        abort(401);
    }
}
