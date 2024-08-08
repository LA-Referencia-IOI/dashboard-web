<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blockchain extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'type',
        'number_nodes',
        'local',
        'status',
        'description',
        'url',
        'enodes'
    ];
}



