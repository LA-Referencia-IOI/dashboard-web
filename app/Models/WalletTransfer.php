<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransfer extends Model
{
    use HasFactory;

    protected $fillable = ['authority_id', 'amount', 'tx_hash'];

    public function authority()
    {
        return $this->belongsTo(Authority::class, 'authority_id', 'id');
    }
}
