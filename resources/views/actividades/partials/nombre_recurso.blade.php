@switch($recurso::class)
    @case('App\Models\IntellijProject')
        {{ __('IntelliJ project') }}
        @break
    @case('App\Models\MarkdownText')
        {{ __('Markdown text') }}
        @break
    @case('App\Models\YoutubeVideo')
        {{ __('YouTube video') }}
        @break
    @case('App\Models\FileUpload')
        {{ __('Image upload') }}
        @break
    @case('App\Models\FileResource')
        {{ __('Files') }}
        @break
    @case('App\Models\Cuestionario')
        {{ __('Questionnaire') }}
        @break
    @case('App\Models\LinkCollection')
        {{ __('Link collection') }}
        @break
    @case('App\Models\Selector')
        {{ __('Selector') }}
        @break
    @default
        {{ __('Unknown') }}
@endswitch
