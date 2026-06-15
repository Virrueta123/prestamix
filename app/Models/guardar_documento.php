<?php

namespace App\Models;

use App\Helpers\Encryptor;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class guardar_documento extends Model
{
    use HasFactory;
    protected $table = 'guardar_documento';
    public $timestamps = true;
    protected $primaryKey = 'guardar_documento_id';
  
    protected $hidden = [
        'guardar_documento_id' 
    ];

    protected $appends = ['urlapi',"fechacreacion","horacreacion"];

    // relaciones
    public function usuario()
    {
        return $this->belongsTo(User::class, "created_user");
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, "sucursal_id");
    }
      
    // cast
    public function getUrlapiAttribute()
    {
        return Encryptor::encrypt($this->guardar_documento_id);
    }

     public function getHoracreacionAttribute()
    { 
        return Carbon::parse($this->updated)->format('h:i A');
    }

    public function getFechacreacionAttribute()
    { 
        return Carbon::parse($this->updated)->format('Y/m/d');
    }
}
