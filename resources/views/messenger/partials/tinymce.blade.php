<script src="{{ url('/tinymce/tinymce.min.js?apiKey=8krh70d56tqoqk6d2q7vpauy2oiss2rvh5k3fh3hund2ck3x') }}"></script>
<script>
    tinymce.init({
        selector: 'textarea#message',
        language: 'es_ES',
        plugins: "link image autolink",
        default_link_target: "_blank",
        link_assume_external_targets: true
    });
</script>
