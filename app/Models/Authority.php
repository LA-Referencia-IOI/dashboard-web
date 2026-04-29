<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Authority extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'responsible',
        'email',
        'phone',
        'wallet_address',
        'balance',
        'status',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function institutions()
    {
        return $this->hasMany(Institution::class, 'authority_id', 'id');
    }

    public function account()
    {
        return $this->hasOne(Account::class, 'authority_id', 'id');
    }

    public function blockchains()
    {
        return $this->hasMany(Blockchain::class, 'authority_id', 'id');
    }

    public function balanceHistories()
    {
        return $this->hasMany(AuthorityBalanceHistory::class, 'authority_id', 'id');
    }

    public function getStatusBadge()
    {
        switch ($this->status) {
            case 'active':
                return '<span class="badge badge-success">Active</span>';
            case 'pending':
                return '<span class="badge badge-warning">Pending</span>';
            case 'disabled':
                return '<span class="badge badge-danger">Disabled</span>';
            default:
                return '<span class="badge badge-secondary">Unknown</span>';
        }
    }

    public function isRegistered()
    {
        return $this->status === 'active';
    }
}
