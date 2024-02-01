<?php

namespace App\Http\Controllers;

use App\Enums\DayWeek;
use App\Models\Buffet;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function __construct(
        protected Buffet $buffet
    )
    {
    }

    public function dashboard() {
        $user = auth()->user();
        if($user->isBuffet()) {
            $buffet = Buffet::find($user->buffet_id);
            return redirect()->route('buffet.dashboard', ['buffet'=>$buffet->slug]);
        }
        if($user->isOwner()) {
            $buffets = $this->buffet->where('owner_id', $user->id)->get();
            return view('dashboard', ['buffets'=>$buffets]);
        }
        // Não tem permissão
        abort(401);
    }
}
