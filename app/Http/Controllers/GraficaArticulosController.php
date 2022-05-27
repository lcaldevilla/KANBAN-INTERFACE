<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use App\Models\Almacen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Khill\Lavacharts\Lavacharts;

class GraficaArticulosController extends Controller
{

    // recibe el filtro de servicio por ajax
    public function almacenesPost(Request $request)
    {
        $input = $request->status;

        $almacenes = DB::table('ALMACEN as a')
            ->join('SERVICIO as s', 's.servicio_id', '=', 'a.servicio_id')
            ->select('almacen_id', 'dsc_almacen', 's.cod_servicio', 'a.cod_almacen')
            ->where('s.cod_servicio', '=', $input)
            ->get();

        return response()->json(['almacenes' => $almacenes]);
    }

    // Se llama al inicio, devuelve todo el histórico de consumos
    public function index()
    {
        $matches = array();
        $aux_array_nivel_urgencia = array();
        $aux_array_ano = array();

        $servicios = Servicio::all();

        $anos = DB::table('PEDIDO_REPOSICION as pr')
        ->select(DB::raw('YEAR(CONVERT(DATE, pr.fecha_creacion)) as ano'))
        ->groupBy(DB::raw('YEAR(CONVERT(DATE, pr.fecha_creacion))'))
        ->orderBy('ano', 'DESC')
        ->get();


        $almacenes = DB::table('ALMACEN as a')
            ->join('SERVICIO as s', 's.servicio_id', '=', 'a.servicio_id')
            ->select('almacen_id', 'dsc_almacen', 'a.cod_almacen')
            ->get();

        $articulos = DB::table('PRODUCTO')
            ->select('producto_id', 'cod_producto', 'dsc_producto')
            ->orderBy('cod_producto', 'ASC')
            ->get();

        $lava = new Lavacharts();

        // generamos el grafico de lineas
        $unidades = $lava->DataTable();

        // genero variables dinamicas por cada uno de los almacenes
        foreach ($almacenes as $almacen) {
            $aux_matches = $matches;
            $matches_almacen = array();
            ${"cod_alm_" . $almacen->cod_almacen} = $almacen->cod_almacen;
            array_push($matches_almacen, 'prd.cod_almacen', '=', ${"cod_alm_" . $almacen->cod_almacen});
            array_push($aux_matches, $matches_almacen);
            ${"consumos_" . $almacen->cod_almacen} = DB::table('PEDIDO_REPOSICION as pr')
                ->join('PEDIDO_REPOSICION_DET as prd', 'pr.pedido_repo_id', '=', 'prd.pedido_repo_id')
                ->select(DB::raw('CONVERT(DATE, pr.fecha_creacion) as fecha'), DB::raw('COUNT(*) as total'))
                ->where($aux_matches)
                ->groupBy(DB::raw('CONVERT(DATE, pr.fecha_creacion)'))
                ->orderBy('fecha', 'DESC')
                ->get();
        }

        // genero la estructura datatable correcta para el grafico con el modelo
        // fecha, linea1, linea2, linea_n
        $numero_almacenes = 0; // que xisten en el sistema
        $longitudes_array = array();
        $fechas = array(); // almacena las fechas para la grafica
        $unidades->addDateColumn('Date');
        foreach ($almacenes as $almacen) {
            $numero_almacenes++;
            $unidades->addNumberColumn($almacen->cod_almacen);
            global ${"chart_array_" . $almacen->cod_almacen};
            ${"chart_array_" . $almacen->cod_almacen} = array();
            $total = 0;
            foreach (${"consumos_" . $almacen->cod_almacen} as $consumo) {
                $n_data = [];
                array_push($n_data, $consumo->fecha, $consumo->total);
                array_push(${"chart_array_" . $almacen->cod_almacen}, $n_data);
                array_push($fechas, $consumo->fecha);
                $total++;
                //print($almacen->cod_almacen."+ ".$consumo->fecha." + ".$consumo->total);
            }
            // almacenamos el um de elementos de los array de cada almacen
            $longitudes_array += ["chart_array_" . $almacen->cod_almacen => count(${"chart_array_" . $almacen->cod_almacen})];
        }
        $fechas = array_unique($fechas); // eliminos dupli9cados y ordeno
        sort($fechas);
        // print(var_dump($fechas));
        // tenemos $numero de almacenes y  su longitud
        // recorremos los almacenes y creamos el datatable para el grafico de la forma datos = fecha, alm1, alm2, alm3, alm4
        rsort($longitudes_array, SORT_NUMERIC); //ordeno mayor a menor las longitudes
        //recorremos los array y creamos el datatable
        $datos = array(); // almacen los datos para la grafica
        // busco en cada array de almacen
        foreach ($fechas as $fecha) {
            // incicializamos los valores
            ${"valor_ALM1"} = 0;
            ${"valor_ALM2"} = 0;
            ${"valor_ALM3"} = 0;
            ${"valor_ALM4"} = 0;

            // buscamos en los arrays de los almacenes
            foreach ($almacenes as $almacen) {
                for ($indice = 0; $indice < count(${"chart_array_" . $almacen->cod_almacen}); $indice++) {
                    if ($fecha == ${"chart_array_" . $almacen->cod_almacen}[$indice][0]) {
                        ${"valor_" . $almacen->cod_almacen} = ${"chart_array_" . $almacen->cod_almacen}[$indice][1];
                    }
                }
            }

            // relleno el array de datos de los graficos
            $unidades->addRow([$fecha, ${"valor_ALM1"}, ${"valor_ALM2"}, ${"valor_ALM3"}, ${"valor_ALM4"}]);
        }

        $lava->LineChart('Unidades', $unidades, ['title' => 'Curva consumos', 'curveType' => 'none', 'pointsVisible' => true,]);
        return view('graficaArticulos', compact('servicios', 'almacenes', 'lava', 'articulos', 'anos'))->render();
    }



