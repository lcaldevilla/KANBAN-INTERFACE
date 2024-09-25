@extends('plantilla')

@section('title', 'Gráfica Consumos Normales ')

@section('content')

<!-- Filtro de opciones de visualización -->
<h1 class="standard-margin">Gráfica Consumo Artículos </h1>
<div class="card card-default">
    <div id="loading" class="text-center text-danger">
        <div id="loading_hijo">
            <h2>Cargando datos...</h2>
        </div>
    </div>

    <div id="collapse1" class="collapse show">
        <div class="card-body">
            <div class="row">


                <div class="col-md-2 col-lg-2">
                    <div class="form-group">
                        <label class="control-label"><strong>Artículo</strong></label>
                        <select name="ano" id="articulo" class="form-control input-lg btn-submit" data-dependent="articulo">
                            <option value="0">Elegir artículo</option>
                            @foreach ($articulos as $articulo)
                            <option value="{{ $articulo->cod_producto}}">{{ $articulo->cod_producto }} - {{ $articulo->dsc_producto }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-2 col-lg-2">
                    <div class="form-group">
                        <label class="control-label"><strong>Año</strong></label>
                        <select name="ano" id="ano" class="form-control input-lg btn-submit" data-dependent="ano">
                            <option value="0">Todos los años</option>
                            @foreach ($anos as $ano)
                            <option value="{{ $ano->ano}}">{{ $ano->ano }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

              <!--  <div class="col-md-2 col-lg-2">
                    <div class="form-group">
                        <label class="control-label"><strong>&nbsp;</strong></label>
                        <button type="button" id="btnActualizar" class="form-control btn btn-primary align-center">Actualizar</button>
                    </div>
                </div>
                -->
            </div>
            <!-- MUESTRA LA ELECCION DE ALMACENES -->
            <div class="row">

                <div class="col-md-2 col-lg-2">
                    <div class="form-group">
                        <label class="control-label"><strong>Almacén</strong></label>
                        <select name="almacen1" id="almacen1" class="form-control input-lg dynamic">
                            <option value="0">No comparar</option>
                            @foreach ($almacenes as $almacen)
                            <option value="{{ $almacen->cod_almacen}}">{{ $almacen->dsc_almacen }}</option>
                            @endforeach

                        </select>
                    </div>
                </div>

                <div class="col-md-2 col-lg-2">
                    <div class="form-group">
                        <label class="control-label"><strong>Almacén</strong></label>
                        <select name="almacen2" id="almacen2" class="form-control input-lg dynamic">
                            <option value="0">No comparar</option>
                            @foreach ($almacenes as $almacen)
                            <option value="{{ $almacen->cod_almacen}}">{{ $almacen->dsc_almacen }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <div class="col-md-2 col-lg-2">
                    <div class="form-group">
                        <label class="control-label"><strong>Almacén</strong></label>
                        <select name="almacen3" id="almacen3" class="form-control input-lg dynamic">
                            <option value="0">No comparar</option>
                            @foreach ($almacenes as $almacen)
                            <option value="{{ $almacen->cod_almacen}}">{{ $almacen->dsc_almacen }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-2 col-lg-2">
                    <div class="form-group">
                        <label class="control-label"><strong>Almacén</strong></label>
                        <select name="almacen4" id="almacen4" class="form-control input-lg dynamic">
                            <option value="0">No comparar</option>
                            @foreach ($almacenes as $almacen)
                            <option value="{{ $almacen->cod_almacen}}">{{ $almacen->dsc_almacen }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-2 col-lg-2">
                    <div class="form-group">
                        <label class="control-label"><strong>&nbsp;</strong></label>
                        <button type="button" id="btnComparar" class="form-control btn btn-primary align-center">Comparar</button>
                    </div>
                </div>
            </div>

            {{ csrf_field() }}
        </div>


    </div>

</div>

@include('graficaNormales_chart')

<br />

<!-- ********************************************************************************************************************** -->



@endsection

@section('script')
<script type="text/javascript">
    // Gestiona la tabla de datos por defecto
    $(document).ready(function() {
        $.noConflict();

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
                url: "{{ route('graficaNormales.almacenesPost') }}",
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

        // actualiza la grafica según los filtros
        $("#btnActualizar").click(function(e) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            e.preventDefault();
            var status = this.value;
            var almacen = $("#almacen").val();
            var servicio = $("#servicio").val();
            var ano = $("#ano").val();


            $.ajax({
                url: "{{ route('graficaNormales.update') }}",
                method: "POST",
                data: {
                    almacen: almacen,
                    servicio: servicio,
                    ano: ano
                },
                success: function(consumos) {
                    //alert(consumos);
                    $('#temps_div').html(consumos);
                }
            });
        });

        // -------------------------------------------------------------------------
        // actualiza la grafica con comparacion 
        $("#btnComparar").click(function(e) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            e.preventDefault();
            var status = this.value;
            var articulo = $("#articulo").val();
            var almacen1 = $("#almacen1").val();
            var almacen2 = $("#almacen2").val();
            var almacen3 = $("#almacen3").val();
            var almacen4 = $("#almacen4").val();


            // compruebo que al menos un almacen tiene valor
            if (almacen1 == 0 && almacen2 == 0 && almacen3 == 0 && almacen4 == 0) {
                alert("Es necesario seleccionar al menos un almacen");
            } else {

                $.ajax({
                    url: "{{ route('graficaArticulos.compara') }}",
                    method: "POST",
                    data: {
                        articulo: articulo,
                        almacen1: almacen1,
                        almacen2: almacen2,
                        almacen3: almacen3,
                        almacen4: almacen4
                    },
                    success: function(consumos) {
                        //alert(consumos);
                        $('#temps_div').html(consumos);
                    }
                });
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
</script>
@endsection