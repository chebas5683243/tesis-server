<table>
    <thead>
        <tr>
            <td>Fecha del registro</td>
            <td>Hora del registro</td>
            @foreach($parametros as $parametro)
                <td>{{ $parametro->nombre }}</td>
            @endforeach
        </tr>
        <tr>
            <td>Fecha (dd/mm/yyyy)</td>
            <td>Hora (hh:mm:ss)</td>
            @foreach($parametros as $parametro)
                <td>{{ $parametro->nombre_corto . ' (' . $parametro->unidad_corto . ')' }}</td>
            @endforeach
        </tr>
    </thead>
    <tbody>
        <tr>
            <td></td>
            <td></td>
            @foreach($parametros as $parametro)
                <td></td>
            @endforeach
            <td>Completar las casillas</td>
        </tr>
    </tbody>
</table>