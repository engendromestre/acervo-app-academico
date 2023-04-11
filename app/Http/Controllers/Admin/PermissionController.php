<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:permission list', ['only' => ['index', 'show']]);
        $this->middleware('can:permission create', ['only' => ['create', 'store']]);
        $this->middleware('can:permission edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:permission delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function index(Request $request)
    {
        $permissions = (new Permission)->newQuery();
        $permissions->latest();
        $permissions = $permissions->when(
            $request->q,function($query,$q)
            {
                $query->where('name','LIKE','%'. $q .'%')
                      ->orWhere('guard_name','LIKE','%'. $q .'%');
            }
        )->paginate(8);
        $fields = (new Permission)->getFields();

        return Inertia::render('Admin/Permission/Index', [
            'fields' => $fields,
            'filters' => $request->only(['q']),
            'data' => $permissions,
            'can' => [
                'create' => Auth::user()->can('permission create'),
                'edit' => Auth::user()->can('permission edit'),
                'delete' => Auth::user()->can('permission delete'),
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
        $permission = new Permission();
        $request['guard_name'] = 'web';
        $request->validate($permission->rules());
        $permission->create($request->all());
        return redirect()->route('permission.index',['page' => $request->input('page')])->with('message', 'Created Successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Permission  $permission
     */
    public function update(Request $request, Permission $permission)
    {
        $request['guard_name'] = 'web';
        $request->validate( $permission->rules() );
        $permission->update($request->all());
        return redirect()->route('permission.index',['page' => $request->input('page')])->with('message', 'Updated Successfully');
    }

     /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Permission  $permission
     */
    public function destroy(Request $request,Permission $permission)
    {
        $permission->delete();
        return redirect()->route('permission.index',['page' => $request->input('page')])->with('message', 'Deleted Successfully');
    }
}
