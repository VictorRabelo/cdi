<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDespesaEntrega extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('despesa_entregas', function (Blueprint $table) {
            $table->id('id_despesaEntrega');
            $table->foreignId('entregador_id')->nullable();
            $table->float('valor', 8, 2);
            $table->string('descricao');
            $table->date('data');
            $table->timestamps();

            $table->foreign('entregador_id')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('cascade')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('desespesa_entrega');
    }
}
