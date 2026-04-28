<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\InstitutionType;
use App\Models\Authority;

class Institution extends Model
{
    use HasFactory;

    protected $fillable = [
        'authority_id',
        'type',
        'name',
        'address',
        'phone',
        'email',
        'responsible',
        'latitude',
        'longitude',
        'country',
        'city',
        'state',
        'numberNodes',
        'typeNodes',
        'detailsNodes',
        'status',
        'description',
    ];

    public function authority()
    {
        return $this->belongsTo(Authority::class, 'authority_id', 'id');
    }

    public function getTypeAlias()
    {
        return InstitutionType::getDescription($this->type);
    }

    public function getTypeNodes()
    {
        switch ($this->typeNodes) {
            case 0:
                return '<span class="badge badge-success">Main dARK</span>';
            case 1:
                return '<span class="badge badge-success">External Node in Main dARK</span>';
            case 2:
                return '<span class="badge badge-info">Network Partner</span>';
            case 3:
                return '<span class="badge badge-info">External Node in Network Partner</span>';
            default:
                return 'Undefined';
        }
    }

    public function getStatus()
    {
        switch ($this->status) {
            case '0':
                return '<span class="badge badge-success">Enabled</span>';
            case '1':
                return '<span class="badge badge-danger">Disabled</span>';
            case '3':
                return '<span class="badge badge-info">Enabled and with account</span>';
            default:
                return '<span class="badge badge-warning">Undefined</span>';
        }
    }
}
