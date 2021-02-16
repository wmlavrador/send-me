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

}