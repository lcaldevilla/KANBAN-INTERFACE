<table class="table table-image" id ="tabla">
    <thead>
        <tr>
            <th scope="col">Lector </th>
            <th scope="col">Descripción </th>
            <th scope="col">IP</th>
            <th scope="col">Estado</th>
            <th scope="col">Ordinarios</th>
            <th scope="col">Urgentes</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($array_dispositivos as $dispositivo)
        <tr>
            <td><img src="{{ asset('images/qbox_reader.png') }}" class="img-fluid img-thumbnail" alt="QBOX"
                    width="50" height="50"></td>
            <td> {{ $dispositivo['dsc_dispositivo'] }}</td>
            <td>
                {{ $dispositivo['ip_dispositivo'] }}
            </td>
            <td>
                <img src="{{ $dispositivo['imagen'] }}" class="img-fluid img-thumbnail" alt="QBOX"
                    width="50" height="50">
            </td>
            <td>{{ $dispositivo['peticiones_normales'] }}</td>
            <td>{{ $dispositivo['peticiones_urgentes'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>