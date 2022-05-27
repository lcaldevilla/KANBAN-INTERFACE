<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PedidoReposicionHistoric extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Pedido_reposicion_historic', function (Blueprint $table) {
            $table->increments('pedido_reposicion_historic_id');
            $table->integer('pedido_repo_id')->nullable();
            $table->integer('usuario_id')->nullable();
            $table->dateTime('fecha_creacion', 0)->nullable();
            $table->char('estado', 10)->nullable();
            $table->dateTime('fecha_fin', 0)->nullable();
            $table->char('cod_error', 4)->nullable();
            $table->char('dsc_error', 500)->nullable();
            $table->char('cod_archivo', 30)->nullable();
            $table->integer('tipo_pedido');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('Pedido_reposicion_historic');
    }
}
