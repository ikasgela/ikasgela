<h2>{{ __('Skills development')}}</h2>

@if(count($calificaciones->skills_curso) > 0)
    <table class="tabla-datos">
        <tr>
            <th class="text-left">{{ __('Skill') }}</th>
            <th>{{ __('Activities') }}</th>
            @if($calificaciones->hayExamenes)
                <th>{{ __('Exams') }}</th>
            @endif
            <th>{{ __('Total') }}</th>
        </tr>
        @foreach ($calificaciones->skills_curso as $skill)

            @php($resultado = $calificaciones->resultados[$skill->id])
            @php($peso_examenes = $skill->peso_examen)

            <tr>
                <td>{{ $skill->name }}</td>

                @php($porcentaje_tarea = $resultado->porcentaje_tarea())
                <td class="text-center">
                    {{ formato_decimales($porcentaje_tarea) }}&thinsp;%
                </td>
                @if($peso_examenes>0)
                    @php($porcentaje_examen = $resultado->porcentaje_examen())
                    <td class="text-center">
                        {{ formato_decimales($porcentaje_examen) }}&thinsp;%
                    </td>
                @elseif($calificaciones->hayExamenes)
                    <td class="text-center">-</td>
                @endif
                @php($porcentaje_competencia = $resultado->porcentaje_competencia())
                <td class="text-center {{ $porcentaje_competencia < $calificaciones->minimo_competencias ? 'bg-warning text-dark' : 'bg-success' }}">
                    {{ formato_decimales($porcentaje_competencia) }}&thinsp;%
                </td>
            </tr>
        @endforeach
    </table>
@else
    <p>{{ __('No skills assigned.') }}</p>
@endif
