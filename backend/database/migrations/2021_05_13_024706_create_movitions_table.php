<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movitions', function (Blueprint $table) {
            $table->id('id_movition');
            $table->foreignId('venda_id')->nullable();
            $table->date('data')->now();
            $table->float('valor', 8, 2)->nullable();
            $table->string('descricao')->nullable();
            $table->enum('tipo', ['entrada','saida'])->nullable();
            $table->enum('status', ['eletronico','geral'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movitions');
    }
}
