<?php

namespace App\Models;

use App\Helpers\Encryptor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ingreso extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'ingreso';
    public $timestamps = true;
    protected $primaryKey = 'ingreso_id';
 

    protected $hidden = [
        'ingreso_id',"created_user" 
    ];
    protected $appends = ['urlapi',"pago"];
 
    public function usuario(){
        return $this->belongsTo(User::class,'created_user');
    }

    public function prestamo(){
        return $this->belongsTo(prestamo::class,'prestamo_id');
    }

    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cli_id');
    }

    public function pagos(){
        return $this->hasMany(pagos::class,'ingreso_id');
    }

    public function cuenta(){
        return $this->belongsTo(cuentas::class,'cuentas_id');
    }

    public function getUrlapiAttribute()
    {
        return Encryptor::encrypt($this->ingreso_id);
    }

    public function getPagoAttribute()
    {
        
        return pagos::with("cuenta")->where("ingreso_id",$this->ingreso_id)->where("caja_chica_id",$this->caja_chica_id)->get();
    }
}
