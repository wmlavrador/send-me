<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\PasswordValidationRules;
use App\Models\UserWallet;
use App\Rules\ChecarDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\User;

class UserCtr extends Controller
{
    use PasswordValidationRules;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validarDoc = new ChecarDocumento();

        $request['tipo_conta'] = $validarDoc->validarCPF($request['documento']) ? '1' : '2';
        $request['documento'] = preg_replace('/[^0-9]/', '', (string) $request['documento']);

        $messages = [
            "nome_cimpleto.required" => "Informe seu Nome Completo.",
            "email.unique" => "Este e-mail já está cadastrado.",
            "email.email" => "Formato de e-mail incorreto.",
            "documento.required" => "O Campo documento é Obrigatório",
            "documento.unique" => "Já existe cadastro com este Documento.",
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
            ]
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

        if($newUserId){
            return response(["sucesso" => "Registrado com sucesso"], 200);
        }
        else {
            return response(["erro" => "Algo de errado ao cadastrar, tente novamente mais tarde."], 419);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
