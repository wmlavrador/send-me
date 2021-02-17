<?php

namespace App\Http\Controllers;

use App\Models\Transacoes;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
                         "trns.valor",
                         "trns.situacao",
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
                         "trns.valor",
                         "trns.situacao",
                         "trns.id"
                     )
                     ->join("users", "users.id", "=", "trns.payee")
                     ->unionAll($trnsPayer)
                     ->where("users.id", '=', $userId)->get();

        foreach($trnsPayee as  $key => $col){
            $col->situacao = Transacoes::getSituacao($col->situacao);
        }

        return response($trnsPayee);
    }

    public function destinatarios(){
        $user = Auth::user();

        if($user->tipo_conta === "1"){
            $destinatarios = User::select("nome_completo", "id")->where("id", "<>" , $user->id)->get();
        }
        else {
            $destinatarios = [];
        }

        return response($destinatarios);
    }

    public function novaTransacao(Request $request){

        $messages = [
            "payer.required" => "Impossível continuar a transferência, efetue login novamente.",
            "payee.required" => "Informe o destinatário do valor a ser transferido."
        ];

        Validator::make($request->all(), [
            "payer" => [
                'required',
                function($atributo, $payer, $fail){
                     if($payer != Auth::id()){
                         $fail("Tipo de transferência não autorizada!");
                     }
                     if(Auth::user()['tipo_conta'] == "lojista"){
                         $fail("Seu perfil não comporta este tipo de transação.");
                     }
                }
            ],
            "payee" => [
                "required",
                function($atributo, $payee, $fail){
                    if(Auth::id() == $payee){
                        $fail("Tipo de transferência não autorizada!");
                    }
                }
            ],
            "value" => [
                function($atributo, $valor, $fail){
                    $walletDefault = User::find(Auth::id())->carteiras()->where("tipo_carteira" , "=", "1")->first();

                    if($valor > $walletDefault['saldo']){
                        $fail("O Valor da transferência excede o saldo de R$ {$walletDefault['saldo']} em sua carteira!");
                    }

                    if($valor < 1){
                        $fail("Valor mínimo para transferência é R$ 1,00 ");
                    }
                }
            ]
        ], $messages)->validate();

        // Após autorizado
        $trnsAguardando = DB::table("transacoes")->insertGetId([
            "payer" => $request['payer'],
            "payee" => $request['payee'],
            "valor" => $request['value'],
            "situacao" => "1"
        ]);

        // Desconta da carteira de origem,
        // Passa pelo processo de autorização
        // Chega na carteira de destino.
        $autorizado = UserWalletCtr::transferir($request->all());

        if($autorizado){
            $transacao = Transacoes::find($trnsAguardando);
            $transacao->situacao = "2";
            $transacao->save();

            $criarNotificacao = NotificacoesCtr::novaNotificacao([
                "origem" => "1",
                "id_origem" => $trnsAguardando,
                "mensagem" => "Você recebeu uma nova transferência",
                "payee" => $request['payee']
            ]);

        }
        else {
            $transacao = Transacoes::find($trnsAguardando);
            $transacao->situacao = "5";
            $transacao->save();

            return response(['erro' => 'transferência não autorizada!'], 422);
        }

    }

    public function estornar($idTransacao){
        $transacao = Transacoes::where("situacao", "=", "2")->findOrFail($idTransacao);

        $dados = [
            "owner" => $transacao['payer'],
            "payer" => $transacao['payee'],
            "payee" => $transacao['payer'],
            "value" => $transacao['valor']
        ];

        Validator::make($dados, [
            "owner" => function($attr, $owner, $fail){
                if(Auth::id() != $owner){ // Somente o usario que pagou pode estornar o valor.
                    $fail("Você não está habilitado para pedir o estorno!");
                }
            }
        ])->validate();

        $autorizado = UserWalletCtr::transferir($dados);

        if($autorizado){
            $transacao->situacao = "3";
            $transacao->save();

            return response(["sucesso" => "Valor estornado com sucesso"], 200);
        }
        else {
            return response(["erro" => "O Estorno não foi autorizado!"], 419);
        }

    }

    public function devolver($idTransacao){
        $transacao = Transacoes::where("situacao", "=", "2")->findOrFail($idTransacao);

        $dados = [
            "payer" => $transacao['payee'],
            "payee" => $transacao['payer'],
            "value" => $transacao['valor']
        ];

        Validator::make($dados, [
            "payer" => function($attr, $payer, $fail){
                if(Auth::id() != $payer){ // Somente o usario que recebeu devolver o valor.
                    $fail("Você não pode devolver esta transação.");
                }
                if(Auth::user()['tipo_conta'] == 2){
                    $fail("Seu perfil não está autorizado para este tipo de transação");
                }
            }
        ])->validate();

        $autorizado = UserWalletCtr::transferir($dados);

        if($autorizado){
            $transacao->situacao = "4";
            $transacao->save();

            return response(["message" => "Valor devolvido com sucesso!"], 200);
        }
        else {
            return response(["message" => "A Devolução não foi autorizado."], 419);
        }

    }

}
