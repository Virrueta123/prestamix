<?php

namespace App\Models;

use App\Helpers\Encryptor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class pagos extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'pagos';
    public $timestamps = true;
    protected $primaryKey = 'pagos_id';
 

    protected $hidden = [
        'pagos_id',"created_user"
    ];
    protected $appends = ['urlapi'];
 
    public function usuario(){
        return $this->belongsTo(User::class,'created_user');
    } 

    public function sucursal(){
        return $this->belongsTo(User::class,'sucursal_id');
    } 
    
    public function cuenta(){
        return $this->belongsTo(cuentas::class,'cuentas_id');
    } 

    public function getUrlapiAttribute()
    {
        return Encryptor::encrypt($this->pagos_id);
    }
}
