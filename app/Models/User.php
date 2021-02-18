<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{
    Factories\HasFactory,
    SoftDeletes
};
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

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

    /**
     * Retorna as carteiras dos usuários
     *
     * @return \App\Models\UserWallet
     */
    public function carteiras(): object
    {
        return $this->hasMany(UserWallet::class);
    }

    /**
     * Retorna a descrição do tipo de conta
     *
     * @param  int  $cod
     * @return string
     */
    public static function getTipoConta($cod): string
    {
        $tiposConta = [
          "1" => "Pessoa Física",
          "2" => "Pessoa Jurídica"
        ];

        return $tiposConta[$cod];
    }

}
