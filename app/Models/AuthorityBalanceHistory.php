<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthorityBalanceHistory extends Model
{
    use HasFactory;

    protected $fillable = ['authority_id', 'balance'];

    public function authority()
    {
        return $this->belongsTo(Authority::class, 'authority_id', 'id');
    }
}
