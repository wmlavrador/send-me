<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public static function getStatus($cod){
        $status = [
          "1" => "Em Andamento",
          "2" => "Enviado",
          "3" => "Entregue"
        ];

        return $status[$cod];
    }

    public static function getOrigem($cod){
        $origem = [
            "1" => "Transações",
        ];

        return $origem[$cod];
    }
}
