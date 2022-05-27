<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;



class ConsumosExport implements FromCollection
{

    protected $consumos;

    // el constructoir recibe los paramtros que utilizará para obtener los datos de la esportación
    public function __construct($consumos = null)
    {
        $this->consumos = $consumos;
        error_log("export excel->almacen: ");
    }

    public function collection()
    {
        return $this->consumos;

    }
}
?>
