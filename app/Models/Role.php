<?php

namespace App\Models;

use Spatie\Permission\Models\Role as OriginalRole;

class Role extends OriginalRole
{
    protected $fillable = [
        'name',
        'guard_name',
        'updated_at',
        'created_at',
    ];

    /**
     * Return Fields scrutcture for Forms and List Components
     */
    public function getFields()
    {
        return [
            'id'=> ['title'=>'ID','dataType'=>'','initValue'=>'','create'=>false,'read'=>false,'update'=>true],
            'name'=> ['title'=>'Role','dataType'=>'text','initValue'=>'','create'=>true,'read'=>true,'update'=>true,'position'=>'0'],
            'guard_name'=> ['title'=>'Guard Name','dataType'=>'text','initValue'=>'','create'=>false,'read'=>false,'update'=>false,'position'=>'1'],
            'permissions'=> ['title'=>'Permissions','dataType'=>'checkbox','initValue'=>[],'create'=>true,'read'=>false,'update'=>true,'position'=>'1']
        ];
    }

    /**
     * Defines the feedbacks of the validation rules
     */
    public function rules()
    {
        $unique = isset($this->id) ? "unique:roles,name,{$this->id}" : "unique:roles,name";
        return [
            'name' => ["required",$unique,"max:50"],
            'guard_name' => ['required', 'max:25']
        ];
    }
}