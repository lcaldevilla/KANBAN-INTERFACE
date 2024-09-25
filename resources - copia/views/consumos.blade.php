@extends('plantilla')

@section('title', 'consumos almacen ')

@section('content')

<!-- Filtro de opciones de visualización -->
<h2 class="standard-margin">Consumos Artículos Recurrentes </h2>
<div class="card card-default">
    <div id="loading" class="text-center text-danger">
    <div id="loading_hijo"><h2>Cargando datos...</h2></div>
    </div>

    <div id="collapse1" class="collapse show">
        <div class="card-body">
            <div class="row">

                <div class="col-md-3 col-lg-3">
                    <div class="form-group">
                        <label class="control-label "><strong>Servicio</strong></label>
                        <select name="servicio" id="servicio" class="form-control input-lg btn-submit" data-dependent="almacen">
                            <option value="0">Todos los servicios</option>
                            @foreach ($servicios as $servicio)
                            <option value="{{ $servicio->cod_servicio}}">{{ $servicio->dsc_servicio }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-3 col-lg-3">
                    <div class="form-group">
                        <label class="control-label"><strong>Almacén</strong></label>
                        <select name="almacen" id="almacen" class="form-control input-lg dynamic">
                            <option value="0">Todos los almacenes</option>

                        </select>
                    </div>
                </div>


                <div class="col-md-2 col-lg-2">
                    <div class="form-group">
                        <label class="control-label"><strong>Desde</strong></label>
                        <input id="datepickerDesde" class="form-control" width="276" value="02/04/2018" />
                    </div>
                </div>


                <div class="col-md-2 col-lg-2">
                    <div class="form-group">
                        <label class="control-label"><strong>Hasta</strong></label>
                        <input id="datepickerHasta" class="form-control" width="276" value="02/04/2021" />
                    </div>
                </div>

                <div class="col-md-2 col-lg-2">
                    <div class="form-group">
                        <label class="control-label"><strong>&nbsp;</strong></label>
                        <button type="button" id="btnActualizar" class="form-control btn btn-primary align-center">Actualizar</button>
                    </div>
                </div>

            </div>

            {{ csrf_field() }}
        </div>


    </div>

</div>

<br />

<!-- ********************************************************************************************************************** -->
<div class="table-responsive standard-margin" id="table_data" >
    @include('consumos_pagination')
</div>


@endsection

@section('script')
<script type="text/javascript">
    // Gestiona la tabla de datos por defecto
    $(document).ready(function() {
        $.noConflict();
        $('#tabla').DataTable({
            dom: 'lBfrtip',
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No existen registros",
                "info": "Mostrando página _PAGE_ de _PAGES_",
                "infoEmpty": "Sin registros",
                "search": "Buscar",
                "infoFiltered": "(filtered from _MAX_ total records)",
                "paginate": {
                    "first": "Primera",
                    "last": "Última",
                    "next": "Siguiente",
                    "previous": "Anterior"
                },
            },
            buttons: [
                'copyHtml5',
                'excelHtml5',
                'pdfHtml5'
            ],
            "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "Todos"]
            ]
        });

        var d = new Date();

        // Set it to one month ago
        d.setMonth(d.getMonth() - 1);
        // Gestión de los filtros de fechas
        $('#datepickerDesde').datepicker({
            uiLibrary: 'bootstrap4',
            locale: 'es',
            format: 'dd-mm-yyyy',
            autoclose: true
        }).datepicker("setDate", d);

        $('#datepickerHasta').datepicker({
            uiLibrary: 'bootstrap4',
            locale: 'es',
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayBtn: true
        }).datepicker("setDate", new Date());

        // Actuliza el dropdown de los almacenes
        $("#servicio").change(function(e) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
            e.preventDefault();
            var status = this.value;

            $.ajax({
                type: 'POST',
                url: "{{ route('consumos.post') }}",
                data: {
                    status: status
                },
                success: function(data) {
                    $("#loading").show();
                    if (data) {
                        $("#almacen").empty();
                        $("#almacen").append('<option value="0">Todos los almacenes</option>');

                        $.each(data, function(key, value) {
                            $.each(value, function(i, j) {
                                $("#almacen").append('<option value="' + value[i].cod_almacen + '">' + value[i].dsc_almacen + '</option>');
                            });
                        })
                    }
                    $("#loading").hide();
                }
            });
        });




        // actualiza la tabla según los filtros

        $("#btnActualizar").click(function(e) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            e.preventDefault();
            var status = this.value;
            // $("#tabla").empty();
            $('#tabla').DataTable().destroy();
            var almacen = $("#almacen").val();
            var servicio = $("#servicio").val();
            var desde = $("#datepickerDesde").val();
            var hasta = $("#datepickerHasta").val();

            $.ajax({
                url: "{{ route('consumos.update') }}",
                method: "POST",
                data: {
                    almacen: almacen,
                    servicio: servicio,
                    desde: desde,
                    hasta: hasta
                },
                success: function(consumos) {
                    //alert("update");
                    $('#tabla').html(consumos);

                    //$.noConflict();
                    $('#tabla').DataTable({
                        dom: 'lBfrtip',

                        "language": {
                            "lengthMenu": "Mostrar _MENU_ registros",
                            "zeroRecords": "No existen registros",
                            "info": "Mostrando página _PAGE_ de _PAGES_",
                            "infoEmpty": "Sin registros",
                            "search": "Buscar",
                            "infoFiltered": "(filtered from _MAX_ total records)",
                            "copy": "Copiar",
                            "paginate": {
                                "first": "Primera",
                                "last": "Última",
                                "next": "Siguiente",
                                "previous": "Anterior"
                            },
                        },
                        buttons: [
                            'copyHtml5',
                            'excelHtml5',
                            'pdfHtml5',
                        ],
                        "lengthMenu": [
                            [10, 25, 50, -1],
                            [10, 25, 50, "Todos"]
                        ]
                    });
                }
            });
        });
    });


    function onReady(callback) {
        var intervalId = window.setInterval(function() {
            if (document.getElementsByTagName('body')[0] !== undefined) {
                window.clearInterval(intervalId);
                callback.call(this);
            }
        }, 1000);
    }

    function setVisible(selector, visible) {
        document.querySelector(selector).style.display = visible ? 'block' : 'none';
    }

    onReady(function() {
        // setVisible('.page', true);
        setVisible('#loading', false);
    });
</script>
@endsection
