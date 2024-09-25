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
        Fecha Solicitud
      </th>
      <th scope="col">
        Fecha Reposición
      </th>
      <th scope="col">
        Reponedor
      </th>
      <th scope="col">
        Cod. Producto
      </th>
      <th scope="col">
        Descripción
      </th>
      <th scope="col">
        Cantidad
      </th>

    </tr>
  </thead>
  <tbody>
    @foreach ($consumos as $consumo)
    <tr>
      <td>
        {{$consumo->cod_servicio}}
      </td>
      <td>
        {{$consumo->cod_almacen}}
      </td>
      <td> {{$consumo->fecha_creacion}}</td>
      <td>
        {{$consumo->fecha}}
      </td>
      <td>
        {{$consumo->dsc_usuario}}
      </td>
      <td>
        {{$consumo->cod_producto}}
      </td>
      <td>
        {{$consumo->dsc_producto}}
      </td>
      <td>
        {{$consumo->cant_reponer}}
      </td>

    </tr>
    @endforeach
  </tbody>
</table>