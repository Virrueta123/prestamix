<?php

namespace App\Models;

use App\Helpers\Encryptor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class solicitud_trabajador extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'solicitud_trabajador';
    public $timestamps = true;
    protected $primaryKey = 'solicitud_trabajador_id';
 

    protected $hidden = [
        'solicitud_trabajador_id',"created_user"
    ];
    protected $appends = ['urlapi'];
 
    public function usuario(){
        return $this->belongsTo(User::class,'created_user');
    } 
     public function gasto(){
        return $this->hasOne(gastos::class,'solicitud_trabajador_id');
    } 

    public function sucursal(){
        return $this->belongsTo(User::class,'sucursal_id');
    }  

    public function salario(){
        return $this->belongsTo(salario::class,'salario_id');
    }   

    public function getUrlapiAttribute()
    {
        return Encryptor::encrypt($this->solicitud_trabajador_id);
    }
}
