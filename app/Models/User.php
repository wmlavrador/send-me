<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens,HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nome_completo',
        'email',
        'password',
        'documento',
        'tipo_conta',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        "deleted_at",
        "email_verified_at",
        "two_factor_secret",
        "two_factor_recovery_codes"
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function carteiras(){
        return $this->hasMany(UserWallet::class);
    }

    public static function getTipoConta($cod){
        $tiposConta = [
          "1" => "Pessoa Física",
          "2" => "Pessoa Jurídica"
        ];

        return $tiposConta[$cod];
    }

}
