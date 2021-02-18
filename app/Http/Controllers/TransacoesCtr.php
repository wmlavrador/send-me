<?php

namespace App\Http\Controllers;

use App\Models\Transacoes;
use App\Models\User;
use App\Models\UserWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransacoesCtr extends Controller
{
    public function index(){
        $userId = Auth::id();

        $transacoes = DB::table("transacoes as tra")
        ->select(
            "payer.nome_completo as pagador",
                    "payee.nome_completo as recebedor",
                    "tra.situacao",
                    "tra.created_at as criado",
                    "tra.updated_at as atualizado",
                    "tra.valor",
                    "tra.id",
        )
        ->join("users as payer", "payer.id", "=", "payer")
        ->join("users as payee", "payee.id", "=", "payee")
        ->where("payee", $userId)
        ->orWhere("payer", $userId)->get();

        foreach($transacoes as  $key => $col){
            $col->situacao = Transacoes::getSituacao($col->situacao);
        }

        return response()->json($transacoes);
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
            "payee.required" => "Informe o destinatário do valor a ser transferido.",
            "value.required" => "Informe o valor a ser transferido!",
        ];

        $validator = Validator::make($request->all(), [
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
                "required",
                function($attr, $value, $fail){
                    if(UserWallet::existeRecursos($value) === false){
                        $fail("Saldo insuficiente para continuar!");
                    }
                    if($value < 0.5){
                        $fail("Valor mínimo por transação R$ 0,5 ");
                    }
                }
            ]
        ], $messages);

        $firstErro = $validator->errors()->first();

        if(!empty($firstErro)){
            return response()->json(["erro" => $firstErro], 422);
        }

        // Cadastra a nova transação, em andamento
        $novaTransacao = DB::table("transacoes")->insertGetId([
            "payer" => $request['payer'],
            "payee" => $request['payee'],
            "valor" => $request['value'],
            "situacao" => "1"
        ]);

        // Processo de transferência
        $transferir = UserWalletCtr::transferir([
            "payer" => $request['payer'],
            "payee" => $request['payee'],
            "value" => $request['value'],
            "id_origem" => $novaTransacao,
            "origem" => "1"
        ]);

        if($transferir){
            // Atualzia a transação em andamento, para Aprovada.
            $transacao = Transacoes::find($novaTransacao);
            $transacao->situacao = "2";
            $transacao->save();

            return response()->json(["sucesso" => "Transação concluída, transferência aprovada!"], 200);
        }
        else {
            // Atualiza a transação para Não autorizada
            $transacao = Transacoes::find($novaTransacao);
            $transacao->situacao = "5";
            $transacao->save();

            return response()->json(["erro" => "Transferência não foi autorizada!"], 422);
        }

    }

    public function estornar($idTransacao, Transacoes $transacoes){
        $transacao = $transacoes->where("id", $idTransacao)->where("situacao", "=", "2")->get();

        if(empty($transacao) == ""){
            return response()->json(["erro" => "Transação não foi encontrada"], 422);
        }

        $dados = [
            "owner" => $transacao['payer'],
            "payer" => $transacao['payee'],
            "payee" => $transacao['payer'],
            "value" => $transacao['valor']
        ];

        $validator = Validator::make($dados, [
            "owner" => function($attr, $owner, $fail){
                if(Auth::id() != $owner){ // Somente o usario que pagou pode estornar o valor.
                    $fail("Você não está habilitado para pedir o estorno!");
                }
            }
        ]);

        $firstErro = $validator->errors()->first();

        if(!empty($firstErro)){
            return response()->json(["erro" => $firstErro], 422);
        }

        $transferir = UserWalletCtr::transferir($dados);

        if($transferir){ // se autorizar, atualiza a transação para Estornado
            $transacao->situacao = "3";
            $transacao->save();

            return response()->json(["sucesso" => "Valor estornado com sucesso"], 200);
        }
        else {
            return response()->json(["erro" => "O Valor do Estorno não foi autorizado!"], 422);
        }

    }

    public function devolver($idTransacao, Transacoes $transacoes){
        $transacao = $transacoes->where("id", $idTransacao)->where("situacao", "=", "2")->get();

        if(empty($transacao) == ""){
            return response()->json(["erro" => "Transação não foi encontrada"], 422);
        }

        $dados = [
            "payer" => $transacao['payee'],
            "payee" => $transacao['payer'],
            "value" => $transacao['valor']
        ];

        $validator = Validator::make($dados, [
            "payer" => function($attr, $payer, $fail){
                if(Auth::id() != $payer){ // Somente o usario que recebeu devolver o valor.
                    $fail("Você não pode devolver esta transação.");
                }
                if(Auth::user()['tipo_conta'] == 2){
                    $fail("Seu perfil não está autorizado para este tipo de transação");
                }
            }
        ]);

        $firstErro = $validator->errors()->first();

        if(!empty($firstErro)){
            return response()->json(["erro" => $firstErro], 422);
        }

        $autorizado = UserWalletCtr::transferir($dados);

        if($autorizado){
            $transacao->situacao = "4";
            $transacao->save();

            return response()->json(["sucesso" => "Valor devolvido com sucesso!"], 200);
        }
        else {
            return response()->json(["erro" => "A Devolução não foi autorizado."], 422);
        }

    }

}
