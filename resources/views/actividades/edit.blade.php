@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit activity')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($actividad, ['route' => ['actividades.update', $actividad->id], 'method' => 'PUT']) !!}

            <div class="form-group row">
                {!! Form::label('unidad', __('Unit'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <select class="form-control" id="unidad_id" name="unidad_id">
                        @foreach($unidades as $unidad)
                            <option
                                value="{{ $unidad->id }}" {{ $actividad->unidad_id == $unidad->id ? 'selected' : '' }}>
                                @isset($unidad->codigo)
                                    {{ $unidad->codigo }} -
                                @endisset
                                {{ $unidad->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{ Form::campoTexto('nombre', __('Name')) }}
            {{ Form::campoTexto('descripcion', __('Description')) }}
            {{ Form::campoTexto('slug', __('Slug')) }}
            {{ Form::campoTexto('puntuacion', __('Score')) }}
            {{ Form::campoCheck('plantilla', __('Template')) }}
            {{ Form::campoTexto('orden', __('Order')) }}

            <div class="form-group row">
                {!! Form::label('siguiente_id', __('Next'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <select class="form-control" id="siguiente_id" name="siguiente_id">
                        <option value="">{{ __('--- None ---') }}</option>
                        @if($actividad->plantilla)
                            @foreach($plantillas as $temp)
                                <option
                                    value="{{ $temp->id }}" {{ !is_null($actividad->siguiente) && $actividad->siguiente->id == $temp->id ? 'selected' : '' }}>
                                    {{ $temp->slug . ' ('. $temp->id . ')' }}
                                </option>
                            @endforeach
                        @else
                            @foreach($actividades as $temp)
                                <option
                                    value="{{ $temp->id }}" {{ !is_null($actividad->siguiente) && $actividad->siguiente->id == $temp->id ? 'selected' : '' }}>
                                    {{ $temp->slug . ' ('. $temp->id . ')' }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            {{ Form::campoCheck('siguiente_overriden', __('Next field overriden')) }}
            {{ Form::campoCheck('final', __('Final')) }}
            {{ Form::campoCheck('auto_avance', __('Auto advance')) }}

            <div class="form-group row">
                {!! Form::label('qualification_id', __('Qualification'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <select class="form-control" id="qualification_id" name="qualification_id">
                        <option value="">{{ __('--- None ---') }}</option>
                        @foreach($qualifications as $qualification)
                            <option
                                value="{{ $qualification->id }}" <?php if ($actividad->qualification_id == $qualification->id) echo 'selected'; ?>>
                                {{ $qualification->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{ Form::campoTexto('fecha_disponibilidad', __('Availability date')) }}
            {{ Form::campoTexto('fecha_entrega', __('Due date')) }}
            {{ Form::campoTexto('fecha_limite', __('Deadline')) }}

            {{ Form::campoCheck('destacada', __('Highlighted')) }}
            {{ Form::campoTexto('tags', __('Tags')) }}

            {{ Form::campoTexto('multiplicador', __('Multiplier')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