    // actualiza la grafica
    public function update(Request $request)
    {
        $almacen = $request->input('almacen');
        $servicio = $request->input('servicio');
        $ano = $request->input('ano');
        

        $lava = new Lavacharts();

        // generamos el grafico de lineas
        $unidades = $lava->DataTable();

        $matches = array();
        $aux_array_nivel_urgencia = array();
        $aux_array_servicio = array();
        $aux_array_almacen = array();
        $aux_array_ano = array();

        if ($servicio != '0' || $almacen != '0') {

            if ($servicio != '0') { // todos los servicios
                array_push($aux_array_servicio, 'prd.cod_servicio', '=', $servicio);
                array_push($matches, $aux_array_servicio);
            }

            if ($almacen != '0') { // todos los almacenes
                array_push($aux_array_almacen, 'prd.cod_almacen', '=', $almacen);
                array_push($matches, $aux_array_almacen);
            }

            if ($ano != '0') { // todos los años
                array_push($aux_array_ano, DB::raw('YEAR(CONVERT(DATE, pr.fecha_creacion))'), '=', $ano);
                array_push($matches, $aux_array_ano);
            }

            array_push($aux_array_nivel_urgencia, 'prd.codigo_urgencia', '=', '1');
            array_push($matches, $aux_array_nivel_urgencia);

            //$lava = new Lavacharts();

            //
            $consumos = DB::table('PEDIDO_REPOSICION as pr')
                ->join('PEDIDO_REPOSICION_DET as prd', 'pr.pedido_repo_id', '=', 'prd.pedido_repo_id')
                ->select(DB::raw('CONVERT(DATE, pr.fecha_creacion) as fecha'), DB::raw('COUNT(*) as total'))
                ->where($matches)
                ->groupBy(DB::raw('CONVERT(DATE, pr.fecha_creacion)'))
                ->orderBy('fecha', 'DESC')
                ->get();

            //$unidades = $lava->DataTable();

            $unidades->addDateColumn('Date')
                ->addNumberColumn('Consumos');

            foreach ($consumos as $consumo) {
                $unidades->addRow([$consumo->fecha, $consumo->total]);
            }
        } else { //seleecionamos todos los almacenes y servicios
            if ($ano != '0') { // todos los años
                array_push($aux_array_ano, DB::raw('YEAR(CONVERT(DATE, pr.fecha_creacion))'), '=', $ano);
                array_push($matches, $aux_array_ano);
            }

            array_push($aux_array_nivel_urgencia, 'prd.codigo_urgencia', '=', '1');
            array_push($matches, $aux_array_nivel_urgencia);

            $almacenes = DB::table('ALMACEN as a')
                ->join('SERVICIO as s', 's.servicio_id', '=', 'a.servicio_id')
                ->select('almacen_id', 'dsc_almacen', 'a.cod_almacen')
                ->get();

            // genero variables dinamicas por cada uno de los almacenes
            foreach ($almacenes as $almacen) {
                $aux_matches = $matches;
                $matches_almacen = array();
                ${"cod_alm_" . $almacen->cod_almacen} = $almacen->cod_almacen;
                array_push($matches_almacen, 'prd.cod_almacen', '=', ${"cod_alm_" . $almacen->cod_almacen});
                array_push($aux_matches, $matches_almacen);
                ${"consumos_" . $almacen->cod_almacen} = DB::table('PEDIDO_REPOSICION as pr')
                    ->join('PEDIDO_REPOSICION_DET as prd', 'pr.pedido_repo_id', '=', 'prd.pedido_repo_id')
                    ->select(DB::raw('CONVERT(DATE, pr.fecha_creacion) as fecha'), DB::raw('COUNT(*) as total'))
                    ->where($aux_matches)
                    ->groupBy(DB::raw('CONVERT(DATE, pr.fecha_creacion)'))
                    ->orderBy('fecha', 'ASC')
                    ->get();
            }

            // genero la estructura datatable correcta para el grafico con el modelo
            // fecha, linea1, linea2, linea_n
            $numero_almacenes = 0; // que xisten en el sistema
            $longitudes_array = array();
            $fechas = array(); // almacena las fechas para la grafica
            $unidades->addDateColumn('Date');
            foreach ($almacenes as $almacen) {
                $numero_almacenes++;
                $unidades->addNumberColumn($almacen->cod_almacen);
                global ${"chart_array_" . $almacen->cod_almacen};
                ${"chart_array_" . $almacen->cod_almacen} = array();
                $total = 0;
                foreach (${"consumos_" . $almacen->cod_almacen} as $consumo) {
                    $n_data = [];
                    array_push($n_data, $consumo->fecha, $consumo->total);
                    array_push(${"chart_array_" . $almacen->cod_almacen}, $n_data);
                    array_push($fechas, $consumo->fecha);
                    $total++;
                    //print($almacen->cod_almacen."+ ".$consumo->fecha." +");
                }
                // almacenamos el um de elementos de los array de cada almacen
                $longitudes_array += ["chart_array_" . $almacen->cod_almacen => count(${"chart_array_" . $almacen->cod_almacen})];
            }
            $fechas = array_unique($fechas); // eliminos dupli9cados y ordeno
            sort($fechas);
            // print(var_dump($fechas));
            // tenemos $numero de almacenes y  su longitud
            // recorremos los almacenes y creamos el datatable para el grafico de la forma datos = fecha, alm1, alm2, alm3, alm4
            rsort($longitudes_array, SORT_NUMERIC); //ordeno mayor a menor las longitudes
            //recorremos los array y creamos el datatable
            $datos = array(); // almacen los datos para la grafica
            // busco en cada array de almacen
            foreach ($fechas as $fecha) {
                // incicializamos los valores
                ${"valor_ALM1"} = 0;
                ${"valor_ALM2"} = 0;
                ${"valor_ALM3"} = 0;
                ${"valor_ALM4"} = 0;

                // buscamos en los arrays de los almacenes
                foreach ($almacenes as $almacen) {
                    for ($indice = 0; $indice < count(${"chart_array_" . $almacen->cod_almacen}); $indice++) {
                        if ($fecha == ${"chart_array_" . $almacen->cod_almacen}[$indice][0]) {
                            ${"valor_" . $almacen->cod_almacen} = ${"chart_array_" . $almacen->cod_almacen}[$indice][1];
                        }
                    }
                }

                // relleno el array de datos de los graficos
                $unidades->addRow([$fecha, ${"valor_ALM1"}, ${"valor_ALM2"}, ${"valor_ALM3"}, ${"valor_ALM4"}]);
            }
        }
        $lava->LineChart('Unidades', $unidades, ['title' => 'Curva consumos', 'curveType' => 'none', 'pointsVisible' => true,]);
        return view('graficaNormales_chart', compact('lava'))->render();
    }

