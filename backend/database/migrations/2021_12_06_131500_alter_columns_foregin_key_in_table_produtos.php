<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnsForeginKeyInTableProdutos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->dropForeign(['categoria_id']);
            $table->dropForeign(['fornecedor_id']);
            
            $table->foreignId('fornecedor_id')->nullable()->change();
            $table->foreignId('categoria_id')->nullable()->change();

            $table->foreign('categoria_id')->references('id_categoria')->on('categorias')->onDelete('SET NULL')->onUpdate('cascade')->change()->nullable();
            $table->foreign('fornecedor_id')->references('id_fornecedor')->on('fornecedores')->onDelete('SET NULL')->onUpdate('cascade')->change()->nullable();
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
