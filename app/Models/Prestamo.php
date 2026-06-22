<?php

namespace App\Models;

use App\Helpers\Encryptor;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\PrestamoFactory::new();
    }
    protected $table = "prestamos";
    public $timestamps = true;
    protected $primaryKey = 'prestamo_id';
    protected $guarded = [];

    protected $hidden = [
        'solicitud_id'
    ];
    protected $appends = ['urlapi', 'code', "ncuotas","pa","fechainicio","cuota_actual"];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($modelo) {
            $Prestamo = Prestamo::max('serie');
            if (is_null($Prestamo)) {
                $Prestamo = 1;
            } else {
                $Prestamo++;
            }
            $modelo->serie = $Prestamo;
        });
    }

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, "solicitud_id");
    }

    public function analista()
    {
        return $this->belongsTo(User::class, "analista_id");
    }

    public function getCodeAttribute()
    {
        $numeroFormateado = sprintf('%06d', $this->serie);
        return  $numeroFormateado;
    }

    public function getUrlapiAttribute()
    {
        return Encryptor::encrypt($this->prestamo_id);
    }

    public function getNcuotasAttribute()
    { 
        $cronogramas = cronograma::where("prestamo_id", $this->prestamo_id)->get()->count(); 
        return $cronogramas;
    }
 
    public function getFechainicioAttribute()
    {  
            return $this->created_at; 
    }

    public function getPaAttribute()
    { 
        
        // $cronogramas = cronograma::where("prestamo_id", $this->prestamo_id)->where("yes_pago","Y")->sum("cuota"); 
        $pagos = ingreso::where("prestamo_id", $this->prestamo_id)->sum("monto");
        return $pagos;
    }

    public function cronograma()
    {
        return $this->hasMany(cronograma::class, "prestamo_id");
    }

    

    public function getCuotaActualAttribute()
    {  
      
            return cronograma::where("prestamo_id", $this->prestamo_id)->where("yes_pago", "N")->orderBy("periodo", 'asc')->first();  
        
    }

  
 
   
    public function mensajes_prestamo()
    {
        return $this->hasOne(mensajes_prestamo::class, "msj_prestamo_id");
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, "cli_id");
    }
}
