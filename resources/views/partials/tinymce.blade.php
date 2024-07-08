<script src="{{ asset('/tinymce/tinymce.min.js') }}"></script>

<iframe id="frameUpload" name="frameUpload" style="display:none"></iframe>
<form id="formUpload" target="frameUpload" method="post"
      enctype="multipart/form-data" style="width:0px;height:0;overflow:hidden">
    <input name="image" type="file" onchange="enviar();">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
</form>

<script>
    function enviar() {
        let data = new FormData();
        data.append('image', document.getElementById('formUpload').image.files[0]);

        axios.post('{{ route('tinymce.upload.image') }}', data).then(function (response) {
            top.$('.tox-browse-url').parent().find('.tox-textfield').val(response.data);
            top.$("button[title|='Save']").click();
        });
    }
</script>
