<?php

namespace App\Models;

use App\Helpers\Encryptor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    use HasFactory;
    protected $table = 'sucursal';
    public $timestamps = true;
    protected $primaryKey = 'sucursal_id';
    protected $guarded = [];

    protected $hidden = [
        'sucursal_id' 
    ];

    protected $appends = ['urlapi'];

    public function getUrlapiAttribute()
    {
        return Encryptor::encrypt($this->sucursal_id);
    }
}
