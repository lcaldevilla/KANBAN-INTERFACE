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
        Fecha
      </th>
      <th scope="col">
        Producto
      </th>
      <th scope="col">
        Descripción
      </th>
      <th scope="col" data-editable="true">
        Cantidad
      </th>
      <th scope="col">
        Tipo
      </th>
      <th scope="col">
        Estado
      </th>
      <th scope="col" data-checkbox="true" data-sortable="false">
        <input type="checkbox" class="select-all checkbox" name="select-all" id="select_all"/>
      </th>
    </tr>
  </thead>
  <tbody>
    @foreach ($consumos as $consumo)
    <tr>
      <td name="servicio">
        {{$consumo->cod_servicio}}
      </td>
      <td name="almacen">
        {{$consumo->cod_almacen}}
      </td>
      <td name="fecha">
           {{$consumo->fecha_creacion}}</td>
      <td name="codigo_producto">
        {{$consumo->cod_producto}}
      </td>
      <td name="descripcion">
        {{$consumo->dsc_producto}}
      </td>
      <td name="cant_reposicion" data-id="{{$consumo->stock_id}}"">
        {{$consumo->cant_reposicion}}
      </td>
      <td name="urgencia">
        {{$consumo->nivel_urgencia}}
      </td>
      <td name="estado">
        {{$consumo->estado_reposicion}}
      </td>
      <td name="stock_id" >
        <input type="checkbox" class="select-item checkbox" name="select[]" value="{{$consumo->stock_id}}" />
        <i class="bi bi-pencil-square" aria-hidden="true" value="{{$consumo->stock_id}}"></i>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
