<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\UserType;
use App\Enums\AccountType;
use App\Enums\InstitutionType;
use Carbon\Carbon;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile',
        'checkin_date',
        'auth_id',
        'contact_email',
        'naan',
        'organization_name',
        'payload_schema',
        'address',
        'balance',
        'private_key',
        'shoulder',
        'dnam_auth_id',
        'noid_len',
        'noidprovider_addr',
        'status'
    ];

}

            
