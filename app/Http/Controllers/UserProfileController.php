<?php

namespace App\Http\Controllers;

use App\Models\Buffet;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserProfileController extends Controller
{
    public function show(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }
        return view('pages.user-profile', compact('buffet'));
    }

    public function update(Request $request)
    {
        $attributes = $request->validate([
            'username' => ['required','max:255', 'min:2'],
            'firstname' => ['max:100'],
            'lastname' => ['max:100'],
            'email' => ['required', 'email', 'max:255',  Rule::unique('users')->ignore(auth()->user()->id),],
            'address' => ['max:100'],
            'city' => ['max:100'],
            'country' => ['max:100'],
            'postal' => ['max:100'],
            'about' => ['max:255']
        ]);

        auth()->user()->update([
            'username' => $request->get('username'),
            'firstname' => $request->get('firstname'),
            'lastname' => $request->get('lastname'),
            'email' => $request->get('email') ,
            'address' => $request->get('address'),
            'city' => $request->get('city'),
            'country' => $request->get('country'),
            'postal' => $request->get('postal'),
            'about' => $request->get('about')
        ]);
        return back()->with('succes', 'Profile succesfully updated');
    }
}
