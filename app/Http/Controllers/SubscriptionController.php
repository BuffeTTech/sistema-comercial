<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
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
        protected Role $role
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

        $subscription = $this->subscription->create([
            'name'=>$request->subscription['name'],
            'slug'=>$request->subscription['slug'],
            'description'=>$request->subscription['description'],
            'price'=>$request->subscription['price'],
            //'discount'=>$request->subscription['discount'],
            'status'=>$request->subscription['status'],
        ]);

        return response(status: 201)->json($subscription);
    }
    public function insert_role_in_permission(Request $request){
        $permission = $request->permission;
        $role = $request->role;
        $role_eloquent = $this->role->where('name', $role['name'])->get()->first();
        $permission_eloquent = $this->permission->where('name', $permission['name'])->get()->first();
        if(!$role_eloquent) {
            return response(422)->json();
        }
        if(!$permission_eloquent) {
            $this->permission->create([
                'name'=>$permission['name']
            ]);
        }

        $role_eloquent->givePermissionTo($permission['name']);

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
        $role = $this->role->create([
            'name'=>$request->role['name'],
        ]);

        return response(status: 201)->json($role);
    }
    public function create_permission(Request $request) {
        $permission = $this->permission->create([
            'name'=>$request->permission['name'],
        ]);

        return response(status: 201)->json($permission);
    }


    // Sistema
    public function change_subscription(){}
}
