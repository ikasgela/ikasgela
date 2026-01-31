<script src="{{ asset('build/tinymce/tinymce.min.js') }}"></script>
<script>
    window.tinymce_base_url = '{{ asset('build/tinymce') }}';
</script>

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
            const browseUrl = top.document.querySelector('.tox-browse-url');
            if (browseUrl) {
                const textfield = browseUrl.parentElement.querySelector('.tox-textfield');
                if (textfield) {
                    textfield.value = response.data;
                }
            }
            const saveBtn = top.document.querySelector("button[title='Save']");
            if (saveBtn) {
                saveBtn.click();
            }
        });
    }
</script>
