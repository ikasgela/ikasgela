<div class="card">
    <div class="card-header"><i class="fas fa-link mr-2"></i>{{ __('Links') }}</div>
    <div class="card-body">
        <h5 class="card-title">{{ $link_collection->titulo }}</h5>
        <p class="card-text">{{ $link_collection->descripcion }}</p>
        @if(count($link_collection->links) > 0)
            <div class="table-responsive">
                <table class="table table-bordered small">
                    <thead class="thead-dark">
                    <tr>
                        <th>{{ __('Link') }}</th>
                        @if(Auth::user()->hasRole('profesor') && Route::currentRouteName() != 'archivo.show' && Route::currentRouteName() != 'actividades.preview')
                            <th>{{ __('Order') }}</th>
                            <th class="text-center">{{ __('Actions') }}</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($link_collection->links as $link)
                        <tr>
                            <td>
                                <a href="{{ $link->url }}" target="_blank">
                                    {{ $link->descripcion ?: $link->url }}
                                </a>
                            </td>
                            @if(Auth::user()->hasRole('profesor') && Route::currentRouteName() != 'archivo.show' && Route::currentRouteName() != 'actividades.preview')
                                <td>
                                    @include('partials.botones_reordenar', ['ruta' => 'links.reordenar'])
                                </td>
                                <td class="text-center">
                                    <div class='btn-group'>
                                        {!! Form::open(['route' => ['links.destroy', $link->id], 'method' => 'DELETE']) !!}
                                        @include('partials.boton_borrar')
                                        {!! Form::close() !!}
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @if(Auth::user()->hasRole('profesor') && Route::currentRouteName() != 'archivo.show' && Route::currentRouteName() != 'actividades.preview')
        <hr class="my-0">
        <div class="card-body">
            {!! Form::open(['route' => ['links.store']]) !!}

            {{ Form::campoTexto('url', __('URL'), '', ['placeholder' => 'https://ikasgela.com']) }}
            {{ Form::campoTexto('descripcion', __('Description')) }}
            {!! Form::hidden('link_collection_id', $link_collection->id) !!}

            <span class="help-block text-danger">{{ $errors->first('url') }}</span>

            <button class="btn btn-primary single_click">
                <i class="fas fa-spinner fa-spin" style="display:none;"></i> {{ __('Add') }}
            </button>

            {!! Form::close() !!}
        </div>
    @endif
</div>
