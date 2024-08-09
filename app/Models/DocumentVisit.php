<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'visited_at',
        'ip_address',
    ];
}
