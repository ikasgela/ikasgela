<h2>{{ __('Evaluation and calification') }}</h2>

<table class="tabla-marcador-contenedor">
    <tr>
        @if($curso->minimo_entregadas > 0)
            <td>
                <table
                    class="tabla-marcador {{ $calificaciones->examen_final || $calificaciones->hay_nota_manual ? 'bg-light text-dark' : ($calificaciones->actividades_obligatorias_superadas ? 'bg-success text-dark' : 'bg-warning text-dark') }}">
                    <tr>
                        <th class="text-left">{{ __('Mandatory activities') }}</th>
                    </tr>
                    <tr>
                        <td class="text-center">{{ $calificaciones->num_actividades_obligatorias > 0 ? $calificaciones->actividades_obligatorias_superadas ? trans_choice('tasks.completed', 2) : ($calificaciones->numero_actividades_completadas+0)."/".($calificaciones->num_actividades_obligatorias+0)  : __('None') }}</td>
                    </tr>
                </table>
            </td>
        @endif
        @if($calificaciones->minimo_competencias > 0)
            <td>
                <table
                    class="tabla-marcador {{ $calificaciones->examen_final || $calificaciones->hay_nota_manual ? 'bg-light text-dark' : ($calificaciones->competencias_50_porciento ? 'bg-success text-dark' : 'bg-warning text-dark') }}">
                    <tr>
                        <th class="text-left">{{ __('Skills') }}</th>
                    </tr>
                    <tr>
                        <td class="text-center">{{ $calificaciones->competencias_50_porciento ? trans_choice('tasks.passed', 2) : trans_choice('tasks.not_passed', 2) }}</td>
                    </tr>
                </table>
            </td>
        @endif
        @if($calificaciones->num_pruebas_evaluacion > 0)
            <td>
                <table
                    class="tabla-marcador {{ ($curso->examenes_obligatorios || $calificaciones->examen_final) && !$calificaciones->hay_nota_manual ? ($calificaciones->examen_final && !$calificaciones->examen_final_superado ? 'bg-warning text-dark': ($calificaciones->pruebas_evaluacion ? 'bg-success text-dark' : 'bg-warning text-dark')) : 'bg-light text-dark' }}">
                    <tr>
                        <th class="text-left">{{ __('Assessment tests') }}</th>
                    </tr>
                    <tr>
                        <td class="text-center">{{ $calificaciones->num_pruebas_evaluacion > 0 ? ($calificaciones->examen_final && !$calificaciones->examen_final_superado ? trans_choice('tasks.not_passed', 2) : ($calificaciones->pruebas_evaluacion ? trans_choice('tasks.passed', 2) : trans_choice('tasks.not_passed', 2))) : __('None') }}</td>
                    </tr>
                </table>
            </td>
        @endif
        <td>
            <table
                class="tabla-marcador {{ $calificaciones->examen_final || $calificaciones->hay_nota_manual ? 'bg-light text-dark' : ($calificaciones->evaluacion_continua_superada ? 'bg-success text-dark' : 'bg-warning text-dark') }}">
                <tr>
                    <th class="text-left">{{ __('Continuous evaluation') }}</th>
                </tr>
                <tr>
                    <td class="text-center">{{ $calificaciones->evaluacion_continua_superada ? trans_choice('tasks.passed', 1) : trans_choice('tasks.not_passed', 1) }}</td>
                </tr>
            </table>
        </td>
        <td>
            <table
                class="tabla-marcador {{ ($calificaciones->evaluacion_continua_superada || $calificaciones->examen_final_superado || $calificaciones->nota_manual_superada) ? 'bg-success text-dark' : ($curso->disponible() ? 'bg-light text-dark' : 'bg-warning text-dark') }}">
                <tr>
                    <th class="text-left">{{ __('Calification') }}</th>
                </tr>
                <tr>
                    <td class="text-center">{{ ($calificaciones->evaluacion_continua_superada || $calificaciones->examen_final_superado || $calificaciones->nota_manual_superada) ? $calificaciones->nota_final : ($curso->disponible() ? __('Unavailable') : __('Fail')) }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
