<?php

namespace App\Models;

use App\Helpers\Encryptor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;
    protected $table = "cliente"; 
    public $timestamps = true;
    protected $primaryKey = 'cli_id';
    protected $hidden = ["cli_id","sucursal_id"];
  
 
    protected $appends = ['urlapi',"ultimasolicitud"];

    public function ContactosCliente(){
        return $this->hasMany(ContactosCliente::class,"cli_id");
    }

    public function solicitudes(){
        return $this->hasMany(Solicitud::class,"cli_id");
    }

    public function getUltimasolicitudAttribute(){
        return  $this->solicitudes()->orderBy("solicitud_id", "DESC")->first();
    }

    // appends
    public function getUrlapiAttribute( ){ 
        return Encryptor::encrypt($this->cli_id); 
    } 
}
