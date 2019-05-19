<script src="{{ url('/tinymce/tinymce.min.js?apiKey=8krh70d56tqoqk6d2q7vpauy2oiss2rvh5k3fh3hund2ck3x') }}"></script>
<script>
    tinymce.init({
        selector: 'textarea#message',
        language: 'es_ES',
        plugins: "link image autolink emoticons lists hr",
        default_link_target: "_blank",
        toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | image link emoticons hr',
        link_assume_external_targets: true
    });
</script>
