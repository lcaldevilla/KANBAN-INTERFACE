<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\matches;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $matches = array();
        $matchesUrgentes = array();
        $array_articulos_normales = array();
        $array_articulos_pendientes = array();
        $array_articulos_urgentes = array();
        $array_dispositivos = array();
        $array_detalle_dispositivo = array();

        // condiciones para buscar articulos normales
        array_push($array_articulos_normales, 'c.nivel_urgencia', '=', '1');
        array_push($array_articulos_pendientes, 'mr.estado_reposicion', '=', 'PENDIENTE');
        array_push($matches, $array_articulos_normales);
        array_push($matches, $array_articulos_pendientes);

        // condiciones para buscar articulos urgentes
        array_push($array_articulos_urgentes, 'c.nivel_urgencia', '=', '2');
        array_push($matchesUrgentes, $array_articulos_urgentes);
        array_push($matchesUrgentes, $array_articulos_pendientes);

        /*$peticionesNormales = DB::table('MAPA_REPOSICIONES as mr')
            ->join('CONTENEDOR as c', 'c.cod_tag', '=', 'mr.cod_tag')
            ->select('*')
            ->where($matches)
            ->count();
        

        $peticionesUrgentes = DB::table('MAPA_REPOSICIONES as mr')
            ->join('CONTENEDOR as c', 'c.cod_tag', '=', 'mr.cod_tag')
            ->select('*')
            ->where($matchesUrgentes)
            ->count();
*/
        $dispositivos = DB::table('DISPOSITIVO')
            ->select('*')
            ->where('ind_habilitado', '=', 'S')
            ->get();

        // realiza ping a cada unos de los dispositivos

        foreach ($dispositivos as $dispositivo) {
            // condiciones para buscar articulos normales
            array_push($matches, $array_articulos_normales);
            array_push($matches, $array_articulos_pendientes);
            // condiciones para buscar articulos urgentes
            array_push($matchesUrgentes, $array_articulos_urgentes);
            array_push($matchesUrgentes, $array_articulos_pendientes);

            $array_almacen_id  = array();
            $ip = $dispositivo->ip_dispositivo;
            $descripcion = $dispositivo->dsc_dispositivo;
            array_push($array_almacen_id, 'u.almacen_id', '=', $dispositivo->almacen_id);
            array_push($matches, $array_almacen_id);
            array_push($matchesUrgentes, $array_almacen_id);

            $host = $ip;
            $port = 80;
            $waitTimeoutInSeconds = 3;
            // realiza un ping para coprobar si el dispositivo está online
            try {
                if ($fp = fsockopen($host, $port, $errCode, $errStr, $waitTimeoutInSeconds)) {
                    $pingOk = asset('images/semaforo_verde.png');
                    fclose($fp);
                } else {
                    $pingOk = asset('images/semaforo_rojo.png');
                    fclose($fp);
                }
            } catch (\Exception $ex) {
                $pingOk = asset('images/semaforo_rojo.png');
            }

            // recupera los articulos pendientes de servir
            $peticionesNormales = DB::table('MAPA_REPOSICIONES as mr')
            ->join('CONTENEDOR as c', 'c.cod_tag', '=', 'mr.cod_tag')
            ->join ('UBICACION  as u', 'u.ubicacion_id', '=', 'c.ubicacion_id')
            ->select('*')
            ->where($matches)
            ->count();

            // recupera los articulos pendientes de servir
            $peticionesUrgentes = DB::table('MAPA_REPOSICIONES as mr')
            ->join('CONTENEDOR as c', 'c.cod_tag', '=', 'mr.cod_tag')
            ->join ('UBICACION  as u', 'u.ubicacion_id', '=', 'c.ubicacion_id')
            ->select('*')
            ->where($matchesUrgentes)
            ->count();

            $array_detalle_dispositivo['ip_dispositivo'] = $ip;
            $array_detalle_dispositivo['dsc_dispositivo'] = $descripcion;
            $array_detalle_dispositivo['imagen'] = $pingOk;
            $array_detalle_dispositivo['peticiones_normales'] = $peticionesNormales;
            $array_detalle_dispositivo['peticiones_urgentes'] = $peticionesUrgentes;
            array_push($array_dispositivos, $array_detalle_dispositivo);
            $matches = array();
            $matchesUrgentes = array();
        }

        return view('home', compact( 'array_dispositivos'));
    }

    // Actualiza el número de pedidos
    public function indexUpdate()
    {
        $matches = array();
        $matchesUrgentes = array();
        $array_articulos_normales = array();
        $array_articulos_pendientes = array();
        $array_articulos_urgentes = array();
        $array_dispositivos = array();
        $array_detalle_dispositivo = array();

        // condiciones para buscar articulos normales
        array_push($array_articulos_normales, 'c.nivel_urgencia', '=', '1');
        array_push($array_articulos_pendientes, 'mr.estado_reposicion', '=', 'PENDIENTE');
        array_push($matches, $array_articulos_normales);
        array_push($matches, $array_articulos_pendientes);

        // condiciones para buscar articulos urgentes
        array_push($array_articulos_urgentes, 'c.nivel_urgencia', '=', '2');
        array_push($matchesUrgentes, $array_articulos_urgentes);
        array_push($matchesUrgentes, $array_articulos_pendientes);

        $dispositivos = DB::table('DISPOSITIVO')
            ->select('*')
            ->where('ind_habilitado', '=', 'S')
            ->get();

        // realiza ping a cada unos de los dispositivos

        foreach ($dispositivos as $dispositivo) {
            // condiciones para buscar articulos normales
            array_push($matches, $array_articulos_normales);
            array_push($matches, $array_articulos_pendientes);
            // condiciones para buscar articulos urgentes
            array_push($matchesUrgentes, $array_articulos_urgentes);
            array_push($matchesUrgentes, $array_articulos_pendientes);

            $array_almacen_id  = array();
            $ip = $dispositivo->ip_dispositivo;
            $descripcion = $dispositivo->dsc_dispositivo;
            array_push($array_almacen_id, 'u.almacen_id', '=', $dispositivo->almacen_id);
            array_push($matches, $array_almacen_id);
            array_push($matchesUrgentes, $array_almacen_id);

            $host = $ip;
            $port = 80;
            $waitTimeoutInSeconds = 1;
            // realiza un ping para coprobar si el dispositivo está online
            try {
                if ($fp = fsockopen($host, $port, $errCode, $errStr, $waitTimeoutInSeconds)) {
                    $pingOk = asset('images/semaforo_verde.png');
                    fclose($fp);
                } else {
                    $pingOk = asset('images/semaforo_rojo.png');
                    fclose($fp);
                }
            } catch (\Exception $ex) {
                $pingOk = asset('images/semaforo_rojo.png');
            }

            // recupera los articulos pendientes de servir
            $peticionesNormales = DB::table('MAPA_REPOSICIONES as mr')
            ->join('CONTENEDOR as c', 'c.cod_tag', '=', 'mr.cod_tag')
            ->join ('UBICACION  as u', 'u.ubicacion_id', '=', 'c.ubicacion_id')
            ->select('*')
            ->where($matches)
            ->count();

            // recupera los articulos pendientes de servir
            $peticionesUrgentes = DB::table('MAPA_REPOSICIONES as mr')
            ->join('CONTENEDOR as c', 'c.cod_tag', '=', 'mr.cod_tag')
            ->join ('UBICACION  as u', 'u.ubicacion_id', '=', 'c.ubicacion_id')
            ->select('*')
            ->where($matchesUrgentes)
            ->count();

            $array_detalle_dispositivo['ip_dispositivo'] = $ip;
            $array_detalle_dispositivo['dsc_dispositivo'] = $descripcion;
            $array_detalle_dispositivo['imagen'] = $pingOk;
            $array_detalle_dispositivo['peticiones_normales'] = $peticionesNormales;
            $array_detalle_dispositivo['peticiones_urgentes'] = $peticionesUrgentes;
            array_push($array_dispositivos, $array_detalle_dispositivo);
            $matches = array();
            $matchesUrgentes = array();
        }

        return view('tabla_home', compact('array_dispositivos'))->render();
      
    }
}
