<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableFormaPagamento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('formas_pagamentos', function (Blueprint $table) {
            $table->id();
            $table->string('descricao');
            $table->float('taxa', 8, 2)->nullable();
            $table->integer('parcelamento')->nullable();
            $table->string('observacao')->nullable();
            $table->string('bandeira')->nullable();
            $table->enum('status', ['ativo','inativo']);
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
        Schema::dropIfExists('formas_pagamentos');
    }
}
