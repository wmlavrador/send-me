<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    UserCtr,
    TransacoesCtr,
    UserWalletCtr,
    LoginCtr
};

Route::prefix("user")->group(function(){
    Route::post("/create", [UserCtr::class, 'store']);
    Route::post("/login", [LoginCtr::class, 'autorizar']);
});

Route::middleware("auth:sanctum")->group(function(){

    Route::prefix("user")->group(function(){
        Route::post("/logout", [LoginCtr::class, 'logout']);
        Route::get("/me", function(Request $request){
            return $request->user();
        })->name("profile");
    });

    Route::prefix("transacoes")->group(function(){
        Route::get("/listar", [TransacoesCtr::class, 'index']);
        Route::get("/destinatarios", [TransacoesCtr::class, 'destinatarios']);

        Route::post("/estornar/{idTransacao}", [TransacoesCtr::class, "estornar"]);
        Route::post("/devolver/{idTransacao}", [TransacoesCtr::class, "devolver"]);
    });

    Route::prefix("/minhas-carteiras")->group(function(){
        Route::get("/listar", [UserWalletCtr::class, 'index']);
        Route::post("/depositar", [UserWalletCtr::class, 'depositar']);
    });

    Route::post("/transaction", [TransacoesCtr::class, 'novaTransacao']);

});
