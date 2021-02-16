<?php

namespace App\Http\Controllers;

use App\Events\NovaTransacao;
use App\Models\Notificacoes;
use Illuminate\Support\Facades\Http;

class NotificacoesCtr extends Controller
{

    public static function novaNotificacao($input = array()){

        $mokyNotify = "https://run.mocky.io/v3/b19f7b9f-9cbf-4fc6-ad22-dc30601aec04";
        $response = Http::get($mokyNotify);

        if($response['message'] == "Enviado" && $response->successful()){
            event( new NovaTransacao($input['mensagem'], $input['payee']));
            $input['status'] = 2;
        }
        else {
            $input['status'] = 1;
        }

        return Notificacoes::create([
            "origem" => $input['origem'],
            "id_origem" => $input['id_origem'],
            "mensagem" => $input["mensagem"],
            "situacao" => $input['status'],
            "user_id" => $input['payee'],
        ]);

    }

}
