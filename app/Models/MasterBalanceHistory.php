<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterBalanceHistory extends Model
{
    use HasFactory;

    protected $fillable = ['balance'];
}
