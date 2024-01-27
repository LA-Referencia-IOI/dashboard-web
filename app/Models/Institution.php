<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\UserType;
use App\Enums\InstitutionType;
use Carbon\Carbon;

class Institution extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'address',
        'phone',
        'email',
        'responsible',
        'latitude',
        'longitude',
        'balance',
        'plan',
        'country',
        'city',
        'state',
        'numberNodes',
        'typeNodes',
        'detailsNodes',
        'status',
        'description'
    ];

    public function getTypeAlias()
    {
        $type = InstitutionType::getDescription($this->type);

        return $type;
    }

    public function getStatus()
    {
        
        $status = $this->status;

        if($status == 0){
            return "<span class=\"badge badge-success\">Enabled</span>";
        }else if($status == 1){
            return "<span class=\"badge badge-danger\">Disabled</span>";
        }else{
            return "<span class=\"badge badge-warning\">Undefined</span>";
        }
        return $status;
    }


}
