<?php

namespace App\Models;

use App\Helpers\Encryptor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class compra extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'compra';
    public $timestamps = true;
    protected $primaryKey = 'compra_id';
 

    protected $hidden = [
        'compra_id',"created_user"
    ];
    protected $appends = ['urlapi'];
 
    public function usuario(){
        return $this->belongsTo(User::class,'created_user');
    }
 
    public function getUrlapiAttribute()
    {
        return Encryptor::encrypt($this->compra_id);
    }
}
