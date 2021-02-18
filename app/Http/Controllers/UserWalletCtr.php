<?php

namespace App\Http\Controllers;


use App\Models\{User, UserWallet};
use Illuminate\Support\Facades\{Auth, Http};
use Illuminate\Http\Request;

class UserWalletCtr extends Controller
{
    /**
     * Retorna a carteira padrão do usuario
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(User::find(Auth::id())->carteiras()->get());
    }

    /**
     * Efetua a transação dos valores entre as carteiras dos usuarios
     *
     * @param  array  $input
     * @param  \App\Models\Transacoes
     *
     * @return bool
     */
    public static function transferir($input): bool
    {
        $mokyAuth = "https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6";

        $response = Http::get($mokyAuth);

        if($response['message'] == "Autorizado" && $response->successful())
        {
            $payer = User::find($input['payer']);

            $walletPayer = $payer->carteiras()->where("tipo_carteira", '=', '1')->first();
            $payee = User::find($input['payee'])->carteiras()->where("tipo_carteira", '=', '1')->first();

            // Verifica se o tipo da conta está elegível para transferências.
            if($payer->tipo_conta == 2){
                return false;
            }

            // Verifica se o Pagador possui recursos;
            $existeRecurso = UserWallet::existeRecursos($input['value']);

            if($existeRecurso === false)
            {
                return false;
            }

            $walletPayer->decrement('saldo', $input['value']);
            $payee->increment('saldo', $input['value']);

            NotificacoesCtr::novaNotificacao([
                "origem" => "1",
                "id_origem" => $input["id_origem"],
                "mensagem" => "Você recebeu uma nova transferência",
                "payee" => $input['payee']
            ]);

            return true;
        }

        return false;
    }

    /**
     * Realiza o depósito de um valor na carteira padrão do usuario
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function depositar(Request $request)
    {

        $checkDecimal = UserWallet::checkDecimal($request->value);
        if(!empty($checkDecimal))
        {
            return response()->json(["erro" => $checkDecimal], 422);
        }

        $payee = User::find(Auth::id())->carteiras()->where("tipo_carteira", '=', '1')->first();
        $payee->increment('saldo', $request->value);

        return response()->json(["sucesso" => "Deposito realizado com sucesso!"]);
    }
}
