<?php

namespace App\Models;

use App\Helpers\Encryptor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class gastos extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'gastos';
    public $timestamps = true;
    protected $primaryKey = 'gastos_id';
 

    protected $hidden = [
        'gastos_id' 
    ];
    protected $appends = ['urlapi','pago',"urlcaja"];
 
    public function usuario(){
        return $this->belongsTo(User::class,'created_user');
    }

    public function tipo_gasto(){
        return $this->belongsTo(tipo_gasto::class,'tipo_gasto_id');
    }

    public function solicitud(){
        return $this->belongsTo(Solicitud::class,'solicitud_id');
    }

    public function sucursal(){
        return $this->belongsTo(sucursal::class,'sucursal_id');
    }

    public function getPagoAttribute()
    {
        
        return pagos::with("cuenta")->where("gastos_id",$this->gastos_id)->where("caja_chica_id",$this->caja_chica_id)->get();
    }

    public function getUrlapiAttribute()
    {
        return Encryptor::encrypt($this->gastos_id);
    }
    public function getUrlcajaAttribute()
    {
        return Encryptor::encrypt($this->caja_chica_id);
    }
}
