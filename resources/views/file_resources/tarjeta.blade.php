<div class="card mb-3">
    <div class="card-header d-flex justify-content-between">
        <div><i class="fas fa-file me-2"></i>{{ __('Files') }}</div>
        <div>
            @include('partials.ver_recurso', ['recurso' => $file_resource, 'ruta' => 'file_resources'])
            @include('partials.modificar_recursos', ['ruta' => 'file_resources'])
            @include('partials.editar_recurso', ['recurso' => $file_resource, 'ruta' => 'file_resources'])
        </div>
    </div>
    <div class="card-body">
        @include('partials.cabecera_recurso', ['recurso' => $file_resource, 'ruta' => 'file_resources'])
        @if(count($file_resource->files) > 0)
            <div class="table-responsive">
                <table class="table table-bordered small m-0">
                    <thead class="thead-dark">
                    <tr>
                        <th></th>
                        <th>{{ __('File') }}</th>
                        <th>{{ __('Size') }}</th>
                        @if(Auth::user()->hasRole('profesor') && Route::currentRouteName() == 'file_resources.show')
                            <th>{{ __('Uploaded') }}</th>
                            <th>{{ __('Order') }}</th>
                            <th class="text-center">{{ __('Actions') }}</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($file_resource->files as $file)
                        @if($file->visible || Auth::user()->hasRole('profesor') && Route::currentRouteName() == 'file_resources.show')
                            <tr>
                                <td class="text-center font-xl">@include('file_resources.partials.icono', ['extension' => $file->extension])</td>
                                <td>
                                    <a href="{{ $file->imageUrl('documents') }}"
                                        @include('file_resources.partials.destino', ['extension' => $file->extension])>
                                        {{ $file->description ?: $file->title }}
                                    </a>
                                </td>
                                <td>{{ $file->size_in_kb }} KB</td>
                                @if(Auth::user()->hasRole('profesor') && Route::currentRouteName() == 'file_resources.show')
                                    <td>{{ $file->uploaded_time }}</td>
                                    <td>
                                        @include('partials.botones_reordenar', ['ruta' => 'files.reordenar'])
                                    </td>
                                    <td class="text-center">
                                        <div class='btn-group me-2'>
                                            {{ html()->form('POST', route('files.toggle.visible', $file->id))->open() }}
                                            {{ html()->submit($file->visible ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>')
                                                        ->class(['btn btn-sm', $file->visible ? 'btn-primary' : 'btn-light'])
                                                        ->attribute('title', $file->visible ? __('Visible') : __('Hidden')) }}
                                            {{ html()->form()->close() }}
                                        </div>
                                        <div class='btn-group'>
                                            {{ html()->form('DELETE', route('files.delete', $file->id))->open() }}
                                            @include('partials.boton_borrar')
                                            {{ html()->form()->close() }}
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @if(Auth::user()->hasRole('profesor') && Route::currentRouteName() == 'file_resources.show')
        <hr class="my-0">
        <div class="card-body">
            <form action="{{ route('files.upload.document') }}" enctype="multipart/form-data" method="post">
                @csrf
                <div class="form-group">
                    @include('components.label-text', [
                        'label' => __('Description'),
                        'name' => 'description',
                    ])
                    <input type="file" class="form-control mb-3" name="file" id="file">
                    <input type="hidden" name="file_resource_id" value="{{ $file_resource->id }}">
                    <span class="help-block text-danger">{{ $errors->first('file') }}</span>
                </div>
                <button class="btn btn-primary single_click">
                    <i class="fas fa-spinner fa-spin" style="display:none;"></i> {{ __('Upload') }}
                </button>
            </form>
        </div>
    @endif
</div>
