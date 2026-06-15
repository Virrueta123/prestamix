<?php

namespace App\Models;

use App\Helpers\Encryptor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class send_email extends Model
{
    use HasFactory,SoftDeletes; 
    protected $table = 'send_email';
    public $timestamps = true;
    protected $primaryKey = 'send_email_id';
 

    protected $hidden = [
        'send_email_id',"created_user"
    ];
    protected $appends = ['urlapi'];
 
    public function usuario(){
        return $this->belongsTo(User::class,'created_user');
    } 
 
    public function getUrlapiAttribute()
    {
        return Encryptor::encrypt($this->send_email_id);
    }
}
