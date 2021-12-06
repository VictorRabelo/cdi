<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendas', function (Blueprint $table) {
            $table->id('id_venda');
            $table->foreignId('vendedor_id');
            $table->foreignId('cliente_id')->nullable();
            $table->float('total_final', 8, 2)->nullable();
            $table->float('lucro', 8, 2)->nullable();
            $table->float('pago', 8, 2)->nullable();
            $table->float('restante', 8, 2)->nullable();
            $table->integer('qtd_produto')->nullable();
            $table->string('pagamento', ['dinheiro','debito','credito', 'pix'])->nullable();
            $table->enum('status', ['pago','pendente'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendas');
    }
}
