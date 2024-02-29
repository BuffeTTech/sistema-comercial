<?php

namespace App\Http\Controllers;

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

    public function buffetAlegria(Request $request){
        $buffet = $this->buffet->where('slug', 'buffet-alegria')->get()->first();

        return view('buffetTest', ['buffet'=>$buffet]);
    }
    public function buffetTest(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet not found'])->withInput();
        }

        return view('buffetTest', ['buffet'=>$buffet]);
    }
}
