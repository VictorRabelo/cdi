<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnEntregaIdInTableVendas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendas', function (Blueprint $table) {
            $table->foreignId('entrega_id')->nullable();
            $table->foreign('entrega_id')->references('id_entrega')->on('entregas')->onDelete('SET NULL')->onUpdate('cascade')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendas', function (Blueprint $table) {
            $table->dropForeign(['entrega_id']);
        });
    }
}
