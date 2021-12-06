<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnProdutoIdInTableEstoque extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('estoques', function (Blueprint $table) {
            $table->dropForeign(['produto_id']);
            $table->foreign('produto_id')->references('id_produto')->on('produtos')->onDelete('cascade')->onUpdate('cascade')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('estoque', function (Blueprint $table) {
            //
        });
    }
}
