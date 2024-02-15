<?php

namespace App\Http\Controllers;

// use App\Http\Requests\RegisterRequest;

use App\Enums\BuffetStatus;
use App\Models\Buffet;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function create(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();
        if(!$buffet || !$buffet_slug || $buffet->status == BuffetStatus::UNACTIVE->name) {
            return redirect(RouteServiceProvider::NOT_FOUND);
            //redirecionar para a landing page do sistema administrativo
        } else {
            // buffet exists
            return view('auth.register', compact('buffet'));
        }
    }

    public function store()
    {
        $attributes = request()->validate([
            'username' => 'required|max:255|min:2',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:5|max:255',
            'terms' => 'required'
        ]);
        $user = User::create($attributes);
        auth()->login($user);

        return redirect('/dashboard');
    }
}
