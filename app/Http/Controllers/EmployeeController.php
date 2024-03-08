<?php

namespace App\Http\Controllers;

use App\Enums\UserStatus;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Mail\UserCreated;
use App\Models\Address;
use App\Models\Buffet;
use App\Models\BuffetSubscription;
use App\Models\Phone;
use App\Models\SubscriptionConfiguration;
use App\Models\User;
use Carbon\Carbon;
use Hashids\Hashids;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{
    protected Hashids $hashids;

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
        $this->hashids = new Hashids(config('app.name'));
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

        $configurations = SubscriptionConfiguration::where('subscription_id', $buffet_subscription->subscription_id)->get()->first();

        $employees = $this->user
            ->with(['user_phone2', 'user_phone1', 'user_address'])
            ->where('buffet_id', $buffet->id)
            ->withoutRole($buffet_subscription->subscription->slug.'.user')
            ->paginate($request->get('per page', 5), ['*'], 'page', $request->get('page', 1));

        $this->authorize('viewAnyEmployee', [User::class, $buffet]);

        $buffet_subscription = BuffetSubscription::where('buffet_id', $buffet->id)->with('subscription')->latest()->first();
        if($buffet_subscription->expires_in < Carbon::now()) {
            return redirect()->back()->withErrors(['buffet'=> "Buffet is not active"])->withInput();
        }

        $configurations = SubscriptionConfiguration::where('subscription_id', $buffet_subscription->subscription_id)->get()->first();
        $roles = $this->role->where('name', 'like', $buffet_subscription->subscription->slug.'.%')->get();

        $total = $this->user->where('buffet_id',$buffet->id)->withoutRole($buffet_subscription->subscription->slug.'.user')->where('status', UserStatus::ACTIVE->name)->get();

        return view('employee.index', ['buffet'=>$buffet, 'employees'=>$employees, 'configurations'=>$configurations, 'total'=>count($total), 'buffet_subscription'=>$buffet_subscription, 'roles'=>$roles]); 
    }
    
    public function create(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['generic_error'=>'Buffet not found'])->withInput();
        }

        $buffet_subscription = BuffetSubscription::where('buffet_id', $buffet->id)->with('subscription')->latest()->first();
        if($buffet_subscription->expires_in < Carbon::now()) {
            return redirect()->back()->withErrors(['generic_error'=> "Buffet is not active"])->withInput();
        }
        $configurations = SubscriptionConfiguration::where('subscription_id', $buffet_subscription->subscription_id)->get()->first();

        $employees = $this->user
            ->where('buffet_id', $buffet->id)
            ->withoutRole($buffet_subscription->subscription->slug.'.user')
            ->get();
        
        if(count($employees) >= $configurations['max_employees']) {
            return redirect()->back()->withErrors(['generic_error'=> 'Não é permitido cadastrar mais funcionarios neste plano.'])->withInput();
        }

        $roles = $this->role->where('name', 'like', $buffet_subscription->subscription->slug.'.%')->get();
        
        $this->authorize('createEmployee', [User::class, $buffet]);

        return view('employee.create', ['buffet'=>$buffet, 'roles'=>$roles, 'buffet_subscription'=>$buffet_subscription]);
    }

    public function store(StoreEmployeeRequest $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['generic_error'=>'Buffet não encontrado.'])->withInput();
        }

        $this->authorize('createEmployee', [User::class, $buffet]);

        $buffet_subscription = $this->buffet_subscription->where('buffet_id', $buffet->id)->with('subscription')->latest()->first();
        if($buffet_subscription->expires_in < Carbon::now()) {
            return redirect()->back()->withErrors(['buffet'=> "Este buffet não está mais ativo."])->withInput();
        }

        $configurations = SubscriptionConfiguration::where('subscription_id', $buffet_subscription->subscription_id)->get()->first();

        $employees = $this->user
            ->where('buffet_id', $buffet->id)
            ->withoutRole($buffet_subscription->subscription->slug.'.user')
            ->get();
        
        if(count($employees) >= $configurations['max_employees']) {
            return redirect()->back()->withErrors(['generic_error'=> 'Não é permitido cadastrar mais funcionarios neste plano.'])->withInput();
        }
        
        $mail_exists = $this->user->where('buffet_id', $buffet->id)->where('email', $request->email)->first();
        if($mail_exists) {
            return redirect()->back()->withErrors(['email'=>'Este e-mail já esta cadastrado.'])->withInput();
        }
        $document_exists = $this->user->where('buffet_id', $buffet->id)->where('document', $request->document)->first();
        if($document_exists) {
            return redirect()->back()->withErrors(['document'=> 'Este documento já está cadastrado.'])->withInput();
        }
        $role_exists = $this->role->where('name', $request->role)->get()->first();
        if(!$role_exists) {
            return redirect()->back()->withErrors(['role'=> 'Cargo não encontrado.'])->withInput();
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

        return redirect()->back()->with(['success'=>'Funcionário cadastrado com sucesso!']);
    }
    
    public function edit(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        $buffet_subscription = BuffetSubscription::where('buffet_id', $buffet->id)->with('subscription')->latest()->first();
        if($buffet_subscription->expires_in < Carbon::now()) {
            return redirect()->back()->withErrors(['buffet'=> "Buffet is not active"])->withInput();
        }

        $roles = $this->role->where('name', 'like', $buffet_subscription->subscription->slug.'.%')->get();

        $user_id = $this->hashids->decode($request->employee)[0];

        $employee = $this->user
            ->with(['user_phone2', 'user_phone1', 'user_address'])
            ->where('buffet_id', $buffet->id)
            ->find($user_id); 
            
        if(!$employee){
            return redirect()->back()->withErrors(['user'=>'Funcionário não encontrado.'])->withInput();
        }
        $this->authorize('updateEmployee', [User::class, $employee, $buffet]);

        return view('employee.update', ['buffet'=>$buffet, 'employee'=>$employee, 'roles'=>$roles, 'buffet_subscription'=>$buffet_subscription]);
    }

    public function update(UpdateEmployeeRequest $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        $buffet_subscription = BuffetSubscription::where('buffet_id', $buffet->id)->with('subscription')->latest()->first();
        if($buffet_subscription->expires_in < Carbon::now()) {
            return redirect()->back()->withErrors(['buffet'=> "Buffet is not active"])->withInput();
        }

        $roles = $this->role->where('name', 'like', $buffet_subscription->subscription->slug.'.%')->get();

        $user_id = $this->hashids->decode($request->employee)[0];

        $employee = $this->user
            ->with(['user_phone2', 'user_phone1', 'user_address'])
            ->where('buffet_id', $buffet->id)
            ->find($user_id); 

        if(!$employee){
            return redirect()->back()->withErrors(['user'=>'Funcionário não encontrado.'])->withInput();
        }
        $this->authorize('updateEmployee', [User::class, $employee, $buffet]);
            
        if($request->phone1) {
            if($employee->phone1) {
                $this->phone->find($employee->phone1)->update(['number'=>$request->phone1]);
            } else {
                $employee->update(['phone1'=>$this->phone->create(['number'=>$request->phone1])->id]);
            }
        }
        if($request->phone2) {
            if($employee->phone2) {
                $this->phone->find($employee->phone2)->update(['number'=>$request->phone2]);
            } else {
                $employee->update(['phone2'=>$this->phone->create(['number'=>$request->phone2])->id]);
            }
        }

        $employee->update($request->except(['phone1', 'phone2', 'role']));

        if($employee->roles[0]->name !== $request->role) {
            $employee->syncRoles($request->role);
        }

        return redirect()->route('employee.edit', ['buffet'=>$buffet->slug, 'employee'=>$employee->hashed_id])->with(['success'=>'Funcionário atualizado com sucesso!']);
    }
    
    public function show(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['generic_error'=>'Buffet não encontrado.'])->withInput();
        }

        $user_id = $this->hashids->decode($request->employee)[0];

        $buffet_subscription = BuffetSubscription::where('buffet_id', $buffet->id)->with('subscription')->latest()->first();
        if($buffet_subscription->expires_in < Carbon::now()) {
            return redirect()->back()->withErrors(['buffet'=> "Buffet is not active"])->withInput();
        }

        $employee = $this->user
            ->with(['user_phone2', 'user_phone1', 'user_address'])
            ->where('buffet_id', $buffet->id)
            ->find($user_id); 
        
        $this->authorize('viewEmployee', [User::class, $employee, $buffet]);

        if(!$employee){
            return redirect()->back()->withErrors(['user'=>'Funcionário não encontrado.'])->withInput();
        }

        return view('employee.show', ['buffet'=>$buffet, 'employee'=>$employee, 'buffet_subscription'=>$buffet_subscription]);
    }

    public function destroy(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        $buffet_subscription = BuffetSubscription::where('buffet_id', $buffet->id)->with('subscription')->latest()->first();
        if($buffet_subscription->expires_in < Carbon::now()) {
            return redirect()->back()->withErrors(['buffet'=> "Buffet is not active"])->withInput();
        }

        $user_id = $this->hashids->decode($request->employee)[0];

        $employee = $this->user
            ->with(['user_phone2', 'user_phone1', 'user_address'])
            ->where('buffet_id', $buffet->id)
            ->find($user_id); 

        if(!$employee){
            return redirect()->back()->withErrors(['user'=>'Funcionário não encontrado.'])->withInput();
        }

        $this->authorize('deleteEmployee', [User::class, $employee, $buffet]);

        $employee->syncRoles($buffet_subscription->subscription->slug.'.user');

        return redirect()->back()->with(['success'=>"Usuario 'deletado' com sucesso."]);
    }

}
