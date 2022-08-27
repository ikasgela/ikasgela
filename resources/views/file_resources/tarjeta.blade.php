<div class="card">
    <div class="card-header"><i class="fas fa-file mr-2"></i>{{ __('Files') }}</div>
    <div class="card-body">
        <h5 class="card-title">{{ $file_resource->titulo }}</h5>
        <p class="card-text">{{ $file_resource->descripcion }}</p>
        @if(count($file_resource->files) > 0)
            <div class="table-responsive">
                <table class="table table-bordered small">
                    <thead class="thead-dark">
                    <tr>
                        <th></th>
                        <th>{{ __('File') }}</th>
                        <th>{{ __('Size') }}</th>
                        @if(Auth::user()->hasRole('profesor') && Route::currentRouteName() != 'archivo.show')
                            <th>{{ __('Uploaded') }}</th>
                            <th class="text-center">{{ __('Actions') }}</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($file_resource->files as $file)
                        <tr>
                            <td class="text-center font-xl">@include('file_resources.partials.icono', ['extension' => $file->extension])</td>
                            <td>
                                <a href="{{ $file->imageUrl('documents') }}">
                                    {{ $file->description ?: $file->title }}
                                </a>
                            </td>
                            <td>{{ $file->size_in_kb }} KB</td>
                            @if(Auth::user()->hasRole('profesor') && Route::currentRouteName() != 'archivo.show')
                                <td>{{ $file->uploaded_time }}</td>
                                <td class="text-center">
                                    <div class='btn-group'>
                                        {!! Form::open(['route' => ['files.delete', $file->id], 'method' => 'DELETE']) !!}
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
    @if(count($file_resource->files) < $file_resource->max_files && Route::currentRouteName() != 'archivo.show' && Route::currentRouteName() != 'actividades.preview' || !Auth::user()->hasRole('alumno'))
        <hr class="my-0">
        <div class="card-body">
            <form action="{{ route('files.upload.document') }}" enctype="multipart/form-data" method="post">
                @csrf
                <div class="form-group">
                    {{ Form::campoTexto('description', __('Description')) }}
                    <input type="file" name="file" id="file">
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
