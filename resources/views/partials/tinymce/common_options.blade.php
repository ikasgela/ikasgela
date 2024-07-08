        license_key: 'gpl',
        language: '{{ LaravelLocalization::getCurrentLocale() }}',
        plugins: "link image autolink emoticons lists hr codesample autosave",
        default_link_target: "_blank",
        toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | codesample | image link emoticons hr',
        link_assume_external_targets: true,
        codesample_global_prismjs: true,
        codesample_languages: [
            @include('partials.tinymce.codesample_languages')
        ],
