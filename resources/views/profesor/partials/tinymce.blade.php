@include('partials.prismjs')

@include('partials.tinymce')

<script>
    tinymce.init({
        selector: 'textarea#feedback',
        license_key: 'gpl',
        promotion: false,
        language: '{{ LaravelLocalization::getCurrentLocale() }}',
        plugins: "link image autolink emoticons lists hr codesample",
        default_link_target: "_blank",
        toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | codesample | image link emoticons hr',
        link_assume_external_targets: true,
        codesample_global_prismjs: true,
        codesample_languages: [
            @include('partials.tinymce.codesample_languages')
        ],
        relative_urls: false,
        setup: function (editor) {
            editor.on('init', function () {
                editor.focus();
                editor.selection.select(editor.getBody(), true);
                editor.selection.collapse(false);
            });
        },
        file_picker_callback: function (callback, value, meta) {
            if (meta.filetype === 'image') {
                $('#formUpload input').click();
            }
        }
    });

    function validate_feedback() {
        if ((tinymce.EditorManager.get('feedback').getContent()) === '') {
            alert('{{ __('Feedback cannot be empty.') }}');
            return false;
        }
    }
</script>
