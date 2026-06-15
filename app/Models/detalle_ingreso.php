<?php

namespace App\Models;

use App\Helpers\Encryptor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class detalle_ingreso extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'detalle_ingreso';
    public $timestamps = true;
    protected $primaryKey = 'detalle_ingreso_id';
 

    protected $hidden = [
        'detalle_ingreso_id',"created_user"
    ];
    protected $appends = ['urlapi'];
 
    public function usuario(){
        return $this->belongsTo(User::class,'created_user');
    }

    public function ingreso(){
        return $this->belongsTo(ingreso::class,'ingreso_id');
    }
 
    public function getUrlapiAttribute()
    {
        return Encryptor::encrypt($this->detalle_ingreso_id);
    }
    
    
}
