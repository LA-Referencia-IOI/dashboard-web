<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\UserType;
use Carbon\Carbon;

class Institution extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'address',
        'phone',
        'email',
        'responsible',
        'latitude',
        'longitude',
        'balance',
        'plan',
        'country',
        'city',
        'state',
        'numberNodes',
        'typeNodes',
        'detailsNodes',
        'status',
        'description'
    ];
}
