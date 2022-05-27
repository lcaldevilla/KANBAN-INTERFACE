<table class="table table-hover table-striped table-bordered" id='tabla' style="width:100%">
  <thead>
  <tr>
       <th colspan="8"><strong>Total registros: </strong>&nbsp; {{ $total_Registros }}</th>
     </tr>
    <tr>
      <th scope="col">
        Almacén
      </th>
      <th scope="col">
        Cod. Producto
      </th>
      <th scope="col">
        Desc. Producto
      </th>
      <th scope="col">
        Último
      </th> 
      <th scope="col">
        Días
      </th> 

    </tr>
  </thead>
  <tbody>
    @foreach ($consumos as $consumo)
    <tr>
      <td>
        {{$consumo->cod_almacen}}
      </td>
      <td> {{$consumo->cod_producto}}</td>
      <td>
        {{$consumo->dsc_producto}}
      </td>
      <td>
        {{$consumo->ultimo}}
      </td>
      <td>
        {{$consumo->dias}}
      </td>
    </tr>
    @endforeach
  </tbody>
</table>