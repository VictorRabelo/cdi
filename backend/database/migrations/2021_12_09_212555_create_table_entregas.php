<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableEntregas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entregas', function (Blueprint $table) {
            $table->id('id_entrega');
            $table->foreignId('entregador_id')->nullable();
            $table->float('total_final', 8, 2)->default(0.00)->nullable();
            $table->float('lucro', 8, 2)->default(0.00)->nullable();
            $table->integer('qtd_produtos')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();

            $table->foreign('entregador_id')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entregas', function (Blueprint $table) {
            Schema::dropIfExists('entregas');
        });
    }
}
