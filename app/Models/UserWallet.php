<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWallet extends Model
{
    use HasFactory;

    protected $table = "user_wallet";

    protected $fillable = [
        "descricao",
        "saldo",
        "tipo_carteira",
        "user_id"
    ];

    protected $attributes = [
        "tipo_carteira" => "debito"
    ];

    protected $hidden = [
        "deleted_at"
    ];

    public static function getTipoCarteira($cod){
        $tipos = [
            "1" => "Debito",
            "2" => "Crédito"
        ];

        return $tipos[$cod];
    }

    // Verifica se determinada carteira existe os recursos necessários para transferência.
    public static function existeRecursos($inputValue){
        $carteiraPadrao = User::find(Auth::id())->carteiras()->where("tipo_carteira", '=', '1')->first();

        if($inputValue > $carteiraPadrao['saldo']){
            return false;
        }

        return true;
    }

}
