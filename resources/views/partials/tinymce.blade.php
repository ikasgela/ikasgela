<script src="{{ asset('/tinymce/tinymce.min.js?apiKey='.config('ikasgela.tinymce_apikey')) }}"></script>

@include('mceImageUpload::upload_form', ['upload_url' => route('tinymce.upload.image')])
