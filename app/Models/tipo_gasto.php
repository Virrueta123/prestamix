<?php

namespace App\Models;

use App\Helpers\Encryptor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class tipo_gasto extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'tipo_gasto';
    public $timestamps = true;
    protected $primaryKey = 'tipo_gasto_id';
 

    protected $hidden = [
        'tipo_gasto_id' 
    ];
    protected $appends = ['urlapi'];
 
    public function usuario(){
        return $this->belongsTo(User::class,'created_user');
    }

    public function getUrlapiAttribute()
    {
        return Encryptor::encrypt($this->tipo_gasto_id);
    }

}
