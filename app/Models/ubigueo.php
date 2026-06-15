<?php

namespace App\Models;

use App\Helpers\Encryptor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ubigueo extends Model
{
    use HasFactory ;
    protected $table = 'ubigeo';
    public $timestamps = true;
    protected $primaryKey = 'ubigeo_id';
 

    protected $hidden = [
        'ubigeo_id' 
    ];
    protected $appends = ['urlapi'];
 
    public function usuario(){
        return $this->belongsTo(User::class,'created_user');
    }

    public function getUrlapiAttribute()
    {
        return Encryptor::encrypt($this->ubigeo_id);
    }
}
