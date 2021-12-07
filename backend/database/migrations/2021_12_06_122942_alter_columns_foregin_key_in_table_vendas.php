<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnsForeginKeyInTableVendas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendas', function (Blueprint $table) {
            $table->dropForeign(['vendedor_id']);
            $table->dropForeign(['cliente_id']);
            
            $table->foreignId('vendedor_id')->nullable()->change();
            $table->foreignId('cliente_id')->nullable()->change();

            $table->foreign('vendedor_id')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('cascade')->change()->nullable();
            $table->foreign('cliente_id')->references('id_cliente')->on('clientes')->onDelete('SET NULL')->onUpdate('cascade')->change()->nullable();
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
            //
        });
    }
}
