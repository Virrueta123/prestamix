<?php

namespace App\Models;

use App\Helpers\Encryptor;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Solicitud extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'solicitud';
    public $timestamps = true;
    protected $primaryKey = 'solicitud_id';
    protected $fillable = [];
    protected $hidden = [
       "cli_id", "sucursal_id", "analista_id"
    ];

    protected $appends = ['urlapiantigua',"urlapi", 'code', "statusformated"];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, "cli_id");
    }

    public function analista()
    {
        return $this->belongsTo(User::class, "analista_id");
    }

    public function prestamo()
    {
        return $this->belongsTo(Prestamo::class, "prestamo_id");
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, "created_user");
    }

    public function guardar_documento()
    {
        return $this->hasMany(guardar_documento::class, "solicitud_id");
    }
 

    public function getUrlapiAttribute()
    {
        return Encryptor::encrypt($this->solicitud_id);
    }

    public function getUrlapiantiguaAttribute()
    {
        if ($this->solicitud_id_relacionado == 0) {
           return 0;
        } else {
            return Encryptor::encrypt($this->solicitud_id_relacionado);
        }
    }
 
    public function getStatusformatedAttribute()
    {
        $status_string = "";
        switch ($this->status) {
            case 'M':
                $status_string = "Aceptado y modificado";
                break;

                case 'P':
                $status_string = "Pendiente";
                break;

                case 'R':
                $status_string = "Rechazado";
                break;

                case 'A':
                $status_string = "Aceptado";
                break;
                
                case 'G':
                $status_string = "Procesado";
                break;
        }
        return $status_string;
    }

    public function getCodeAttribute()
    { 
        $numeroFormateado = sprintf('%06d', $this->serie);
        return  $numeroFormateado;
    }
 
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($modelo) {
            $correlativo = Solicitud::max('serie');
            if (is_null($correlativo)) {
                $correlativo = 1;
            } else {
                $correlativo++;
            }
            $modelo->serie = $correlativo;
        });
    }
}
