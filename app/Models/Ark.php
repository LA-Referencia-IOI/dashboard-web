<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Account;
use App\Models\Institution;

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
    'address',
    'target_url',
    'target_http_code',
    'who_name_native',
    'who_acronym',
    'who_location',
    'na_orgtype',
    'na_policy',
    'na_tenure',
    'na_policy_url',
    'test_identifier',
    'service_provider',
    'purpose',
    'rtype',
    'contact_name',
    'contact_unit',
    'contact_tenure',
    'contact_phone',
    'alternate_contact',
    'comments',
    'provider',
];

}
