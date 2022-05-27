<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CrearPedidoReposiciondetHistoricTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Pedido_reposicion_det_historic', function (Blueprint $table) {
            $table->increments('pedido_reposicion_det_historic_id');
            $table->integer('pedido_repo_id')->nullable();
            $table->integer('pedido_repo_det_id')->nullable();
            $table->integer('usuario_id')->nullable();
            $table->char('num_linea', 10)->nullable();
            $table->char('cod_producto', 18)->nullable();
            $table->char('cod_producto2', 18)->nullable();
            $table->char('um_repo', 4)->nullable();
            $table->char('cod_ean', 18)->nullable();
            $table->char('cod_almacen_erp', 18)->nullable();
            $table->char('cant_pack', 10)->nullable();
            $table->char('um_pack', 4)->nullable();
            $table->char('familia', 15)->nullable();
            $table->char('cant_reponer', 10)->nullable();
            $table->char('cod_hospital', 4)->nullable();
            $table->char('cod_servicio', 4)->nullable();
            $table->char('cod_centro_coste', 30)->nullable();
            $table->char('cod_gfh', 10)->nullable();
            $table->char('cod_almacen', 30)->nullable();
            $table->char('fecha', 10)->nullable();
            $table->char('factor_convers_um', 10)->nullable();
            $table->char('codigo_urgencia', 10)->nullable();
            $table->char('cod_lote', 10)->nullable();
            $table->char('atributo1', 15)->nullable();
            $table->char('atributo2', 15)->nullable();
            $table->char('atributo3', 15)->nullable();
            $table->integer('stock_id')->nullable();
            $table->char('ind_no_almacenable', 1)->nullable();
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
        //
    }
}
