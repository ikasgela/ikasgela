@include('partials.tinymce')

<script>
    const tinymce_config = {
        selector: 'textarea#mensaje',
        license_key: 'gpl',
        promotion: false,
        base_url: window.tinymce_base_url,
        language: '{{ LaravelLocalization::getCurrentLocale() }}',
        plugins: "link image autolink emoticons lists hr codesample autosave",
        default_link_target: "_blank",
        toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | codesample | image link emoticons hr',
        link_assume_external_targets: true,
        codesample_global_prismjs: true,
        codesample_languages: [
            @include('partials.tinymce.codesample_languages')
        ],
        skin: (typeof getEffectiveTheme === 'function' ? getEffectiveTheme() : 'light') === 'dark'
            ? "oxide-dark"
            : "oxide",
        content_css: (typeof getEffectiveTheme === 'function' ? getEffectiveTheme() : 'light') === 'dark'
            ? "dark"
            : "default",
        autosave_ask_before_unload: false,
        autosave_interval: "15s",
        autosave_restore_when_empty: true,
        autosave_retention: "60m",
        relative_urls: false,
        file_picker_callback: function (callback, value, meta) {
            if (meta.filetype === 'image') {
                document.querySelector('#formUpload input').click();
            }
        }
    };

    tinymce.init(tinymce_config);

    // Limpiar el autosave de TinyMCE al enviar el formulario
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('form');
        forms.forEach(function(form) {
            form.addEventListener('submit', function() {
                if (tinymce.activeEditor && tinymce.activeEditor.plugins.autosave) {
                    tinymce.activeEditor.plugins.autosave.removeDraft();
                }
                // Limpiar también el localStorage directamente por si acaso
                const editorId = tinymce_config.selector.replace('textarea#', '');
                const keys = Object.keys(localStorage);
                keys.forEach(function(key) {
                    if (key.includes('tinymce-autosave') && key.includes(editorId)) {
                        localStorage.removeItem(key);
                    }
                });
            });
        });
    });
</script>
