<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Return Fields scrutcture for Forms and List Components
     */
    public function getFields()
    {
        return [
            'id'=> ['title'=>'ID','dataType'=>'','initValue'=>'','create'=>false,'read'=>false,'update'=>true],
            'name'=> ['title'=>'User','dataType'=>'text','initValue'=>'','create'=>true,'read'=>true,'update'=>true,'position'=>'0'],
            'email'=> ['title'=>'E-mail','dataType'=>'email','initValue'=>'','create'=>true,'read'=>true,'update'=>true,'position'=>'1'],
            'role'=> ['title'=>'Role','dataType'=>'checkbox','initValue'=>false,'create'=>true,'read'=>true,'update'=>true,'position'=>'2']
        ];
    }

    /**
     * Defines the feedbacks of the validation rules
     */
    public function rules()
    {
        $uniqueName = isset($this->id) ? "unique:users,name,{$this->id}" : "unique:users,name";
        $uniqueEmail = isset($this->id) ? "unique:users,email,{$this->id}" : "unique:users,email";
        return [
            'name' => ["required",$uniqueName,"min:5","max:50"],
            'email' => ["required","email",$uniqueEmail,"min:10","max:150"],
            'role' => ["required"]
        ];
    }
}
