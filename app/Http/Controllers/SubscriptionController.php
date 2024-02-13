<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionConfiguration;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SubscriptionController extends Controller
{
    public function __construct(
        protected Subscription $subscription,
        protected Permission $permission,
        protected Role $role,
        protected SubscriptionConfiguration $configuration,
    )
    {
    }

    // API
    public function create_subscription(Request $request): JsonResponse {
        //return response()->json($request->subscription['name']);

        /*$request->validate([
            'name'=>[],
            'slug'=>[],
            'description'=>[],
            'price'=>[],
            'discount'=>[],
            'status'=>[],
        ]);*/

        $subscription_exists = $this->subscription->where('slug', $request->subscription['slug'])->get()->first();
        if($subscription_exists) {
            return response(403)->json();
        }

        $subscription = $this->subscription->create([
            'name'=>$request->subscription['name'],
            'slug'=>$request->subscription['slug'],
            'description'=>$request->subscription['description'],
            'price'=>$request->subscription['price'],
            //'discount'=>$request->subscription['discount'],
            'status'=>$request->subscription['status'],
        ]);

        $configuration = $this->configuration->create([
            "max_employees"=>$request->configuration['max_employees'],
            "max_food_photos"=>$request->configuration['max_food_photos'],
            "max_decoration_photos"=>$request->configuration['max_decoration_photos'],
            "max_recommendations"=>$request->configuration['max_recommendations'],
            "subscription_id"=>$subscription->id,
        ]);

        return response(status: 201)->json([$subscription, $configuration]);
    }
    public function insert_role_in_permission(Request $request){
        $permission = $request->permission;
        $roles = $request->roles;

        $permission_eloquent = $this->permission->where('name', $permission['name'])->get()->first();
        if(!$permission_eloquent) {
            $this->permission->create([
                'name'=>$permission['name']
            ]);
        }

        foreach($roles as $role) {
            $role_eloquent = $this->role->where('name', $role['name'])->get()->first();
            if($role_eloquent) {
                $role_eloquent->givePermissionTo($permission['name']);
            }
        }

        return response()->json();
    }
    public function remove_role_from_permission(Request $request) {
        $permission = $request->permission;
        $role = $request->role;
        $role_eloquent = $this->role->where('name', $role['name'])->get()->first();
        $permission_eloquent = $this->permission->where('name', $permission['name'])->get()->first();
        if(!$role_eloquent || !$permission_eloquent) {
            return response(422)->json();
        }
        
        $role_eloquent->revokePermissionTo($permission['name']);
        
        return response()->json([$role_eloquent, $permission_eloquent, $role_eloquent->hasPermissionTo($permission['name'])]);
    }

    public function create_role(Request $request) {
        $role_exists = $this->role->where('name', $request->role['name'])->get()->first();
        if($role_exists) {
            return response(403)->json();
        }
        $role = $this->role->create([
            'name'=>$request->role['name'],
        ]);

        return response(status: 201)->json($role);
    }
    public function create_permission(Request $request) {
        $permission_exists = $this->permission->where('name', $request->permission['name'])->get()->first();
        if($permission_exists) {
            return response(403)->json();
        }
        $permission = $this->permission->create([
            'name'=>$request->permission['name'],
        ]);

        return response(status: 201)->json($permission);
    }

    public function create_many_permission(Request $request) {
        $data = $request->data; // array de permissoes laravel permission
        $response = [];
        return response(status: 201)->json($data);
        foreach ($data as $permission) {
            $permission_exists = $this->permission->where('name', $permission['permission']['name'])->get()->first();
            $permi = $permission['permission']['name'];
            if(!$permission_exists) {
                $permission_exists = $this->permission->create([
                    'name'=>$permi,
                ]);
            }
            foreach($permission['roles'] as $role) {
                $role_eloquent = $this->role->where('name', $role['name'])->get()->first();
                if($role_eloquent) {
                    $role_eloquent->givePermissionTo($permission_exists['name']);
                }
            }
            array_push($response, $permission);
        }
        return response(status: 201)->json($response);
    }


    // Sistema
    public function change_subscription(){}
}
