<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Return Fields scrutcture for Forms and List Components
     */
    public function getFields()
    {
        return [
            'id' => ['title' => 'ID', 'dataType' => '', 'initValue' => '', 'create' => false, 'read' => false, 'update' => true],
            'name' => ['title' => 'Name', 'dataType' => 'text', 'initValue' => '', 'create' => true, 'read' => true, 'update' => true, 'position' => '0'],
        ];
    }

    /**
     * Defines the feedbacks of the validation rules
     */
    public function rules()
    {
        $unique = isset($this->id) ? "unique:collections,name,{$this->id}" : "unique:collections,name";
        return [
            'name' => ["required", $unique, "max:150"]
        ];
    }
}
