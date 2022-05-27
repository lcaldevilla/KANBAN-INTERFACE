<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    <!-- favicon -->

    <link rel="shortcut icon" type="image/png" href="{{ asset('images/favicon_qbox.png') }}" />
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" id="bootstrap-css">
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/moment.js') }}"></script>
    <!-- estilos -->
    <link href="{{ asset('css/fontawesome.min.css') }}" rel="stylesheet">
    <!-- Include Bootstrap Datepicker -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap-datepicker.min.css') }}" />
    <script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datepicker.es.min.js') }}"></script>

    <!-- Include Bootstrap Datatables -->

    <link rel="stylesheet" type="text/css" href="{{ asset('css/datatables.min.css') }}" />

    <script type="text/javascript" src="{{ asset('js/pdfmake.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vfs_fonts.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/datatables.min.js') }}"></script>


    <script type="text/javascript" src="http://localhost/charts/loader.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <style>
        #loading {
            display: block;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 100;
            width: 100vw;
            height: 100vh;
            background-color: rgba(222, 222, 222, 0.75);
            background-image: url('{{ asset('images/MnyxU.gif') }}');
            background-repeat: no-repeat;
            background-position: center;
        }

        #loading_hijo {
            position: absolute;
            top: 50%;
            left: 50%;
            height: 30%;
            width: 50%;
            margin: -15% 0 0 -25%;
            color: black;
        }

        a:link,
        a:visited,
        a:active {
            text-decoration: none;
            color: black;
        }

        #temps_div {
            height: 100% !important;
            width: 100% !important;
        }

        .standard-margin {
            width: 97%;
            margin: 0 auto;
        }
    </style>

    @yield('cabecera')


</head>

<body>
    <div class="navbar sticky-top navbar-expand-md navbar-dark bg-dark mb-4" role="navigation">
        <a class="navbar-brand" href="{{ url('/') }}">QBOX Management v1.1</a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/') }}">Inicio</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="dropdown1" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Consumos</a>
                    <ul class="dropdown-menu" aria-labelledby="dropdown1">
                        <li class="dropdown-item "><a href="consumos"> Artículos Recurrentes</a></li>
                        <li class="dropdown-item"><a href="consumosUrgentes">Artículos Urgentes</a></li>
                        <li class="dropdown-item"><a href="sinConsumos">Artículos Sin Movimientos</a></li>
                        <li class="dropdown-item"><a href="consumosAgrupados">Artículos Recurrentes Agrupados</a></li>
                        <li class="dropdown-item"><a href="consumosAgrupadosUrgentes">Artículos Urgentes Agrupados</a></li>
                        <li class="dropdown-item"><a href="consumosPorAlmacen">Artículos Por Almacén</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="dropdown1" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Gráficas</a>
                    <ul class="dropdown-menu" aria-labelledby="dropdown1">
                        <li class="dropdown-item"> <a href="graficaNormales">Artículos Normales</a></li>
                        <li class="dropdown-item"> <a href="graficaUrgentes">Artículos Urgente</a></li>
                        <li class="dropdown-item"> <a href="graficaArticulo">Por Artículos</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('reposiciones') }}">Gestión Reposiciones</a>
                </li>
            </ul>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Salir</button>
        </form>

    </div>


    @yield('content')

    <!-- footer -->

    <!-- escript -->
    @yield('script')

</body>

</html>