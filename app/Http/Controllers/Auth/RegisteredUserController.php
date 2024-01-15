<?php

namespace App\Http\Controllers\Auth;

use App\Enums\BuffetStatus;
use App\Enums\DocumentType;
use App\Http\Controllers\Controller;
use App\Models\Buffet;
use App\Models\BuffetSubscription;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
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

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'document' => [
                'required',
                'string',
                'cpf_ou_cnpj',
                'unique:users,document'
            ],
            // 'document_type' => [
            //     'required',
            //     Rule::in(array_column(DocumentType::cases(), 'name'))
            // ],
            'phone1'=> ['required', 'string', 'celular_com_ddd']
        ]);

        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();
        if(!$buffet || !$buffet_slug || $buffet->status == BuffetStatus::UNACTIVE->name) {
            return redirect()->back()->withErrors('buffet', "Buffet not found")->withInput();
        }
        $buffet_subscription = BuffetSubscription::where('buffet_id', $buffet->id)->with('subscription')->latest()->first();
        if($buffet_subscription->expires_in < Carbon::now()) {
            return redirect()->back()->withErrors('buffet', "Buffet is not active")->withInput();
        }

        $mail_exists = User::where('buffet_id', $buffet->id)->where('email', $request->email)->first();
        if($mail_exists) {
            return redirect()->back()->withErrors('email', 'Email already exists')->withInput();
        }
        $document_exists = User::where('buffet_id', $buffet->id)->where('document', $request->document)->first();
        if($document_exists) {
            return redirect()->back()->withErrors('document', 'Document already exists')->withInput();
        }
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'document'=>$request->document,
            'document_type'=>DocumentType::CPF->name,
            'buffet_id'=>$buffet->id
        ]);

        $user->assignRole($buffet_subscription->subscription->slug.'.user');

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
