<?php

namespace App\Http\Controllers;

use App\Enums\UserStatus;
use App\Models\Address;
use App\Models\Buffet;
use App\Models\BuffetSubscription;
use App\Models\Phone;
use App\Models\SubscriptionConfiguration;
use App\Models\User;
use Carbon\Carbon;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    protected Hashids $hashids;

    public function __construct(
        protected User $user, 
        protected Buffet $buffet, 
        protected BuffetSubscription $buffet_subscription, 
        protected SubscriptionConfiguration $subscription_configuration, 
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

        $buffet_subscription = $this->buffet_subscription->where('buffet_id', $buffet->id)->with('subscription')->latest()->first();
        if($buffet_subscription->expires_in < Carbon::now()) {
            return redirect()->back()->withErrors(['buffet'=> "Buffet is not active"])->withInput();
        }

        $configurations = $this->subscription_configuration->where('subscription_id', $buffet_subscription->subscription_id)->get()->first();

        $users = $this->user
            ->with(['user_phone2', 'user_phone1', 'user_address'])
            ->where('buffet_id', $buffet->id)
            ->role($buffet_subscription->subscription->slug.'.user')
            ->paginate($request->get('per page', 5), ['*'], 'page', $request->get('page', 1));

        $this->authorize('viewAnyUser', [User::class, $buffet]);

        $buffet_subscription = $this->buffet_subscription->where('buffet_id', $buffet->id)->with('subscription')->latest()->first();
        if($buffet_subscription->expires_in < Carbon::now()) {
            return redirect()->back()->withErrors(['buffet'=> "Buffet is not active"])->withInput();
        }


        $configurations = $this->subscription_configuration->where('subscription_id', $buffet_subscription->subscription_id)->get()->first();

        $total = $this->user->where('buffet_id',$buffet->id)->role($buffet_subscription->subscription->slug.'.user')->where('status', UserStatus::ACTIVE->name)->get();
        
        $roles = $this->role->where('name', 'like', $buffet_subscription->subscription->slug.'.%')->get();

        return view('user.index', ['buffet'=>$buffet, 'users'=>$users, 'configurations'=>$configurations, 'total'=>count($total), 'buffet_subscription'=>$buffet_subscription, 'roles'=>$roles]); 
    }

    
    public function create(Request $request){
        abort(401);
    }

    public function store(Request $request){
        abort(401);
    }
    
    public function edit(Request $request){
        abort(401);
    }

    public function update(Request $request){
        abort(401);
    }
    
    public function show(Request $request){
        abort(401);
    }

    public function destroy(Request $request){
        abort(401);    
    }

    public function change_role(Request $request) {
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        $buffet_subscription = BuffetSubscription::where('buffet_id', $buffet->id)->with('subscription')->latest()->first();
        if($buffet_subscription->expires_in < Carbon::now()) {
            return redirect()->back()->withErrors(['buffet'=> "Buffet is not active"])->withInput();
        }

        $user_id = $this->hashids->decode($request->user)[0];

        $user = $this->user
            ->where('buffet_id', $buffet->id)
            ->find($user_id); 

        if(!$user){
            return redirect()->back()->withErrors(['user'=>'Usuário não encontrado.'])->withInput();
        }
        $this->authorize('changeUserRole', [User::class, $user, $buffet]);

        $total = $this->user->where('buffet_id',$buffet->id)->withoutRole($buffet_subscription->subscription->slug.'.user')->where('status', UserStatus::ACTIVE->name)->get();

        $configurations = $this->subscription_configuration->where('subscription_id', $buffet_subscription->subscription_id)->get()->first();

        if($request->role === $user->roles[0]->name) {
            return redirect()->back()->withErrors(['error'=>"Este usuário já possui esta permissão"]);
        }

        if($request->role !== $buffet_subscription->subscription->slug.'.user' && (count($total) >= $configurations['max_employees'] && $configurations['max_employees'] !== null)) {
            return redirect()->back()->withErrors(['error'=>"Este buffet ja possui o limite de funcionários cadastrados"]);
        }

        $user->syncRoles($request->role);

        return redirect()->back()->with(['success'=>'Usuário atualizado com sucesso!']);
    }
}
