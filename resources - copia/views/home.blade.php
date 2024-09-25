@extends('plantilla')

@section('title', 'Gestión')

@section('cabecera')
<link rel="stylesheet" type="text/css" href="{{ asset('css/style_clock.css') }}" />
@endsection

@section('content')
<div class="container">

    <div class="row">
        <!-- Muestra un calendario en el centro de la pantalla -->
        <div class="container">
            <div class="row">
                <div id="clock" class="light">
                    <div class="display">
                        <div class="weekdays"></div>
                        <div class="ampm"></div>
                        <div class="digits"></div>
                    </div>
                </div>

            </div>
        </div>


        <div class="row table-responsive">
            @include('tabla_home')
            
        </div>

    </div>
    @endsection

    @section('script')
    <script type="text/javascript" src="{{ asset('js/script_clock.js') }}"></script>
    <script type="text/javascript">
        var timer = null;

function goAway() {
    /*clearTimeout(timer);
    timer = setInterval(function() {*/
        //window.location.reload();
        $.ajax({
                url: "{{ route('index.update') }}",
                method: "GET",
                success: function(consumos) {
                    //alert("update");
                    $('#tabla').html(consumos);
                }
            });
  //  }, 5000);
}

//window.addEventListener('mousemove', goAway, true);
setInterval(goAway, 5000);
goAway();  // start the first timer off
    </script>
    @endsection