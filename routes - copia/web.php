<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConsumosController;
use App\Http\Controllers\consumosAgrupadosController;
use App\Http\Controllers\consumosPorAlmacenController;
use App\Http\Controllers\ConsumosUrgentesController;
use App\Http\Controllers\consumosAgrupadosUrgentesController;
use App\Http\Controllers\SinConsumosController;
use App\Http\Controllers\GraficaNormalesController;
use App\Http\Controllers\GraficaArticulosController;
use App\Http\Controllers\GraficaUrgentesController;
use App\Http\Controllers\ReposicionesController;
//use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// pantalla inicial de entrada --------------------------------------------------------------------------------------
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('index');
//Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('index');
Route::get('/update', [App\Http\Controllers\HomeController::class, 'indexUpdate'])->name('index.update');

// rutas de las paginas de consumos de articulos normales ----------------------------------------------------------
Route::get('consumos', [ConsumosController::class, 'index'])->name('consumos.index');
// gestiona el cambio de los servicios y almacenes
Route::post('consumos', [ConsumosController::class, 'ajaxRequestPost'])->name('consumos.post');
// gestiona la actualización de la tabla según los filtros
Route::post('consumos/update', [ConsumosController::class, 'ajaxRequestPostUpdate'])->name('consumos.update');

// rutas de las paginas de consumos de articulos urgentes ----------------------------------------------------------
Route::get('consumosUrgentes', [ConsumosUrgentesController::class, 'index'])->name('consumosUrgentes.index');
// gestiona el cambio de los servicios y almacenes
Route::post('consumosUrgentes', [ConsumosUrgentesController::class, 'ajaxRequestPost'])->name('consumosUrgentes.post');
// gestiona la actualización de la tabla según los filtros
Route::post('consumosUrgentes/update', [ConsumosUrgentesController::class, 'ajaxRequestPostUpdate'])->name('consumosUrgentes.update');

// rutas de las paginas de consumos de articulos normales agrupados----------------------------------------------------------
Route::get('consumosAgrupados', [consumosAgrupadosController::class, 'index'])->name('consumosAgrupados.index');
// gestiona el cambio de los servicios y almacenes
Route::post('consumosAgrupados', [consumosAgrupadosController::class, 'ajaxRequestPost'])->name('consumosAgrupados.post');
// gestiona la actualización de la tabla según los filtros
Route::post('consumosAgrupados/update', [consumosAgrupadosController::class, 'ajaxRequestPostUpdate'])->name('consumosAgrupados.update');

// rutas de las paginas de consumos de articulos urgentes Agrupados ----------------------------------------------------------
Route::get('consumosAgrupadosUrgentes', [consumosAgrupadosUrgentesController::class, 'index'])->name('consumosAgrupadosUrgentes.index');
// gestiona el cambio de los servicios y almacenes
Route::post('consumosAgrupadosUrgentes', [consumosAgrupadosUrgentesController::class, 'ajaxRequestPost'])->name('consumosAgrupadosUrgentes.post');
// gestiona la actualización de la tabla según los filtros
Route::post('consumosAgrupadosUrgentes/update', [consumosAgrupadosUrgentesController::class, 'ajaxRequestPostUpdate'])->name('consumosAgrupadosUrgentes.update');

// rutas de la grafica de consumos normales
Route::get('graficaNormales', [GraficaNormalesController::class, 'index'])->name('graficaNormales.index');
Route::post('graficaNormales', [GraficaNormalesController::class, 'almacenesPost'])->name('graficaNormales.almacenesPost');
Route::post('graficaNormales/update', [GraficaNormalesController::class, 'update'])->name('graficaNormales.update');
Route::post('graficaNormales/compara', [GraficaNormalesController::class, 'compara'])->name('graficaNormales.compara');

// rutas de la grafica de consumos urgentes
Route::get('graficaUrgentes', [GraficaUrgentesController::class, 'index'])->name('graficaUrgentes.index');
Route::post('graficaUrgentes', [GraficaUrgentesController::class, 'almacenesPost'])->name('graficaUrgentes.almacenesPost');
Route::post('graficaUrgentes/update', [GraficaUrgentesController::class, 'update'])->name('graficaUrgentes.update');
Route::post('graficaUrgentes/compara', [GraficaUrgentesController::class, 'compara'])->name('graficaUrgentes.compara');

// rutas de la grafica de consumos por articulo
Route::get('graficaArticulos', [GraficaArticulosController::class, 'index'])->name('graficaArticulos.index');
Route::post('graficaArticulos', [GraficaArticulosController::class, 'almacenesPost'])->name('graficaArticulos.almacenesPost');
Route::post('graficaArticulos/update', [GraficaArticulosController::class, 'update'])->name('graficaArticulos.update');
Route::post('graficaArticulos/compara', [GraficaArticulosController::class, 'compara'])->name('graficaArticulos.compara');

// Rutas de la pagina de articulos sin consumo -----------------------------------------------------------------------
Route::get('sinConsumos', [SinConsumosController::class, 'index'])->name('sinConsumos.index');
Route::post('sinConsumos/update', [SinConsumosController::class, 'update'])->name('sinConsumos.update');

// Rutas de la pagina de consumos totales por almacen -----------------------------------------------------------------------
Route::get('consumosPorAlmacen', [ConsumosPorAlmacenController::class, 'index'])->name('consumosPorAlmacen.index');
Route::post('consumosPorAlmacen', [ConsumosPorAlmacenController::class, 'ajaxRequestPost'])->name('consumosPorAlmacen.post');
Route::post('consumosPorAlmacen/update', [ConsumosPorAlmacenController::class, 'update'])->name('consumosPorAlmacen.update');

// Rutas de la pagina de reposiciones -----------------------------------------------------------------------
Route::get('reposiciones', [ReposicionesController::class, 'index'])->name('reposiciones.index');
Route::post('reposiciones', [ReposicionesController::class, 'ajaxRequestPost'])->name('reposiciones.post');
Route::post('reposiciones/update', [ReposicionesController::class, 'update'])->name('reposiciones.update');
Route::post('reposiciones/procesar', [ReposicionesController::class, 'procesar'])->name('reposiciones.procesar');
Route::post('reposiciones/actualizarCantReposicion', [ReposicionesController::class, 'actualizarCantReposicion'])->name('reposiciones.actualizarCantReposicion');
Route::post('reposiciones/eliminar', [ReposicionesController::class, 'eliminar'])->name('reposiciones.eliminar');

// rutas de autenticación -------------------------------------------------------------------------------------------
Auth::routes();
Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');


