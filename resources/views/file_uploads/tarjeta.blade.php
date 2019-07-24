<div class="card">
    <div class="card-header"><i class="fas fa-file-upload"></i> {{ __('File upload') }}</div>
    <div class="card-body">
        <h5 class="card-title">{{ $file_upload->titulo }}</h5>
        <p class="card-text">{{ $file_upload->descripcion }}</p>
        <form action="{{ route('uploadfile') }}" enctype="multipart/form-data" method="post">
            @csrf
            <div class="form-group">
                <input type="file" name="file" id="file">
                <input type="hidden" name="file_upload_id" value="{{ $file_upload->id }}">
                <span class="help-block text-danger">{{ $errors->first('file') }}</span>
            </div>
            <button class="btn btn-primary">{{ __('Upload') }}</button>
            <hr>
            <table class="table table-bordered">
                <tr>
                    <th>File</th>
                    <th>Caption</th>
                    <th>Size</th>
                    <th>Uploaded</th>
                </tr>
                @foreach ($file_upload->files as $file)
                    <tr>
                        <th><img style="width:100px" src="{{$file->url}}"></th>
                        <td>{{$file->title}}</td>
                        <td>{{$file->size_in_kb}} KB</td>
                        <td>{{$file->uploaded_time}}</td>
                    </tr>
                @endforeach
            </table>
        </form>
    </div>
</div>
