<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginCtr;
use App\Http\Controllers\UserCtr;

Route::prefix("user")->group(function(){
    Route::post("/create", [UserCtr::class, 'store']);
});

Route::middleware("auth:sanctum")->group(function(){

    Route::prefix("user")->group(function(){
        Route::get("/me", function(Request $request){
            return $request->user();
        })->name("profile");
    });

    Route::prefix("transacoes")->group(function(){
        Route::get("/listar");
    });

});
