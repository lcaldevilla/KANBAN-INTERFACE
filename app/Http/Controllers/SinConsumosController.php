<?php

namespace App\Http\Controllers;

use App\Models\Pedido_reposicion;
use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ConsumosExport;
use Carbon\Carbon;
use Yajra\Datatables\Datatables;

class SinConsumosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function ajaxRequest()
    {
        return view('ajaxRequest');
    }

     // Se llama al inicio, devuelve todo el histórico de consumos
     public function index()
     {
        $servicios = Servicio::all();

        $almacenes = DB::table('ALMACEN as a')
            ->join('SERVICIO as s', 's.servicio_id', '=', 'a.servicio_id')
            ->select('almacen_id', 'dsc_almacen', 'a.cod_almacen')
            ->get();
        $consumos = DB::select('exec DevuelveSinConsumos ?, ?, ?', array('180', '0', '0'));

         $total_Registros = count($consumos);
        // error_log("sin consumos update");
         return view('sinConsumos', compact('consumos', 'servicios', 'almacenes', 'total_Registros'))->render();
     }

     // Se llama al inicio, devuelve todo el histórico de consumos
     public function update(Request $request)
     {

        // variables recibidas
        $input = $request->status;
        $almacen = $request->input('almacen');
        $servicio = $request->input('servicio');
        $dias = $request->input('dias');
        error_log("sin consumos update: ".$dias."-".$servicio."-".$almacen);
        $consumos = DB::select('exec DevuelveSinConsumos ?, ?, ?', array($dias, $almacen, $servicio));

         $total_Registros = count($consumos);

         return view('sin_Consumos_pagination', compact('consumos', 'total_Registros'))->render();
     }
}
