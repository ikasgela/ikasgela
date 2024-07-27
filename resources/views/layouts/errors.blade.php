@if(count($errors))
    <div class="alert alert-danger mt-3 mb-0">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success mt-3 mb-0">
        {{ session('success') }}
    </div>
@endif
