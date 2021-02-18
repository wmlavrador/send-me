<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\PasswordValidationRules;
use App\Models\{User, UserWallet};
use App\Rules\ChecarDocumento;
use Illuminate\Support\Facades\{DB, Hash, Validator};
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserCtr extends Controller
{
    use PasswordValidationRules;

    /**
     * Cria novo usuário
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validarDoc = new ChecarDocumento();

        $request['tipo_conta'] = $validarDoc->validarCPF($request['documento']) ? '1' : '2';
        $request['documento'] = preg_replace('/[^0-9]/', '', (string) $request['documento']);

        $messages = [
            "nome_completo.required" => "Informe seu Nome Completo.",
            "email.unique" => "Este e-mail já está cadastrado.",
            "email.email" => "Formato de e-mail incorreto.",
            "documento.required" => "O Campo documento é Obrigatório",
            "documento.unique" => "Já existe cadastro com este Documento.",
            "password.required" => "Campo :attribute é obrigatório",
            "password_confirmation.required" => "O Campo :attribute é obrigatório."
        ];

        Validator::make($request->all(), [
            'nome_completo' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
            'documento' => [
                'required',
                Rule::unique(User::class, "documento"),
                new ChecarDocumento
            ],
            "password_confirmation" => "required"
        ], $messages)->validate();

        $newUserId = DB::table("users")->insertGetId([
            'nome_completo' => $request['nome_completo'],
            'documento' => $request['documento'],
            'tipo_conta' => $request['tipo_conta'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
        ]);

        UserWallet::create([
            'descricao' => "Carteira Debito",
            'tipo_carteira' => "1",
            'saldo' => 0,
            'user_id' => $newUserId
        ]);

        if($newUserId)
        {
            $newUser = User::where("documento", $request['documento'])->first();
            $accessToken = $newUser->createToken($request['email'])->plainTextToken;

            return response()->json([
                "sucesso" => "Registrado com sucesso",
                "accessToken" => $accessToken,
                "typeAuth" => "Bearer"
            ], 200);
        }
        else {
            return response()->json(
                ["erro" => "Algo de errado ao cadastrar, tente novamente mais tarde."],
                422
            );
        }
    }

}
