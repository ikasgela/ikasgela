@include('partials.tinymce')

<script>
    tinymce.init({
        selector: 'textarea#message',
        license_key: 'gpl',
        promotion: false,
        language: '{{ LaravelLocalization::getCurrentLocale() }}',
        plugins: "link image autolink emoticons lists hr codesample autosave",
        default_link_target: "_blank",
        toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | codesample | image link emoticons hr',
        link_assume_external_targets: true,
        codesample_global_prismjs: true,
        codesample_languages: [
            @include('partials.tinymce.codesample_languages')
        ],
        autosave_ask_before_unload: false,
        autosave_interval: "15s",
        autosave_restore_when_empty: true,
        autosave_retention: "60m",
        relative_urls: false,
        file_picker_callback: function (callback, value, meta) {
            if (meta.filetype === 'image') {
                $('#formUpload input').click();
            }
        }
    });
</script>
