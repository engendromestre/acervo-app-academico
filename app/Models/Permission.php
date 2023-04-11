<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as OriginalPermission;

class Permission extends OriginalPermission
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
            'id'=> ['title'=>'ID','dataType'=>'','create'=>false,'read'=>false,'update'=>true],
            'name'=> ['title'=>'Name','dataType'=>'text','create'=>true,'read'=>true,'update'=>true,'position'=>'0'],
            'guard_name'=> ['title'=>'Guard Name','dataType'=>'text','create'=>false,'read'=>false,'update'=>false,'position'=>'1']
        ];
    }

    /**
     * Defines the feedbacks of the validation rules
     */
    public function rules()
    {
        $unique = isset($this->id) ? "unique:permissions,name,{$this->id}" : "unique:permissions,name";
        return [
            'name' => ["required",$unique,"max:50"],
            'guard_name' => ['required', 'max:25']
        ];
    }
}