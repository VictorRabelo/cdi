<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFretesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fretes', function (Blueprint $table) {
            $table->id('id_frete');
            $table->float('frete_mia_pjc', 8, 2)->nullable();
            $table->float('frete_pjc_gyn', 8, 2)->nullable();
            $table->float('dolar_frete', 8, 2)->nullable();
            $table->float('total_frete_mia_pjc', 8, 2)->nullable();
            $table->float('total_frete', 8, 2)->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fretes');
    }
}
