<?php

namespace App\Models;

use App\Helpers\Encryptor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class mensajes_prestamo extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'mensajes_prestamo';
    public $timestamps = true;
    protected $primaryKey = 'msj_prestamo_id';
 

    protected $hidden = [
        'msj_prestamo_id',"created_user"
    ];
    protected $appends = ['urlapi'];
 
    public function usuario(){
        return $this->belongsTo(User::class,'created_user');
    } 

    public function sucursal(){
        return $this->belongsTo(User::class,'sucursal_id');
    } 

    public function prestamo(){
        return $this->belongsTo(Prestamo::class,'prestamo_id');
    } 

    public function getUrlapiAttribute()
    {
        return Encryptor::encrypt($this->msj_prestamo_id);
    }
}
