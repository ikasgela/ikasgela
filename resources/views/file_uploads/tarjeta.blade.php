@section('fancybox')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css"/>
    <script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js" defer></script>
@endsection

<div class="card">
    <div class="card-header"><i class="fas fa-file-upload"></i> {{ __('File upload') }}</div>
    <div class="card-body">
        <h5 class="card-title">{{ $file_upload->titulo }}</h5>
        <p class="card-text">{{ $file_upload->descripcion }}</p>
        @if(count($file_upload->files) > 0)
            <div class="table-responsive">
                <table class="table table-bordered small">
                    <thead class="thead-dark">
                    <tr>
                        <th>{{ __('File') }}</th>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Size') }}</th>
                        <th>{{ __('Uploaded') }}</th>
                        @if(Route::currentRouteName() != 'archivo.show')
                            <th class="text-center">{{ __('Actions') }}</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($file_upload->files as $file)
                        <tr>
                            <td>
                                <a data-fancybox="gallery" href="{{ $file->imageUrl('images') }}">
                                    <img style="width:64px" src="{{ $file->imageUrl('thumbnails') }}">
                                </a>
                            </td>
                            <td>{{ $file->title }}</td>
                            <td>{{ $file->size_in_kb }} KB</td>
                            <td>{{ $file->uploaded_time }}</td>
                            @if(Route::currentRouteName() != 'archivo.show')
                                <td class="text-center">
                                    <div class='btn-group'>
                                        {!! Form::open(['route' => ['files.rotate', $file->id], 'method' => 'POST']) !!}
                                        <button title="{{ __('Rotate') }}"
                                                type="submit" class="btn btn-light btn-sm mr-1">
                                            <i class="fas fa-undo fa-flip-horizontal"></i>
                                        </button>
                                        {!! Form::close() !!}
                                        {!! Form::open(['route' => ['deletefile', $file->id], 'method' => 'DELETE']) !!}
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
    @if(count($file_upload->files) < $file_upload->max_files && Route::currentRouteName() != 'archivo.show')
        <hr class="my-0">
        <div class="card-body">
            <p class="small">{{ __('Upload limit') }}: {{ $file_upload->max_files-count($file_upload->files) }}</p>
            <form action="{{ route('uploadfile') }}" enctype="multipart/form-data" method="post">
                @csrf
                <div class="form-group">
                    <input type="file" name="file" id="file">
                    <input type="hidden" name="file_upload_id" value="{{ $file_upload->id }}">
                    <span class="help-block text-danger">{{ $errors->first('file') }}</span>
                </div>
                <button class="btn btn-primary">{{ __('Upload') }}</button>
            </form>
        </div>
    @endif
</div>
