<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:role list', ['only' => ['index', 'show']]);
        $this->middleware('can:role create', ['only' => ['create', 'store']]);
        $this->middleware('can:role edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:role delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $roles = (new Role)->newQuery();
        $user = Auth::user();
        
        $allPermissions = Permission::select('id','name')->get();
        if($user['name'] =='Super Admin') {
            $roles->latest();
        } else {
            
        }
        $roles = $roles->when(
            $request->q,function($query,$q)
            {
                $query->where('name','LIKE','%'. $q .'%')
                      ->orWhere('guard_name','LIKE','%'. $q .'%');
            }
        )->paginate(8);
        
        // permissoes dos papeis
        $roleHasPermissions = DB::table('role_has_permissions')->get();
        $fields = (new Role)->getFields();
        return Inertia::render('Admin/Role/Index', [
            'fields' => $fields,
            'filters' => $request->only(['q']),
            'data' => $roles,
            'permissions' => $allPermissions,
            'roleHasPermissions' => $roleHasPermissions,
            'can' => [
                'create' => Auth::user()->can('role create'),
                'edit' => Auth::user()->can('role edit'),
                'delete' => Auth::user()->can('role delete'),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $role = new Role();
        $request['guard_name'] = 'web';
        $request->validate($role->rules());
        $role = $role->create($request->all());
        $permissions = $request->permissions ?? [];
        $role->givePermissionTo($permissions);
        return redirect()->route('role.index',['page' => $request->input('page')])->with('message', 'Created Successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     */
    public function update(Request $request, Role $role)
    {
        $request['guard_name'] = 'web';
        $request->validate( $role->rules() );
        $role->update($request->all());
        $permissions = $request->permissions ?? [];
        $role->syncPermissions($permissions);
        return redirect()->route('role.index',['page' => $request->input('page')])->with('message', 'Updated Successfully');
    }

     /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     */
    public function destroy(Request $request,Role $role)
    {
        $role->delete();
        return redirect()->route('role.index',['page' => $request->input('page')])->with('message', 'Deleted Successfully');
    }
}
