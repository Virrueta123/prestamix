<?php

namespace App\Models;

use App\Helpers\Encryptor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cronograma extends Model
{
    use HasFactory;
    protected $table = 'cronograma';
    public $timestamps = true;
    protected $primaryKey = 'cronograma_id';


    protected $hidden = [
        'cronograma_id',
        'prestamo_id',
    ];
    protected $appends = ['urlapi', 'pagado'];

    public function prestamo()
    {
        return $this->belongsTo(prestamo::class, "prestamo_id");
    }

    public function getPagadoAttribute()
    {  
        $suma_ingresos = detalle_ingreso::where("cronograma_id", $this->cronograma_id)
            ->join('ingreso', 'detalle_ingreso.ingreso_id', '=', 'ingreso.ingreso_id')
            ->sum('ingreso.monto');
        return $suma_ingresos;
    }

    public function getUrlapiAttribute()
    {
        return Encryptor::encrypt($this->cronograma_id);
    }
}
