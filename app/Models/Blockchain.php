<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Authority;

class Blockchain extends Model
{
    use HasFactory;

    protected $fillable = [
        'authority_id',
        'type',
        'number_nodes',
        'local',
        'status',
        'description',
        'url',
        'enodes',
    ];

    public function authority()
    {
        return $this->belongsTo(Authority::class, 'authority_id', 'id');
    }
}
