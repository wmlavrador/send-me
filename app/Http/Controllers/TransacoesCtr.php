<?php

namespace App\Http\Controllers;

use App\Models\Transacoes;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransacoesCtr extends Controller
{
    public function index(){
        $userId = Auth::id();

        $trnsPayer = DB::table("transacoes", "trns")
                     ->select(
                         "users.nome_completo as pagador",
                         DB::raw("(select nome_completo from users where id = trns.payee) as recebedor"),
                         "trns.situacao",
                         "trns.created_at as criado",
                         "trns.updated_at as atualizado",
                         "trns.id"
                     )
                     ->join("users", "users.id", "=", "trns.payer")
                     ->where("users.id", '=', $userId);

        $trnsPayee = DB::table("transacoes", "trns")
                     ->select(
                         DB::raw("(select nome_completo from users where id = trns.payer) as pagador"),
                         "users.nome_completo as recebedor",
                         "trns.situacao",
                         "trns.created_at as criado",
                         "trns.updated_at as atualizado",
                         "trns.id"
                     )
                     ->join("users", "users.id", "=", "trns.payee")
                     ->unionAll($trnsPayer)
                     ->where("users.id", '=', $userId)->get();

        return response($trnsPayee);
    }

    public function destinatarios(){
        $user = Auth::user();

        if($user['tipo_conta'] === "usuario"){
            $destinatarios = User::all()->where("id", "<>" , $user['id']);
        }
        else {
            $destinatarios = [];
        }

        return response($destinatarios);
    }

    public function store(Request $request){


        return Transacoes::create([
            "payer" => $request['payer'],
            "payee" => $request['payee'],
            "valor" => $request['value'],
            "situacao" => "andamento"
        ]);

    }

}
