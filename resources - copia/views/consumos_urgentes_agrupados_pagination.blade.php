<!-- <strong>Total registros22: </strong>&nbsp; {{ $total_Registros }} -->
<table class="table table-hover table-striped table-bordered" id='tabla' style="width:100%">
  <thead>
    <tr>
      <th colspan="3"><strong>Total registros: </strong>&nbsp; {{ $total_Registros }}</th>
      <th colspan="3"><strong>Total artículos: </strong>&nbsp; {{ $total_articulos[0]->unidades }}</th>
    </tr>

    <tr>
      <th scope="col">
        Servicio
      </th>
      <th scope="col">
        Almacén
      </th>
      <th scope="col">
        Producto
      </th>
      <th scope="col">
        Descripción
      </th>
      <th scope="col">
        Unidades
      </th>

    </tr>
  </thead>
  <tbody>
    @foreach ($consumos as $consumo)
    <tr>
      <td> {{$consumo->cod_servicio}}</td>
      <td>
        {{$consumo->cod_almacen}}
      </td>
      <td>
        {{$consumo->cod_producto}}
      </td>
      <td>
        {{$consumo->dsc_producto}}
      </td>
      <td>
        {{$consumo->total}}
      </td>
    </tr>
    @endforeach
  </tbody>
</table>