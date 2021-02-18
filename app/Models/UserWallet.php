<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{
    Factories\HasFactory,
    Model
};

use Illuminate\Support\Facades\Auth;

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

    public static function getTipoCarteira($cod): string
    {
        $tipos = [
            "1" => "Debito",
            "2" => "Crédito"
        ];

        return $tipos[$cod];
    }


    /**
     * Verifica se na carteira existe os recursos para transferência.
     *
     * @param  float  $inputValue
     * @return bool
     */
    public static function existeRecursos($inputValue): bool
    {
        $carteiraPadrao = User::find(Auth::id());
        $carteiraPadrao = $carteiraPadrao->carteiras()->where("tipo_carteira", '=', '1')->first();

        if($inputValue > $carteiraPadrao['saldo'])
        {
            return false;
        }

        return true;
    }

    /**
     * Verifica o formato de valor permitido.
     *
     * @param  float  $value
     * @return string
     */
    public static function checkDecimal(float $value): string
    {
        if(preg_match("/(,)/", $value))
        {
            return "Este formato '{$value} é inválido. formato permitido: 123.99";
        }

        $parts = explode(".", $value);
        if(count($parts) == 2)
        {
            if(strlen($parts[1]) > 3)
            {
                return "Só é permitido até 3 dígitos decimais!";
            }
        }
        if(count($parts) > 2)
        {
            return "Informe o valor inteiro seguido do ponto indicando o valor decimal!";
        }

        return "";
    }

}
