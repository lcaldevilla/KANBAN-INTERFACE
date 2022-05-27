<?php

namespace App\Http\Controllers;

use App\Models\Pedido_reposicion;
use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
//use Maatwebsite\Excel\Facades\Excel;
//use Maatwebsite\Excel\Concerns\Exportable;
//use App\Exports\ConsumosExport;


use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

//use Yajra\Datatables\Datatables;

class ReposicionesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function ajaxRequest()
    {
        return view('ajaxRequest');
    }

    // obtiene el ultimo indice de fichero
    public function DevuelveUltimoIndiceFichero()
    {
        $pedido_reposicion = DB::table('PEDIDO_REPOSICION')
            ->orderBy('pedido_repo_id', 'DESC')
            ->get();

        if (count($pedido_reposicion) > 0) {
            $fichero =  $pedido_reposicion[0]->cod_archivo;
            $ficheroSplit = explode("_", $fichero);
            $ficheroSinExtension = explode(".", $ficheroSplit[2]);
           // error_log("indice: " . $ficheroSinExtension[0]);
           return $ficheroSinExtension[0];
        }
        return "0";
    }

    // recibe el filtro de servicio por ajax
    public function ajaxRequestPost(Request $request)
    {
        $input = $request->status;

        $almacenes = DB::table('ALMACEN as a')
            ->join('SERVICIO as s', 's.servicio_id', '=', 'a.servicio_id')
            ->select('almacen_id', 'dsc_almacen', 's.cod_servicio', 'a.cod_almacen')
            ->where('s.cod_servicio', '=', $input)
            ->get();

        return response()->json(['almacenes' => $almacenes]);
    }

    // actualiza la tabla de consumos según los filtros elegidos
    // si los valores recibidos en almacen y servicios son 0 mostramos todos
    public function update(Request $request)
    {
        // variables recibidas
        $input = $request->status;
        $almacen = $request->input('almacen');
        $servicio = $request->input('servicio');

        $aux_array_servicio = array();
        $aux_array_almacen = array();
        $matches = array();

        if ($servicio != '0') { // todos los servicios
            array_push($aux_array_servicio, 's.cod_servicio', '=', $servicio);
            array_push($matches, $aux_array_servicio);
        }

        if ($almacen != '0') { // todos los almacenes
            array_push($aux_array_almacen, 'a.cod_almacen', '=', $almacen);
            array_push($matches, $aux_array_almacen);
        }


        $consumos = DB::table('MAPA_REPOSICIONES as mr')
            ->join('CONTENEDOR as c', 'c.cod_tag', '=', 'mr.cod_tag')
            ->join('UBICACION as u', 'u.ubicacion_id', '=', 'c.ubicacion_id')
            ->join('ALMACEN as a', 'a.almacen_id', '=', 'u.almacen_id')
            ->join('SERVICIO as s', 's.servicio_id', '=', 'a.servicio_id')
            ->join('PRODUCTO as p', 'p.cod_producto', '=', DB::raw("SUBSTRING(c.cod_contenedor,5, LEN(c.cod_contenedor)-5 )"))
            ->select('s.cod_servicio', DB::raw("FORMAT(mr.fecha_creacion, 'dd-MM-yyyy') as fecha_creacion"), 'a.cod_almacen', 'p.cod_producto', 'p.dsc_producto', 'u.cod_ubicacion', 'mr.cant_reposicion', 'c.nivel_urgencia', 'mr.estado_reposicion', 'mr.stock_id')
            ->where($matches)
            ->orderBy('a.cod_almacen')
            ->get();


        $total_articulos = DB::table('MAPA_REPOSICIONES as mr')
            ->leftJoin('CONTENEDOR as c', 'c.cod_tag', '=', 'mr.cod_tag')
            ->leftJoin('UBICACION as u', 'u.ubicacion_id', '=', 'c.ubicacion_id')
            ->leftJoin('ALMACEN as a', 'a.almacen_id', '=', 'u.almacen_id')
            ->leftJoin('SERVICIO as s', 's.servicio_id', '=', 'a.servicio_id')
            ->leftJoin('PRODUCTO as p', 'p.cod_producto', '=', DB::raw("SUBSTRING(c.cod_contenedor,5, LEN(c.cod_contenedor)-5 )"))
            ->where($matches)
            ->select(DB::raw('sum(convert(int, cant_reposicion)) as unidades'))
            ->get();

        $total_Registros = count($consumos);
        // error_log("update view: ".count($total_articulos));
        return view('reposiciones_pagination', compact('consumos', 'total_Registros', 'total_articulos'))->render();
    }

    // Procesa los articulos seleccionados
    public function procesar(Request $request)
    {
        
        $aux_array_stock_id = array();
        $aux_array_almacen = array();
        $matches = array();
        $input = $request->status;
        $almacen_input = $request->input('almacen');
        $pedidoReposicionGenerado = false;

        $user = Auth::user();
        $id = Auth::id(); // usuario que está logado

        // recupero la lista de almacenes del sistema para revisar si tengo solicitudes de cada uno de ellos
        $almacenes = DB::table('ALMACEN')
            ->select('almacen_id', 'dsc_almacen', 'cod_almacen')
            ->get();

        // id de los articulos en mapa reposiciones que hay que cambiar a ENVIADO
        // previamente es neceario generar el txt para la generación del PDF

        // obtengo la ruta de generación del txt
        $path_envio_erp = DB::table('HOSPITAL')
            ->select('path_envio_erp')
            ->get();

        $values = $request->input('selected');
        $fichero_pedido = "";

        error_log("------------------------------------------------------------");
        foreach ($almacenes as $almacen) {
            foreach ($values as $value) {
                if ($value != 'on') {
                    error_log("almacen:" . $almacen->cod_almacen);
                    array_push($aux_array_stock_id, 'mr.stock_id', '=', $value);
                    array_push($aux_array_almacen, 'a.cod_almacen', '=', $almacen->cod_almacen);

                    array_push($matches, $aux_array_stock_id);
                    array_push($matches, $aux_array_almacen);

                    $articulo = DB::table('MAPA_REPOSICIONES as mr')
                        ->join('CONTENEDOR as c', 'c.cod_tag', '=', 'mr.cod_tag')
                        ->join('UBICACION as u', 'u.ubicacion_id', '=', 'c.ubicacion_id')
                        ->join('ALMACEN as a', 'a.almacen_id', '=', 'u.almacen_id')
                        ->join('SERVICIO as s', 's.servicio_id', '=', 'a.servicio_id')
                        ->join('PRODUCTO as p', 'p.cod_producto', '=', DB::raw("SUBSTRING(c.cod_contenedor,5, LEN(c.cod_contenedor)-5 )"))
                        ->select('s.cod_centro_coste', 'a.cod_almacen', 'p.cod_producto', 'c.nivel_urgencia', 'p.familia', 'u.ind_no_almacenable', 'mr.cant_reposicion', 'mr.fecha_creacion')
                        ->where($matches)
                        ->get();

                    if (count($articulo) > 0) {
                        // variables fichero de pedidos
                        $centro_coste = $articulo[0]->cod_centro_coste;
                        $cod_almacen = $articulo[0]->cod_almacen;
                        $cod_articulo = str_pad($articulo[0]->cod_producto, 8);
                        $nivel_urgencia = $articulo[0]->nivel_urgencia;
                        $familia = str_pad($articulo[0]->familia, 8);
                        $almacenable = $articulo[0]->ind_no_almacenable;
                        $cant_reposicion = explode(".", $articulo[0]->cant_reposicion)[0];
                        $fichero_pedido = $fichero_pedido . $centro_coste . $cod_almacen . $cod_articulo . $nivel_urgencia . $familia . $almacenable . $cant_reposicion . PHP_EOL; // entrada en el fichero de pedido
                        //error_log("fecha: " . $articulo[0]->fecha_creacion);
                        
                        
                        // Después de generar el fichero de pedido pasamos los movimientos al historico
                        if ($pedidoReposicionGenerado == false) { // genero la cabecera del pedido de reposicion
                            $ultimoIndiceFichero = ((int) $this->DevuelveUltimoIndiceFichero())+1;
                            $fechaActual = date('Ymd');
                            $pedidoReposicionInsert = DB::table('PEDIDO_REPOSICION')
                                ->insert(['usuario_id' => $id, 'fecha_creacion' => $articulo[0]->fecha_creacion, 'estado' => 'FINALIZADO', 'fecha_fin' => '2021-05-26 09:16:35.857', 'cod_archivo' => $cod_almacen."_".$fechaActual."_".$ultimoIndiceFichero.".txt", 'tipo_pedido' => 1]);
                            $pedidoReposicionGenerado = true;
                        }
                    }
                    // limpio los array para la siguiente iteración
                    $aux_array_stock_id = array();
                    $aux_array_almacen = array();
                    $matches = array();

                    // Una vez generados los ficheros tenemos que actualizar el estado en la BBDD a "ENVIADO"
                    error_log("stock_id:" . $value);
                    //error_log("fecha: ".$articulo[0]->fecha_creacion);
                    $actualizacion = DB::table('MAPA_REPOSICIONES')
                        ->where('stock_id', $value)
                        ->update(['estado_reposicion' => 'ENVIADO']);
                }
            }
            if (strlen($fichero_pedido) > 0) { // tenemos contenido
                $file = fopen($path_envio_erp[0]->path_envio_erp . "\\" . $almacen->cod_almacen . "archivo.txt", "w"); // fichero del pedido a convertir en pdf
                fwrite($file, $fichero_pedido);
                fclose($file); // cerramos el fichero del pedido
                $fichero_pedido = "";
            }
        }


        // recargamos la tabla
        $consumos = DB::table('MAPA_REPOSICIONES as mr')
            ->join('CONTENEDOR as c', 'c.cod_tag', '=', 'mr.cod_tag')
            ->join('UBICACION as u', 'u.ubicacion_id', '=', 'c.ubicacion_id')
            ->join('ALMACEN as a', 'a.almacen_id', '=', 'u.almacen_id')
            ->join('SERVICIO as s', 's.servicio_id', '=', 'a.servicio_id')
            ->join('PRODUCTO as p', 'p.cod_producto', '=', DB::raw("SUBSTRING(c.cod_contenedor,5, LEN(c.cod_contenedor)-5 )"))
            ->select('s.cod_servicio', DB::raw("FORMAT(mr.fecha_creacion, 'dd-MM-yyyy') as fecha_creacion"), 'a.cod_almacen', 'p.cod_producto', 'p.dsc_producto', 'u.cod_ubicacion', 'mr.cant_reposicion', 'c.nivel_urgencia', 'mr.estado_reposicion', 'mr.stock_id')
            ->orderBy('a.cod_almacen')
            ->get();

        $total_articulos = DB::table('MAPA_REPOSICIONES as mr')
            ->leftJoin('CONTENEDOR as c', 'c.cod_tag', '=', 'mr.cod_tag')
            ->leftJoin('UBICACION as u', 'u.ubicacion_id', '=', 'c.ubicacion_id')
            ->leftJoin('ALMACEN as a', 'a.almacen_id', '=', 'u.almacen_id')
            ->leftJoin('SERVICIO as s', 's.servicio_id', '=', 'a.servicio_id')
            ->leftJoin('PRODUCTO as p', 'p.cod_producto', '=', DB::raw("SUBSTRING(c.cod_contenedor,5, LEN(c.cod_contenedor)-5 )"))
            ->select(DB::raw('sum(convert(int, cant_reposicion)) as unidades'))
            ->get();


        $total_Registros = count($consumos);

        return view('reposiciones_pagination', compact('consumos', 'total_Registros', 'total_articulos'))->render();
    }

    public function actualizarCantReposicion(Request $request)
    {
        $input = $request->status;
        $cant_reposicion = $request->input('cant_reposicion');
        $stock_id = $request->input('stock_id');

        $actualizacion = DB::table('MAPA_REPOSICIONES')
            ->where('stock_id', $stock_id)
            ->update(['cant_reposicion' => $cant_reposicion]);
    }

    public function eliminar(Request $request)
    {
        
        $aux_array_stock_id = array();
        $aux_array_almacen = array();
        $matches = array();
        $input = $request->status;
        $almacen_input = $request->input('almacen');

        // recupero la lista de almacenes del sistema para revisar si tengo solciitudes de cada uno de ellos
        $almacenes = DB::table('ALMACEN')
            ->select('almacen_id', 'dsc_almacen', 'cod_almacen')
            ->get();

        // id de los articulos en mapa reposiciones que hay que cambiar a ENVIADO
        // previamente es neceario generar el txt para la generación del PDF

        // obtengo la ruta de generación del txt
        $path_envio_erp = DB::table('HOSPITAL')
            ->select('path_envio_erp')
            ->get();

        $values = $request->input('selected');
        $fichero_pedido = "";

        error_log("------------------------------------------------------------");
        foreach ($almacenes as $almacen) {
            foreach ($values as $value) {

                if ($value != 'on') {
                    error_log("almacen:" . $almacen->cod_almacen);
                    array_push($aux_array_stock_id, 'mr.stock_id', '=', $value);
                    array_push($aux_array_almacen, 'a.cod_almacen', '=', $almacen->cod_almacen);

                    array_push($matches, $aux_array_stock_id);
                    array_push($matches, $aux_array_almacen);

                    $articulo = DB::table('MAPA_REPOSICIONES as mr')
                        ->join('CONTENEDOR as c', 'c.cod_tag', '=', 'mr.cod_tag')
                        ->join('UBICACION as u', 'u.ubicacion_id', '=', 'c.ubicacion_id')
                        ->join('ALMACEN as a', 'a.almacen_id', '=', 'u.almacen_id')
                        ->join('SERVICIO as s', 's.servicio_id', '=', 'a.servicio_id')
                        ->join('PRODUCTO as p', 'p.cod_producto', '=', DB::raw("SUBSTRING(c.cod_contenedor,5, LEN(c.cod_contenedor)-5 )"))
                        ->select('s.cod_centro_coste', 'a.cod_almacen', 'p.cod_producto', 'c.nivel_urgencia', 'p.familia', 'u.ind_no_almacenable', 'mr.cant_reposicion')
                        ->where($matches)
                        ->get();

                    if (count($articulo) > 0) {
                        // variables fichero de pedidos
                        $centro_coste = $articulo[0]->cod_centro_coste;
                        $cod_almacen = $articulo[0]->cod_almacen;
                        $cod_articulo = str_pad($articulo[0]->cod_producto, 8);
                        $nivel_urgencia = $articulo[0]->nivel_urgencia;
                        $familia = str_pad($articulo[0]->familia, 8);
                        $almacenable = $articulo[0]->ind_no_almacenable;
                        $cant_reposicion = explode(".", $articulo[0]->cant_reposicion)[0];
                        $fichero_pedido = $fichero_pedido . $centro_coste . $cod_almacen . $cod_articulo . $nivel_urgencia . $familia . $almacenable . $cant_reposicion . PHP_EOL; // entrada en el fichero de pedido
                    }
                    // limpio los array para la siguiente iteración
                    $aux_array_stock_id = array();
                    $aux_array_almacen = array();
                    $matches = array();

                    // Una vez generados los ficheros tenemos que actualizar el estado en la BBDD a "ENVIADO"
                    error_log("stock_id:" . $value);
                    /*$actualizacion = DB::table('MAPA_REPOSICIONES')
                        ->where('stock_id', $value)
                        ->update(['estado_reposicion' => 'ENVIADO']);*/
                    $actualizacion = DB::table('MAPA_REPOSICIONES')
                        ->where('stock_id', $value)
                        ->delete();
                }
            }
            if (strlen($fichero_pedido) > 0) { // tenemos contenido
                $file = fopen($path_envio_erp[0]->path_envio_erp . "\\" . $almacen->cod_almacen . "archivo.txt", "w"); // fichero del pedido a convertir en pdf
                fwrite($file, $fichero_pedido);
                fclose($file); // cerramos el fichero del pedido
                $fichero_pedido = "";
            }
        }


        // recargamos la tabla
        $consumos = DB::table('MAPA_REPOSICIONES as mr')
            ->join('CONTENEDOR as c', 'c.cod_tag', '=', 'mr.cod_tag')
            ->join('UBICACION as u', 'u.ubicacion_id', '=', 'c.ubicacion_id')
            ->join('ALMACEN as a', 'a.almacen_id', '=', 'u.almacen_id')
            ->join('SERVICIO as s', 's.servicio_id', '=', 'a.servicio_id')
            ->join('PRODUCTO as p', 'p.cod_producto', '=', DB::raw("SUBSTRING(c.cod_contenedor,5, LEN(c.cod_contenedor)-5 )"))
            ->select('s.cod_servicio', DB::raw("FORMAT(mr.fecha_creacion, 'dd-MM-yyyy') as fecha_creacion"), 'a.cod_almacen', 'p.cod_producto', 'p.dsc_producto', 'u.cod_ubicacion', 'mr.cant_reposicion', 'c.nivel_urgencia', 'mr.estado_reposicion', 'mr.stock_id')
            ->orderBy('a.cod_almacen')
            ->get();

        $total_articulos = DB::table('MAPA_REPOSICIONES as mr')
            ->leftJoin('CONTENEDOR as c', 'c.cod_tag', '=', 'mr.cod_tag')
            ->leftJoin('UBICACION as u', 'u.ubicacion_id', '=', 'c.ubicacion_id')
            ->leftJoin('ALMACEN as a', 'a.almacen_id', '=', 'u.almacen_id')
            ->leftJoin('SERVICIO as s', 's.servicio_id', '=', 'a.servicio_id')
            ->leftJoin('PRODUCTO as p', 'p.cod_producto', '=', DB::raw("SUBSTRING(c.cod_contenedor,5, LEN(c.cod_contenedor)-5 )"))
            ->select(DB::raw('sum(convert(int, cant_reposicion)) as unidades'))
            ->get();


        $total_Registros = count($consumos);

        return view('reposiciones_pagination', compact('consumos', 'total_Registros', 'total_articulos'))->render();
    }

    // Se llama al inicio, devuelve los articulos que estan siendo solicitados
    public function index()
    {
        /*$matches = array();
        $aux_array_pendiente = array();

        array_push($aux_array_pendiente, 'mr.estado_reposicion', '=', 'PENDIENTE');*/

        $consumos = DB::table('MAPA_REPOSICIONES as mr')
            ->join('CONTENEDOR as c', 'c.cod_tag', '=', 'mr.cod_tag')
            ->join('UBICACION as u', 'u.ubicacion_id', '=', 'c.ubicacion_id')
            ->join('ALMACEN as a', 'a.almacen_id', '=', 'u.almacen_id')
            ->join('SERVICIO as s', 's.servicio_id', '=', 'a.servicio_id')
            ->join('PRODUCTO as p', 'p.cod_producto', '=', DB::raw("SUBSTRING(c.cod_contenedor,5, LEN(c.cod_contenedor)-5 )"))
            ->select('s.cod_servicio', DB::raw("FORMAT(mr.fecha_creacion, 'dd-MM-yyyy') as fecha_creacion"), 'a.cod_almacen', 'p.cod_producto', 'p.dsc_producto', 'u.cod_ubicacion', 'mr.cant_reposicion', 'c.nivel_urgencia', 'mr.estado_reposicion', 'mr.stock_id')
            ->orderBy('a.cod_almacen')
            ->get();

        $servicios = Servicio::all();

        $almacenes = DB::table('ALMACEN as a')
            ->join('SERVICIO as s', 's.servicio_id', '=', 'a.servicio_id')
            ->select('almacen_id', 'dsc_almacen', 'a.cod_almacen')
            ->get();

        $total_articulos = DB::table('MAPA_REPOSICIONES as mr')
            ->leftJoin('CONTENEDOR as c', 'c.cod_tag', '=', 'mr.cod_tag')
            ->leftJoin('UBICACION as u', 'u.ubicacion_id', '=', 'c.ubicacion_id')
            ->leftJoin('ALMACEN as a', 'a.almacen_id', '=', 'u.almacen_id')
            ->leftJoin('SERVICIO as s', 's.servicio_id', '=', 'a.servicio_id')
            ->leftJoin('PRODUCTO as p', 'p.cod_producto', '=', DB::raw("SUBSTRING(c.cod_contenedor,5, LEN(c.cod_contenedor)-5 )"))
            ->select(DB::raw('sum(convert(int, cant_reposicion)) as unidades'))
            ->get();


        $total_Registros = count($consumos);

        return view('reposiciones', compact('consumos', 'servicios', 'almacenes', 'total_Registros', 'total_articulos'))->render();
    }
}
