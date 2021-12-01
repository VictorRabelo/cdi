<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKeyProdutos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->foreign('categoria_id')->references('id_categoria')->on('categorias');
            $table->foreign('data_id')->references('id_data')->on('datas');
            $table->foreign('valor_id')->references('id_valor')->on('valores');
            $table->foreign('frete_id')->references('id_frete')->on('fretes');
            $table->foreign('fornecedor_id')->references('id_fornecedor')->on('fornecedores');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('produtos', function (Blueprint $table) {
            //
        });
    }
}
