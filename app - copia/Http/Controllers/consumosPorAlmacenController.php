<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido_reposicion;
use App\Models\Servicio;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ConsumosPorAlmacenController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function ajaxRequest()
    {
        return view('ajaxRequest');
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
        $desde = strtotime('-1 day', strtotime($request->input('desde')));
        $hasta =  strtotime('+1 day', strtotime($request->input('hasta')));

        // adapto la fecha
        $auxDesde = Carbon::parse($desde)->format('Ymd');
        $auxHasta = Carbon::parse($hasta)->format('Ymd');

        $aux_array_servicio = array();
        $aux_array_almacen = array();
        $aux_array_desde = array();
        $aux_array_hasta = array();
        $aux_array_nivel_urgencia = array();
        $matches = array();

        if ($servicio != '0') { // todos los servicios
            array_push($aux_array_servicio, 'prd.cod_servicio', '=', $servicio);
            array_push($matches, $aux_array_servicio);
        }

        if ($almacen != '0') { // todos los almacenes
            array_push($aux_array_almacen, 'prd.cod_almacen', '=', $almacen);
            array_push($matches, $aux_array_almacen);
        }

        // añado las fechas en la cláusula WHERE
        array_push($aux_array_desde, 'pr.fecha_creacion', '>=', $auxDesde);
        array_push($matches, $aux_array_desde);

        array_push($aux_array_hasta, 'pr.fecha_creacion', '<=', $auxHasta);
        array_push($matches, $aux_array_hasta);

        // consulta con servicio y almacen
        //array_push($matches, $aux_array_nivel_urgencia);
        //error_log("update view1: ");
        $consumos = DB::table('PEDIDO_REPOSICION as pr')
            ->join('PEDIDO_REPOSICION_DET as prd', 'pr.pedido_repo_id', '=', 'prd.pedido_repo_id')
            ->join('producto as p', 'p.cod_producto', '=', 'prd.cod_producto')
            ->select(DB::raw('sum(convert(int, prd.cant_reponer)) as total'), 'prd.cod_servicio', 'prd.cod_almacen')
            ->where($matches)
            ->groupBy('prd.cod_servicio', 'prd.cod_almacen')
            ->get();

           
        $total_articulos = DB::table('PEDIDO_REPOSICION as pr')
            ->select(DB::raw('sum(convert(int, cant_reponer)) as unidades'))
            ->join('PEDIDO_REPOSICION_DET as prd', 'prd.pedido_repo_id', '=', 'pr.pedido_repo_id')
            ->where($matches)
            ->get();
            
        $total_Registros = count($consumos);


        error_log("update view: " . count($total_articulos));
        return view('consumos_por_almacen_pagination', compact('consumos', 'total_Registros', 'total_articulos'))->render();
        //return view('consumos', compact('consumos', 'servicios', 'almacenes', 'total_Registros'));
    }

    // Se llama al inicio, devuelve todo el histórico de consumos
    public function index()
    {
        $desde = date('Ymd', strtotime('-1 month', strtotime(date('Ymd'))));
        $hasta = date('Ymd', strtotime('+1 day', strtotime(date('Ymd'))));
        $aux_array_desde = array();
        $aux_array_hasta = array();
        $matches = array();

        // adapto la fecha
        $auxDesde = Carbon::parse($desde)->format('Ymd');
        $auxHasta = Carbon::parse($hasta)->format('Ymd');

        // añado las fechas en la cláusula WHERE
        array_push($aux_array_desde, 'pr.fecha_creacion', '>=', $auxDesde);
        array_push($matches, $aux_array_desde);

        array_push($aux_array_hasta, 'pr.fecha_creacion', '<=', $auxHasta);
        array_push($matches, $aux_array_hasta);

        $consumos = DB::table('PEDIDO_REPOSICION as pr')
            ->join('PEDIDO_REPOSICION_DET as prd', 'pr.pedido_repo_id', '=', 'prd.pedido_repo_id')
            ->join('producto as p', 'p.cod_producto', '=', 'prd.cod_producto')
            ->select(DB::raw('sum(convert(int, prd.cant_reponer)) as total'), 'prd.cod_servicio', 'prd.cod_almacen')
            ->where($matches)
            ->groupBy('prd.cod_servicio', 'prd.cod_almacen')
            ->get();

        $servicios = Servicio::all();

        $almacenes = DB::table('ALMACEN as a')
            ->join('SERVICIO as s', 's.servicio_id', '=', 'a.servicio_id')
            ->select('almacen_id', 'dsc_almacen', 'a.cod_almacen')
            ->get();

        $total_articulos = DB::table('PEDIDO_REPOSICION as pr')
            ->select(DB::raw('sum(convert(int, cant_reponer)) as unidades'))
            ->join('PEDIDO_REPOSICION_DET as prd', 'prd.pedido_repo_id', '=', 'pr.pedido_repo_id')
            ->where($matches)
            ->get();

        $total_Registros = count($consumos);

        return view('ConsumosPorAlmacen', compact('consumos', 'servicios', 'almacenes', 'total_Registros', 'total_articulos'))->render();
    }
}
