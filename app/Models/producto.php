<?php

namespace App\Models;

use App\Helpers\Encryptor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class producto extends Model
{
    use HasFactory;
    protected $table = "producto";
    public $timestamps = true;
    protected $primaryKey = 'producto_id';
    protected $guarded = [];

    protected $hidden = [
        'producto_id'
    ];

    protected $appends = ['urlapi'];

    public function getUrlapiAttribute()
    {
        return Encryptor::encrypt($this->producto_id);
    }
  
}
