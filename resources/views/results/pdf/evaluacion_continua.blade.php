<h2>{{ __('Evaluation and calification') }}</h2>

<table class="tabla-marcador-contenedor">
    <tr>
        @if($curso->minimo_entregadas > 0)
            <td>
                <table class="tabla-marcador {{ $actividades_obligatorias_fondo }}">
                    <tr>
                        <th class="text-left">{{ __('Mandatory activities') }}</th>
                    </tr>
                    <tr>
                        <td class="text-center">{{ $actividades_obligatorias_dato }}</td>
                    </tr>
                </table>
            </td>
        @endif
        @if($calificaciones->minimo_competencias > 0)
            <td>
                <table class="tabla-marcador {{ $competencias_fondo }}">
                    <tr>
                        <th class="text-left">{{ __('Skills') }}</th>
                    </tr>
                    <tr>
                        <td class="text-center">{{ $competencias_dato }}</td>
                    </tr>
                </table>
            </td>
        @endif
        @if($calificaciones->num_pruebas_evaluacion > 0)
            <td>
                <table class="tabla-marcador {{ $pruebas_evaluacion_fondo }}">
                    <tr>
                        <th class="text-left">{{ __('Assessment tests') }}</th>
                    </tr>
                    <tr>
                        <td class="text-center">{{ $pruebas_evaluacion_dato }}</td>
                    </tr>
                </table>
            </td>
        @endif
        <td>
            <table class="tabla-marcador {{ $evaluacion_continua_fondo }}">
                <tr>
                    <th class="text-left">{{ __('Continuous evaluation') }}</th>
                </tr>
                <tr>
                    <td class="text-center">{{ $evaluacion_continua_dato }}</td>
                </tr>
            </table>
        </td>
        <td>
            <table class="tabla-marcador {{ $calificacion_fondo }}">
                <tr>
                    <th class="text-left">{{ __('Calification') }}</th>
                </tr>
                <tr>
                    <td class="text-center">{{ $calificacion_dato }}
                        @if(isset($milestone))
                            ({{ $calificacion_dato_publicar }})
                        @endif
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
