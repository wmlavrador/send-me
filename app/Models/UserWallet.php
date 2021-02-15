<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        "descricao",
        "saldo",
        "tipo_carteira",
        "user_id"
    ];

    protected $attributes = [
        "tipo_carteira" => "debito"
    ];

}
