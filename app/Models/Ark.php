<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ark extends Model
{
    use HasFactory;

    protected $fillable = [
        'who',
        'what',
        'when',
        'where',
        'how',
        'why',
        'contact',
        'address'
    ];
}
