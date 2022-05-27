@extends('plantilla')

@section('title', 'consumos almacen ')

@section('content')

    <!-- Filtro de opciones de visualización -->
    <h1 class="standard-margin">Gestión de Reposiciones </h1>
    <div class="card card-default">
        <div id="loading" class="text-center text-danger">
            <div id="loading_hijo">
                <h2>Cargando datos...</h2>
            </div>
        </div>

        <div id="collapse1" class="collapse show">
            <div class="card-body">
                <div class="row">

                    <div class="col-md-3 col-lg-3">
                        <div class="form-group">
                            <label class="control-label "><strong>Servicio</strong></label>
                            <select name="servicio" id="servicio" class="form-control input-lg btn-submit"
                                data-dependent="almacen">
                                <option value="0">Todos los servicios</option>
                                @foreach ($servicios as $servicio)
                                    <option value="{{ $servicio->cod_servicio }}">{{ $servicio->dsc_servicio }}</option>
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
                            <label class="control-label"><strong>&nbsp;</strong></label>
                            <button type="button" id="btnActualizar"
                                class="form-control btn btn-primary align-center">Actualizar</button>
                        </div>
                    </div>

                    <div class="col-md-2 col-lg-2">
                        <div class="form-group">
                            <label class="control-label"><strong>&nbsp;</strong></label>
                            <button type="button" id="btnProcesar"
                                class="form-control btn btn-primary btn-success align-center">Procesar</button>
                        </div>
                    </div>

                    <div class="col-md-2 col-lg-2">
                        <div class="form-group">
                            <label class="control-label"><strong>&nbsp;</strong></label>
                            <button type="button" id="btnEliminar"
                                class="form-control btn btn-primary btn-danger align-center">Eliminar</button>
                        </div>
                    </div>

                </div>

                {{ csrf_field() }}
            </div>


        </div>

    </div>

    <br />

    <!-- ********************************************************************************************************************** -->
    <div class="table-responsive standard-margin" id="table_data">
        @include('reposiciones_pagination')
    </div>


@endsection

@section('script')
    <script type="text/javascript">
        // Gestiona la tabla de datos por defecto
        var editor;

        $(document).ready(function() {
            $.noConflict();
            // Activate an inline edit on click of a table cell
            $('#tabla').on('click', 'tbody td:not(:first-child)', function(e) {
                editor.inline(this);
            });
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
                    url: "{{ route('reposiciones.post') }}",
                    data: {
                        status: status
                    },
                    success: function(data) {
                        $("#loading").show();
                        if (data) {
                            $("#almacen").empty();
                            $("#almacen").append(
                                '<option value="0">Todos los almacenes</option>');

                            $.each(data, function(key, value) {
                                $.each(value, function(i, j) {
                                    $("#almacen").append('<option value="' +
                                        value[i].cod_almacen + '">' + value[
                                            i].dsc_almacen + '</option>');
                                });
                            })
                        }
                        $("#loading").hide();
                    }
                });
            });

            $('#select_all').change(function(e) {
                //alert("check");
                var c = this.checked;
                $(':checkbox').prop('checked', c);
            });

            // actualiza la tabla según los filtro
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

                $.ajax({
                    url: "{{ route('reposiciones.update') }}",
                    method: "POST",
                    data: {
                        almacen: almacen,
                        servicio: servicio,
                    },
                    success: function(consumos) {
                        //alert("update");
                        $('#tabla').html(consumos);

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
                // recargo el gestor de eventos

                $(document).on('click', '#select_all', function() {
                    var c = this.checked;
                    $(':checkbox').prop('checked', c);
                });
            });

            // actualiza la tabla según los filtro
            $("#btnProcesar").click(function(e) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                e.preventDefault();
                var status = this.value;
                // seleccionamos los checcboxes a procesar
                var selected = new Array();
                $("#tabla input[type=checkbox]:checked").each(function() {
                    selected.push(this.value);
                });

                $('#tabla').DataTable().destroy();
                var almacen = $("#almacen").val();
                var servicio = $("#servicio").val();

                $.ajax({
                    url: "{{ route('reposiciones.procesar') }}",
                    method: "POST",
                    data: {
                        almacen: almacen,
                        servicio: servicio,
                        selected: selected,
                    },
                    success: function(consumos) {
                        alert("Peticiones procesadas correctamente");
                        $('#tabla').html(consumos);

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

            // actualiza la tabla según los filtro
            $("#btnEliminar").click(function(e) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                e.preventDefault();
                var status = this.value;
                // seleccionamos los checcboxes a procesar
                var selected = new Array();
                $("#tabla input[type=checkbox]:checked").each(function() {
                    selected.push(this.value);
                });

                $('#tabla').DataTable().destroy();
                var almacen = $("#almacen").val();
                var servicio = $("#servicio").val();

                $.ajax({
                    url: "{{ route('reposiciones.eliminar') }}",
                    method: "POST",
                    data: {
                        almacen: almacen,
                        servicio: servicio,
                        selected: selected,
                    },
                    success: function(consumos) {
                        alert("Reposición eliminada de la lista de peticiones");
                        $('#tabla').html(consumos);

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


        // editamos la tabla
        $("#tabla").on('mousedown.edit', "i.bi.bi-pencil-square", function(e) {
            $(this).removeClass().addClass("bi bi-save");
            var $row = $(this).closest("tr").off("mousedown");
            var $tds = $row.find("td").not(':first').not(':last');
            $.each($tds, function(i, el) {
                var txt = $(this).text().trim();
                if ($(this).attr("name") == "cant_reposicion") { // solo permite editar el campo de cantidad
                    $(this).html("").append("<input type='text' value=\"" + txt + "\">");
                }
            });
        });

        $("#tabla").on('mousedown', "input", function(e) {
            e.stopPropagation();
        });

        // almacena el valor de la cantidad modificada
        $("#tabla").on('mousedown.save', "i.bi.bi-save", function(e) {
            var cant_reposicion;
            var stock_id;

            $(this).removeClass().addClass("bi bi-pencil-square");
            var $row = $(this).closest("tr");
            var $tds = $row.find("td").not(':first').not(':last');

            $.each($tds, function(i, el) {

                if ($(this).attr("name") == "cant_reposicion") {
                    cant_reposicion = $(this).find("input").val();
                    stock_id = $(this).attr('data-id');

                };

                var txt = $(this).find("input").val()
                $(this).html(txt);
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            e.preventDefault();
            var status = this.value;

            $.ajax({
                url: "{{ route('reposiciones.actualizarCantReposicion') }}",
                method: "POST",
                data: {
                    cant_reposicion: cant_reposicion,
                    stock_id: stock_id,
                },
                success: function(msg) {
                    alert("Se ha actualizado la cantidad con exito ");
                }
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

        //*

    </script>
@endsection
