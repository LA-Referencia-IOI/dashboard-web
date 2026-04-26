<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\UserType;
use App\Enums\InstitutionType;
use Carbon\Carbon;
use App\Models\Account;


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
        'code',
        'state',
        'numberNodes',
        'typeNodes',
        'detailsNodes',
        'status',
        'authority_registered',
        'description'
    ];

    public function getTypeAlias()
    {
        $type = InstitutionType::getDescription($this->type);

        return $type;
    }

    public function getTypeNodes()
    {
       
        switch ($this->typeNodes) {
            case 0:
                $resp = "<span class=\"badge badge-success\">Main dARK";
                break;
            case 1:
                $resp = "<span class=\"badge badge-success\">External Node in Main dARK";
                break;
            case 2:
                $resp = "<span class=\"badge badge-info\">Network Partner";
                break;
            case 3:
                $resp = "<span class=\"badge badge-info\">External Node in Network Partner";
                break;
            default:
                $resp = "Undefined";
                break;
        }
        return $resp;
    }

    public function getStatus()
    {
        
        $status = $this->status;

        if($status == 0){
            return "<span class=\"badge badge-success\">Enabled</span>";
        }else if($status == 1){
            return "<span class=\"badge badge-danger\">Disabled</span>";
        }else if($status == 3){
            return "<span class=\"badge badge-info\">Enabled and with account</span>";
        }else{
            return "<span class=\"badge badge-warning\">Undefined</span>";
        }
        return $status;
    }



    public function account()
    {
        return $this->hasOne(Account::class, 'institution_id', 'id');
    }

    public function getNaan()
    {
        return $this->account ? $this->account->naan : null;
    }

    public function getShoulder()
    {
        return $this->account ? $this->account->shoulder : null;
    }

}
