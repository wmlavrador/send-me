<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{
    Factories\HasFactory,
    Model,
    SoftDeletes
};

class Transacoes extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "payer",
        "payee",
        "valor",
        "situacao"
    ];

    /**
     * Retorna String com nome da Situação informada
     * @param  int  $cod
     * @return string
     */
    public static function getSituacao($cod): string
    {
        $situacoes = [
          "1" => "Em Andamento",
          "2" => "Aprovado",
          "3" => "Estornado",
          "4" => "Devolvido",
          "5" => "Não Autorizado",
        ];

        return $situacoes[$cod];
    }

}
