<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\AccountType;
use App\Models\Authority;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'authority_id',
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
        'status',
    ];

    public function authority()
    {
        return $this->belongsTo(Authority::class, 'authority_id', 'id');
    }

    public function getProfileAlias()
    {
        switch ($this->profile) {
            case 0:
                return 'Manager';
            case 1:
                return 'Partner';
            case 2:
                return 'Others';
            default:
                return 'Undefined';
        }
    }

    public function getBalance()
    {
        $balance = $this->balance;
        $formattedBalance = sprintf('%.2e', $balance);
        $formattedBalance = str_replace(['e+', 'e-'], ['x10^', 'x10^-'], $formattedBalance);
        return $formattedBalance;
    }
}
