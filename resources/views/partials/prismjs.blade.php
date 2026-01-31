@section('prismjs-css')
    <link id="prismjs-theme" rel="stylesheet" href="{{ asset('build/prismjs/prism-coy.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('build/prismjs/prism-line-numbers.min.css') }}"/>
@endsection

@section('prismjs-scripts')
    @vite('resources/js/prism.js')
@endsection
