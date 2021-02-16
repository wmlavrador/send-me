<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;

class UserWalletCtr extends Controller
{
    // Return bool
    public static function transferir($input){
        $mokyAuth = "https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6";

        $response = Http::get($mokyAuth);

        if($response['message'] == "Autorizado" && $response->successful()){
            $payer = User::find($input['payer'])->carteiras()->where("tipo_carteira", '=', 'debito')->first();
            $payee = User::find($input['payee'])->carteiras()->where("tipo_carteira", '=', 'debito')->first();

            if($input['value'] > $payer['saldo']){
                return response(["erro" => "O Valor da transferência excede o saldo de R$ {$payer['saldo']} em sua carteira!"], 419);
            }

            $payer->saldo = $payer->decrement('saldo', $input['value']);
            $payee->saldo = $payee->increment('saldo', $input['value']);

            return true;
        }
        else {
            return false;
        }
    }

    public function depositar(Request $request){

        $Payee = User::find(Auth::id())->carteiras()->where("tipo_carteira", '=', 'debito')->first();
        $Payee->saldo = $Payee->increment('saldo', $request['value']);

        return response(["sucesso" => "Deposito realizado com sucesso!"]);
    }
}
