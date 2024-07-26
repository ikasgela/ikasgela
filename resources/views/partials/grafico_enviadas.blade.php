@if(count($chart->datasets) > 0)
    <div class="mb-3">
        {!! $chart->container() !!}
    </div>

    <script src="{{ asset('/js/chart.umd.js') }}" charset="utf-8"></script>
    {!! $chart->script() !!}
@endif
