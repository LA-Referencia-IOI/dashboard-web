<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServerResourceHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'disk_total_gb',
        'disk_used_gb',
        'disk_available_gb',
    ];
}
