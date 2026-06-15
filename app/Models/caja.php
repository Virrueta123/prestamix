<?php

namespace App\Models;

use App\Helpers\Encryptor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class caja extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'caja_chica';
    public $timestamps = true;
    protected $primaryKey = 'caja_chica_id';


    protected $hidden = [
        'caja_chica_id', "created_user"
    ];
    protected $appends = ['urlapi','ingresosefectivo','gastosefectivo','gastoscuenta','code','ingresoscuenta'];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'created_user');
    }

    public function getIngresosefectivoAttribute()
    { 
        $ingresos = pagos::where('caja_chica_id', $this->caja_chica_id)->where("tipo","I")->where("cuentas_id",1)->sum("monto");
        return $ingresos;
    }

    public function getIngresoscuentaAttribute()
    { 
        $ingresos = pagos::where('caja_chica_id', $this->caja_chica_id)->where("tipo","I")->whereNot("cuentas_id",1)->sum("monto");
        return $ingresos;
    }
 
    public function getCodeAttribute()
    { 
        $numeroFormateado = sprintf('%06d', $this->caja_chica_id);
        return  $numeroFormateado;
    }


    public function getGastosefectivoAttribute()
    {
        $gastos = pagos::where('caja_chica_id', $this->caja_chica_id)->whereIn("tipo",["G","C"])->where("cuentas_id",1)->sum("monto");
        return $gastos;
    }

    public function getGastoscuentaAttribute()
    {
        $gastos = pagos::where('caja_chica_id', $this->caja_chica_id)->whereIn("tipo",["G","C"])->whereNot("cuentas_id",1)->sum("monto");
        return $gastos;
    }

    public function getUrlapiAttribute()
    {
        return Encryptor::encrypt($this->caja_chica_id);
    }
}
