<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{
    Factories\HasFactory,
    Model
};

class Notificacoes extends Model
{
    use HasFactory;

    protected $fillable = [
        "origem",
        "id_origem",
        "mensagem",
        "situacao",
        "user_id"
    ];

    /**
     * Retorna string com o nome do status informado
     *
     * @param  int  $cod
     * @return string
     */
    public static function getStatus($cod): string
    {
        $status = [
          "1" => "Em Andamento",
          "2" => "Enviado",
          "3" => "Entregue"
        ];

        return $status[$cod];
    }

    /**
     * Retorna descrição da origem informada
     *
     * @param  int  $cod
     * @return string
     */
    public static function getOrigem($cod): string
    {
        $origem = [
            "1" => "Transações",
        ];

        return $origem[$cod];
    }
}
