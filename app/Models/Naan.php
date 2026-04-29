<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Naan extends Model
{
    use HasFactory;

    protected $fillable = [
        'naan',
        'organization_name',
        'organization_acronym',
        'target_url_template',
        'contact_name',
        'contact_email',
        'status',
        'registered_at',
    ];

    public function authorities()
    {
        return $this->belongsToMany(Authority::class, 'authority_naan', 'naan_id', 'authority_id')->withTimestamps();
    }
}
