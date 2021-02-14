<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginCtr;

Route::post("/login", [LoginCtr::class, "autenticar"]);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
