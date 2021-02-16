<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class UserWalletCtr extends Controller
{
    public function index(){
        return User::find(Auth::id())->carteiras()->get();
    }

    // Return bool
    public static function transferir($input){
        $mokyAuth = "https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6";

        $response = Http::get($mokyAuth);

        if($response['message'] == "Autorizado" && $response->successful()){
            $payer = User::find($input['payer'])->carteiras()->where("tipo_carteira", '=', '1')->first();
            $payee = User::find($input['payee'])->carteiras()->where("tipo_carteira", '=', '1')->first();

            if($input['value'] > $payer['saldo']){
                return response(["erro" => "O Valor da transferência excede o saldo de R$ {$payer['saldo']} em sua carteira!"], 419);
            }

            $payer->decrement('saldo', $input['value']);
            $payee->increment('saldo', $input['value']);

            return true;
        }
        else {
            return false;
        }
    }

    public function depositar(Request $request){

        $payee = User::find(Auth::id())->carteiras()->where("tipo_carteira", '=', '1')->first();
        $payee->increment('saldo', $request->value);

        return response(["sucesso" => "Deposito realizado com sucesso!"]);
    }
}
