<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash, Validator};

class LoginCtr extends Controller
{

    /**
     * Autoriza e retorna o token do usuario
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function autorizar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => "required|email",
            "password" => "required"
        ], [
            "email.required" => "Campo email obrigatório!",
            "email.email" => "Formato de email inválido!",
            "password" => "Campo senha é obrigatório!"
        ]);

        $firstError = $validator->errors()->first();
        if(!empty($firstError)){
            return response()->json(["erro" => $firstError], 422);
        }

        $user = User::where("email", $request->email)->first();

        if(!$user){
            return response()->json(["erro" => "Endereço de email não encontrado"], 422);
        }

        if(!Hash::check($request->password, $user->password)){
            return response()->json(["erro" => "Senha incorreta!"], 422);
        }

        $token = $user->createToken($request->email)->plainTextToken;

        return response()->json([
            "accessToken" => $token,
            "typeAuth" => "Bearer"
        ]);

    }

    /**
     * Revoga o token do usuario autorizado
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::user()->tokens()->delete();
        return response()->json(["sucesso" => "Desconectado com sucesso."], 200);
    }
}
