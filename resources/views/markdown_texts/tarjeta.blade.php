<div class="card">
    <div class="card-header"><i class="fab fa-markdown"></i> {{ $markdown_text->titulo }}</div>
    <div class="card-body pb-1 line-numbers">
        {!! $texto !!}
    </div>
</div>

@section('prismjs-css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.17.1/themes/prism-coy.min.css"
          integrity="sha256-1VOeW3bLzHmS11IR0Cl73suNyhliqnse4BTuwGs4IUg=" crossorigin="anonymous"/>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.17.1/plugins/line-numbers/prism-line-numbers.min.css"
          integrity="sha256-Afz2ZJtXw+OuaPX10lZHY7fN1+FuTE/KdCs+j7WZTGc=" crossorigin="anonymous"/>
@endsection

@section('prismjs-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.17.1/components/prism-core.min.js"
            integrity="sha256-Y+Budm2wBEjYjbH0qcJRmLuRBFpXd0VKxl6XhdS4hgA=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.17.1/plugins/line-numbers/prism-line-numbers.min.js"
            integrity="sha256-hep5s8952MqR7Y79JYfCXZD6vQjVHs7sOu/ZGrs1OEQ=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.17.1/plugins/autoloader/prism-autoloader.min.js"
            integrity="sha256-ht8ay6ZTPZfuixYB99I5oRpCLsCq7Do2LjEYLwbe+X8=" crossorigin="anonymous"></script>
@endsection
