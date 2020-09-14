@if(count($chart->datasets) > 0)
    <div>
        {!! $chart->container() !!}
    </div>

    <script src="{{ asset('/js/Chart.min.js') }}" charset="utf-8"></script>
    {!! $chart->script() !!}
@endif
