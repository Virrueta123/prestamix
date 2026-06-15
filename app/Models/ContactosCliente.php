<?php

namespace App\Models;

use App\Helpers\Encryptor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactosCliente extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'contactos_cliente';
    public $timestamps = true;
    protected $primaryKey = 'ccliente_id';
 

    protected $hidden = [
        'ccliente_id'
    ];
    protected $appends = ['urlapi'];

 
    public function getUrlapiAttribute()
    {
        return Encryptor::encrypt($this->ccliente_id);
    }
}
