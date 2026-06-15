<?php

namespace App\Models;

use App\Helpers\Encryptor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class salario extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'salario';
    public $timestamps = true;
    protected $primaryKey = 'salario_id';
 

    protected $hidden = [
        'salario_id',"created_user"
    ];
    protected $appends = ['urlapi','pagado'];
 
    public function usuario(){
        return $this->belongsTo(User::class,'created_user');
    } 

    public function sucursal(){
        return $this->belongsTo(User::class,'sucursal_id');
    } 

    public function trabajador(){
        return $this->belongsTo(User::class,'trabajador_id');
    } 
 
    public function getUrlapiAttribute()
    {
        return Encryptor::encrypt($this->salario_id);
    }

    public function getPagadoAttribute()
    { 
        $sumar_solicitudes = solicitud_trabajador::where("salario_id", $this->salario_id)->where("status", "G")->get()->sum("monto");
        return  $sumar_solicitudes;
    }


}