    // **********************************************************************************************************
    // actualiza la grafica con el filtro de las comparaciones de curvas
    public function compara(Request $request)
    {
        $articuloRequest = $request->input('articulo');
        $almacenes = array();
        $numeroColumnasGrafico = 0;
        $almacen1 = $request->input('almacen1');
        $almacen2 = $request->input('almacen2');
        $almacen3 = $request->input('almacen3');
        $almacen4 = $request->input('almacen4');

        // el valor 0 hace que no se incluya en la comparación
        if ($almacen1 != '0') {
            array_push($almacenes, $almacen1);
            $numeroColumnasGrafico++;
        }
        if ($almacen2 != '0') {
            array_push($almacenes, $almacen2);
            $numeroColumnasGrafico++;
        }
        if ($almacen3 != '0') {
            array_push($almacenes, $almacen3);
            $numeroColumnasGrafico++;
        }
        if ($almacen4 != '0') {
            array_push($almacenes, $almacen4);
            $numeroColumnasGrafico++;
        }

        $lava = new Lavacharts();
        
        // generamos el grafico de lineas
        $unidades = $lava->DataTable();

        $matches = array();
        $aux_array_articulo = array();
        array_push($aux_array_articulo, 'prd.cod_producto', '=', $articuloRequest);
        array_push($matches, $aux_array_articulo);
       error_log($articuloRequest);

        // genero variables dinamicas por cada uno de los almacenes
        foreach ($almacenes as $almacen) {
            $aux_matches = $matches;
            $matches_almacen = array();
            ${"cod_alm_" . $almacen} = $almacen;
            array_push($matches_almacen, 'prd.cod_almacen', '=', ${"cod_alm_" . $almacen});
            array_push($aux_matches, $matches_almacen);
            ${"consumos_" . $almacen} = DB::table('PEDIDO_REPOSICION as pr')
                ->join('PEDIDO_REPOSICION_DET as prd', 'pr.pedido_repo_id', '=', 'prd.pedido_repo_id')
                ->select(DB::raw('CONVERT(DATE, pr.fecha_creacion) as fecha'), DB::raw('COUNT(*) as total'))
                ->where($aux_matches)
                ->groupBy(DB::raw('CONVERT(DATE, pr.fecha_creacion)'))
                ->orderBy('fecha', 'ASC')
                ->get();
        }

        // genero la estructura datatable correcta para el grafico con el modelo
        // fecha, linea1, linea2, linea_n
        $numero_almacenes = 0; // que existen en el sistema
        $longitudes_array = array();
        $fechas = array(); // almacena las fechas para la grafica
        $unidades->addDateColumn('Date');
        //error_log($articulo);
        foreach ($almacenes as $almacen) {
            $numero_almacenes++;
            $unidades->addNumberColumn($almacen);
            global ${"chart_array_" . $almacen};
            ${"chart_array_" . $almacen} = array();
            $total = 0;
            foreach (${"consumos_" . $almacen} as $consumo) {
                $n_data = [];
                array_push($n_data, $consumo->fecha, $consumo->total);
                array_push(${"chart_array_" . $almacen}, $n_data);
                array_push($fechas, $consumo->fecha);
                $total++;
                error_log($almacen."+ ".$consumo->fecha." +");
            }
            // almacenamos el um de elementos de los array de cada almacen
            $longitudes_array += ["chart_array_" . $almacen => count(${"chart_array_" . $almacen})];
        }
        $fechas = array_unique($fechas); // eliminos dupli9cados y ordeno
        sort($fechas);
        // tenemos $numero de almacenes y  su longitud
        // recorremos los almacenes y creamos el datatable para el grafico de la forma datos = fecha, alm1, alm2, alm3, alm4
        rsort($longitudes_array, SORT_NUMERIC); //ordeno mayor a menor las longitudes
        //recorremos los array y creamos el datatable
        $datos = array(); // almacen los datos para la grafica
        // busco en cada array de almacen
        foreach ($fechas as $fecha) {
            // incicializamos los valores
            $arrayAlmacenes = array();
            $arrayListaDeAlmacenes = array();
            array_push($arrayAlmacenes, $fecha);

            foreach ($almacenes as $almacen) {
                ${"valor_" . $almacen} = 0;
            }

            // buscamos en los arrays de los almacenes
            foreach ($almacenes as $almacen) {
                for ($indice = 0; $indice < count(${"chart_array_" . $almacen}); $indice++) {
                    if ($fecha == ${"chart_array_" . $almacen}[$indice][0]) {
                        ${"valor_" . $almacen} = ${"chart_array_" . $almacen}[$indice][1];
                    }
                }
                array_push($arrayAlmacenes, ${"valor_" . $almacen});
            }
            
            
            //$arrayAlmacenes = array($fecha, ${"valor_ALM1"}, ${"valor_ALM2"}, ${"valor_ALM3"}, ${"valor_ALM4"});
            //array_push($arrayAlmacenes, $arrayListaDeAlmacenes);
            
            $unidades->addRow($arrayAlmacenes);
        }

        $lava->LineChart('Unidades', $unidades, ['title' => 'Curva consumos', 'curveType' => 'none', 'pointsVisible' => true,]);
        return view('graficaArticulos_chart', compact('lava'))->render();
    }
}
