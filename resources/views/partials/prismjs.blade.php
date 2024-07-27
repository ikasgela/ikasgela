@section('prismjs-css')
    <link id="prismjs-theme" rel="stylesheet" href="{{ asset('/prismjs/prism-coy.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('/prismjs/prism-line-numbers.min.css') }}"/>
@endsection

@section('prismjs-scripts')
    <script src="{{ asset('/prismjs/prism.js') }}"></script>
@endsection
