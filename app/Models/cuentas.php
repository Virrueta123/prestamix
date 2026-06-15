<?php

namespace App\Models;

use App\Helpers\Encryptor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class cuentas extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'cuentas';
    public $timestamps = true;
    protected $primaryKey = 'cuentas_id';
 

    protected $hidden = [
        'cuentas_id',"created_user"
    ];
    protected $appends = ['urlapi'];
 
    public function usuario(){
        return $this->belongsTo(User::class,'created_user');
    }

    public function getUrlapiAttribute()
    {
        return Encryptor::encrypt($this->cuentas_id);
    }
}
