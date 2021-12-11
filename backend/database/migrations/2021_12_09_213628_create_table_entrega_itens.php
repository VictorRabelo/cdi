<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableEntregaItens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entrega_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entrega_id');
            $table->foreignId('produto_id')->nullable();

            $table->integer('qtd_produto');
            $table->integer('qtd_disponivel');
            
            $table->float('lucro_entrega', 8, 2);
            $table->float('preco_entrega', 8, 2);

            $table->timestamps();

            $table->foreign('entrega_id')->references('id_entrega')->on('entregas');
            $table->foreign('produto_id')->references('id_produto')->on('produtos')->onDelete('SET NULL')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entrega_itens', function (Blueprint $table) {
            Schema::dropIfExists('entrega_itens');
        });
    }
}
