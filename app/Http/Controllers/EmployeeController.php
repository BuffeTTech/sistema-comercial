<?php

namespace App\Http\Controllers;

use App\Enums\UserStatus;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Mail\UserCreated;
use App\Models\Address;
use App\Models\Buffet;
use App\Models\BuffetSubscription;
use App\Models\Phone;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{
    public function __construct(
        protected User $user, 
        protected Buffet $buffet, 
        protected BuffetSubscription $buffet_subscription, 
        protected Address $addrees, 
        protected Phone $phone, 
        protected Role $role,
        protected Permission $permission
    )
    {
        
    }
    public function index(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet not found'])->withInput();
        }

        $buffet_subscription = BuffetSubscription::where('buffet_id', $buffet->id)->with('subscription')->latest()->first();
        if($buffet_subscription->expires_in < Carbon::now()) {
            return redirect()->back()->withErrors(['buffet'=> "Buffet is not active"])->withInput();
        }

        $employees = $this->user
            ->with(['user_phone2', 'user_phone1', 'user_address'])
            ->where('buffet_id', $buffet->id)
            ->withoutRole($buffet_subscription->subscription->slug.'.user')
            ->paginate($request->get('per page', 5), ['*'], 'page', $request->get('page', 1));

        return view('employee.index', ['buffet'=>$buffet, 'employees'=>$employees]); 
    }
    
    public function create(Request $request){

        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet not found'])->withInput();
        }

        $buffet_subscription = BuffetSubscription::where('buffet_id', $buffet->id)->with('subscription')->latest()->first();
        if($buffet_subscription->expires_in < Carbon::now()) {
            return redirect()->back()->withErrors(['buffet'=> "Buffet is not active"])->withInput();
        }

        $roles = $this->role->where('name', 'like', $buffet_subscription->subscription->slug.'.%')->get();

        return view('employee.create', ['buffet'=>$buffet, 'roles'=>$roles, 'buffet_subscription'=>$buffet_subscription]);
    }

    public function store(StoreEmployeeRequest $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet not found'])->withInput();
        }
        $buffet_subscription = $this->buffet_subscription->where('buffet_id', $buffet->id)->with('subscription')->latest()->first();
        if($buffet_subscription->expires_in < Carbon::now()) {
            return redirect()->back()->withErrors(['buffet'=> "Buffet is not active"])->withInput();
        }
        
        $mail_exists = $this->user->where('buffet_id', $buffet->id)->where('email', $request->email)->first();
        if($mail_exists) {
            return redirect()->back()->withErrors(['email'=>'Email already exists'])->withInput();
        }
        $document_exists = $this->user->where('buffet_id', $buffet->id)->where('document', $request->document)->first();
        if($document_exists) {
            return redirect()->back()->withErrors(['document'=> 'Document already exists'])->withInput();
        }
        $role_exists = $this->role->where('name', $request->role)->get()->first();
        if(!$role_exists) {
            return redirect()->back()->withErrors(['role'=> 'Role not found'])->withInput();
        }

        $phone = $this->phone->create(['number'=>$request->phone1]);

        $password = Str::password(length: 12, symbols: false);

        $user = $this->user->create([
            'name' => $request->name,
            'email' => $request->email,
            'document'=>$request->document,
            'document_type'=>$request->document_type,
            'phone1'=>$phone->id,
            'password' => Hash::make($password),
            'status'=>UserStatus::ACTIVE->name,
            'email_verified_at' => now(),
            'buffet_id' => $buffet->id
        ]);
        $user->assignRole($role_exists->name);

        event(new Registered($user));

        // // Envio de emails funcionando!

        Mail::to($request->email)->queue(new UserCreated(password: $password, user: $user));

        return back()->with('success', 'Usuário cadastrado com sucesso!');
    }
    
    public function edit(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet not found'])->withInput();
        }

        $employee = $this->user
            ->with(['user_phone2', 'user_phone1', 'user_address'])
            ->where('buffet_id', $buffet->id)
            ->find($request->employee); 

        if(!$employee){
            return redirect()->back()->withErrors(['user'=>'user not found'])->withInput();
        }

        return view('employee.update', ['buffet'=>$buffet, 'employee'=>$employee]);
        // $this->authorize('update', Employee::class);
        
        // $commercial = $this->commercial->with(['user.user_phone1','user.user_phone2', 'user.user_address'])->find($request->commercial);
        // if(!$commercial) {
        //     return back()->with('errors', 'User not found');
        // }
        
        // return view('commercial.update', compact(['commercial']))->with('success', 'Usuário deletado com sucesso');
    }

    public function update(){

    }
    
    public function show(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet not found'])->withInput();
        }

        $employee = $this->user
            ->with(['user_phone2', 'user_phone1', 'user_address'])
            ->where('buffet_id', $buffet->id)
            ->find($request->employee); 

        if(!$employee){
            return redirect()->back()->withErrors(['user'=>'user not found'])->withInput();
        }

        return view('employee.show', ['buffet'=>$buffet, 'employee'=>$employee]);
    }

    public function destroy(){

    }

}
