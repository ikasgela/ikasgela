<div class="card mb-3">
    <div class="card-header d-flex justify-content-between">
        <div><i class="fas fa-link me-2"></i>{{ __('Links') }}</div>
        <div>
            @include('partials.ver_recurso', ['recurso' => $link_collection, 'ruta' => 'link_collections'])
            @include('partials.modificar_recursos', ['ruta' => 'link_collections'])
            @include('partials.editar_recurso', ['recurso' => $link_collection, 'ruta' => 'link_collections'])
        </div>
    </div>
    <div class="card-body">
        @include('partials.cabecera_recurso', ['recurso' => $link_collection, 'ruta' => 'link_collections'])
        @if(count($link_collection->links) > 0)
            <div class="table-responsive">
                <table class="table table-bordered small m-0">
                    <thead class="thead-dark">
                    <tr>
                        <th>{{ __('Link') }}</th>
                        @if(Auth::user()->hasRole('profesor') && Route::currentRouteName() == 'link_collections.show')
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
                            @if(Auth::user()->hasRole('profesor') && Route::currentRouteName() == 'link_collections.show')
                                <td>
                                    @include('partials.botones_reordenar', ['ruta' => 'links.reordenar'])
                                </td>
                                <td class="text-center">
                                    <div class='btn-group'>
                                        {{ html()->form('DELETE', route('links.destroy', $link->id))->open() }}
                                        @include('partials.boton_borrar')
                                        {{ html()->form()->close() }}
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
    @if(Auth::user()->hasRole('profesor') && Route::currentRouteName() == 'link_collections.show')
        <hr class="my-0">
        <div class="card-body">
            {{ html()->form('POST', route('links.store'))->open() }}

            @include('components.label-text', [
                'label' => __('URL'),
                'name' => 'url',
                'placeholder' => 'https://ikasgela.com',
            ])
            @include('components.label-text', [
                'label' => __('Description'),
                'name' => 'descripcion',
            ])
            {{ html()->hidden('link_collection_id', $link_collection->id) }}

            <div class="help-block text-danger mb-3">{{ $errors->first('url') }}</div>

            <button class="btn btn-primary single_click">
                <i class="fas fa-spinner fa-spin" style="display:none;"></i> {{ __('Add') }}
            </button>

            {{ html()->form()->close() }}
        </div>
    @endif
</div>
