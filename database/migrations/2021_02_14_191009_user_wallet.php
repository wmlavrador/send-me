<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserWallet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("user_wallet", function(Blueprint $table){
            $table->id();
            $table->string("descricao")->nullable();
            $table->double('saldo', 14,4);
            $table->string("tipo_carteira")->default("debito");
            $table->foreignId("user_id")->constrained()->onDelete("cascade");
            $table->softDeletes('deleted_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_wallet');
    }
}
