<?php

namespace App\Http\Controllers;

use App\Models\Buffet;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        if (view()->exists("pages.{$request->page}")) {
            return view("pages.{$request->page}", compact('buffet'));
        }

        return abort(404);
    }

    public function vr(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }
        return view("pages.virtual-reality", compact('buffet'));
    }

    public function rtl(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }
        return view("pages.rtl", compact('buffet'));
    }

    public function profile(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }
        return view("pages.profile-static", compact('buffet'));
    }

    public function signin(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }
        return view("pages.sign-in-static", compact('buffet'));
    }

    public function signup(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }
        return view("pages.sign-up-static", compact('buffet'));
    }
}
