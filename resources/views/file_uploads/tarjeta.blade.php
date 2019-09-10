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
                        <th class="text-center">{{ __('Actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($file_upload->files as $file)
                        <tr>
                            <td><a data-fancybox="gallery" href="{{ $file->url }}"><img style="width:64px"
                                                                                        src="{{ $file->url }}"></a></td>
                            <td>{{ $file->title }}</td>
                            <td>{{ $file->size_in_kb }} KB</td>
                            <td>{{ $file->uploaded_time }}</td>
                            <td class="text-center">
                                <form method="POST" action="{{ route('deletefile', [$file->id]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <div class='btn-group'>
                                        @include('partials.boton_borrar')
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @if(count($file_upload->files) < $file_upload->max_files)
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
