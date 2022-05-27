@extends('plantilla')

@section('title', 'consumos almacen ')

@section('content')

<!-- Filtro de opciones de visualización -->

<div class="card card-default">

    <div id="collapse1" class="collapse show">
        <div class="card-body">
            <div class="row">

            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                <label class="control-label">Servicio</label>

                </div>
            </div>

            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                <label class="control-label">Almacén</label>

                </div>
            </div>


            <div class="col-md-2 col-lg-2">
                <div class="form-group">
                    <label class="control-label">Desde</label>
                    <input id="datepickerDesde" class="form-control" width="276" value="02/04/2018" />
                </div>
            </div>


            <div class="col-md-2 col-lg-2">
                <div class="form-group">
                    <label class="control-label">Hasta</label>
                    <input id="datepickerHasta" class="form-control" width="276" value="02/04/2021" />
                </div>
            </div>

            <div class="col-md-2 col-lg-2">
                <div class="form-group">
                    <button type="button" id="btnActualizar" class="btn btn-primary align-center">Actualizar</button>
                </div>

                <div class="form-group">
                    <button type="button" id="btnExportarExcel" class="btn btn-primary align-center">Exportar</button>

                </div>
            </div>


            <h1>Products</h1>

            <table id="products-table" class="table table-bordered table-hover yajra-datatable" class="display" style="width:100%">
                <thead>
                    <th>Id</th>
                    <th>Usuario</th>
                    <th>Servicio</th>
                    <th>Almacen</th>

                </thead>
                <tbody>

                </tbody>
            </table>

            {{ csrf_field() }}
            </div>
        </div>
    </div>

<!-- ********************************************************************************************************************** -->



@endsection

@section('script')
<script type = "text/javascript" >
    $('#datepickerDesde').datepicker({
        uiLibrary: 'bootstrap4',
        locale: 'es-es',
    });

    $('#datepickerHasta').datepicker({
        uiLibrary: 'bootstrap4',
        locale: 'es-es',
    });




    $(document).ready(function() {
        var table = $('#products-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('consumos.tablas') }}",
        columns: [
            {data: 'servicio_id', name: 'servicio_id'},
            {data: 'cod_GFH', name: 'cod_GFH'},
            {data: 'cod_servicio', name: 'cod_servicio'},
            {data: 'dsc_servicio', name: 'dsc_servicio'},
            {
                data: 'action',
                name: 'action',
                orderable: true,
                searchable: true
            },
        ]
    });
    });

  </script>
@endsection
