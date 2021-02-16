<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transacoes extends Model
{
    use HasFactory;

    protected $fillable = [
        "payer",
        "payee",
        "valor",
        "situacao"
    ];

    public static function getSituacao($cod){
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
