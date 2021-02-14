<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use \App\Http\Controllers\UserCtr;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name("home");

Route::get("/login", function (){
    return view("painel.login");
})->name("login.index");

Route::post("/user/create", [UserCtr::class, 'store']);

Route::middleware("auth:sanctum")->prefix("admin")->group(function(){

});
