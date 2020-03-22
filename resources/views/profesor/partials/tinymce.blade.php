@include('partials.prismjs')

<script src="{{ url('/tinymce/tinymce.min.js?apiKey=8krh70d56tqoqk6d2q7vpauy2oiss2rvh5k3fh3hund2ck3x') }}"></script>
<script>
    tinymce.init({
        selector: 'textarea#feedback',
        language: 'es_ES',
        plugins: "link image autolink emoticons lists hr codesample",
        default_link_target: "_blank",
        toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | codesample | image link emoticons hr',
        link_assume_external_targets: true,
        codesample_global_prismjs: true,
        codesample_languages: [
            {text: 'Java', value: 'java'},
            {text: 'Swift', value: 'swift'},
            {text: 'Python', value: 'python'},
        ],
        setup: function (editor) {
            editor.on('init', function () {
                editor.focus();
                editor.selection.select(editor.getBody(), true);
                editor.selection.collapse(false);
            });
        }
    });

    function validate_feedback() {
        if ((tinymce.EditorManager.get('feedback').getContent()) === '') {
            alert('{{ __('Feedback cannot be empty.') }}');
            return false;
        }
    }
</script>
