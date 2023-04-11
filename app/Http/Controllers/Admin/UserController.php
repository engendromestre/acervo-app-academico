<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:user list', ['only' => ['index', 'show']]);
        $this->middleware('can:user create', ['only' => ['create', 'store']]);
        $this->middleware('can:user edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:user delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = (new User)->newQuery();
        $user = Auth::user();
        if($user['name'] =='Super Admin') {
            $users->latest();
        } else {
            $users->where('name','!=','Super Admin')->latest();
        }
        
        $users = $users->when(
            $request->q,
            function ($query, $q) {
                $query->where('name', 'LIKE', '%' . $q . '%')
                    ->orWhere('email', 'LIKE', '%' . $q . '%');
            }
        )->paginate(8);
        $fields = (new User)->getFields();

        // todas os papeis
        $allRoles = Role::select('id', 'name')->get();

        // papeis dos usuarios
        $modelHasRoles = DB::table('model_has_roles')->get();

        return Inertia::render('Admin/User/Index', [
            'fields' => $fields,
            'filters' => $request->only(['q']),
            'data' => $users,
            'roles' => $allRoles,
            'modelHasRoles' => $modelHasRoles,
            'can' => [
                'list' => Auth::user()->can('user list'),
                'create' => Auth::user()->can('user create'),
                'edit' => Auth::user()->can('user edit'),
                'delete' => Auth::user()->can('user delete'),
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
        $user = new User();
        $request['password'] = Hash::make(self::generatePassword());
        $request['remember_token'] = Str::random(60);
        $request->validate($user->rules());
        $user = $user->create($request->all());
        $role = $request->role ?? [];
        $user->assignRole($role);
        $resetLinkSent = new PasswordResetLinkController();
        $resetLinkSent->store($request);
        return redirect()->route('user.index', ['page' => $request->input('page')])->with('message', 'Created Successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User $user
     */
    public function update(Request $request, User $user)
    {
        $request->validate($user->rules());
        $user->update($request->all());
        $role = $request->role ?? [];
        $user->syncRoles($role);
        return redirect()->route('user.index', ['page' => $request->input('page')])->with('message', 'Updated Successfully');
    }

     /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User $user
     */
    public function destroy(Request $request,User $user)
    {
        $user->delete();
        return redirect()->route('user.index',['page' => $request->input('page')])->with('message', 'Deleted Successfully');
    }


    /** 
     * Gera senhas aleatórias
     *
     * @param int $qtyCaraceters quantidade de caracteres na senha, por padrão 8
     * @return String 
     */
    static function generatePassword($qtyCaraceters = 8)
    {
        //Letras minúsculas embaralhadas
        $smallLetters = str_shuffle('abcdefghijklmnopqrstuvwxyz');
        //Letras maiúsculas embaralhadas
        $capitalLetters = str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ');
        //Números aleatórios
        $numbers = (((date('Ymd') / 12) * 24) + mt_rand(800, 9999));
        $numbers .= 1234567890;
        //Caracteres Especiais
        $specialCharacters = str_shuffle('!@#$%*-');
        //Junta tudo
        $characters = $capitalLetters . $smallLetters . $numbers . $specialCharacters;
        //Embaralha e pega apenas a quantidade de caracteres informada no parâmetro
        $password = substr(str_shuffle($characters), 0, $qtyCaraceters);
        //Retorna a senha
        return $password;
    }
}
