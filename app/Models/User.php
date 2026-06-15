<?php

namespace App\Models;

use App\Helpers\Encryptor;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
 
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,HasRoles;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];
  
    protected $appends = ['urlapi','rol'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'roles',
        'sucursal_id',
        'id',
        'password',
        'remember_token',
    ]; 

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
     
 
    public function getUrlapiAttribute()
    {
        return Encryptor::encrypt($this->id);
    }

    public function getRolAttribute()
    {
        return $this->getRoleNames()->first();
    }

    public function sucursal(){
        return  $this->belongsTo(sucursal::class,"sucursal_id");
    }
}
