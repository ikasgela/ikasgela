@extends('layouts.app')

@section('content')

    <div class="row mb-3">
        <div class="col-md">
            <h1>{{ __('Available courses') }}</h1>
        </div>
    </div>

    <h2>{{ $period->name }}</h2>
    @forelse($period->categories as $category)
        <h3>{{ $category->name }}</h3>
        <div class="row">
            @forelse($category->cursos as $curso)
                <div class="col-4">
                    <div class="card mb-3">
                        <img class="card-img-top" src="https://placeholder.pics/svg/320x180/EEEEEE/000000-EEEEEE" alt="Card image cap">
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="#" class="card-link">{{ $curso->nombre }}</a>
                            </h5>
                            <p class="card-text">{{ $curso->descripcion }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p>No hay cursos.</p>
                </div>
            @endforelse
        </div>
    @empty
        <div class="col-12">
            <p>No hay categorías</p>
        </div>
    @endforelse
@endsection
