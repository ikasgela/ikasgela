<div class="card mb-3">
    <div class="card-header d-flex justify-content-between">
        <div><i class="bi bi-plus-slash-minus me-2"></i>{{ __('Test result') }}</div>
        <div>
            @include('partials.modificar_recursos', ['ruta' => 'test_results'])
            @include('partials.editar_recurso', ['recurso' => $test_result, 'ruta' => 'test_results'])
        </div>
    </div>
    <div class="card-body">
        @if($test_result->completado)
            @include('partials.cabecera_recurso', ['recurso' => $test_result, 'ruta' => 'test_results'])
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header">{{ __('Questions') }}</div>
                        <div class="card-body text-center">
                            <p class="card-text" style="font-size:150%;">{{ $test_result->num_preguntas }}</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-header">{{ __('Right answers') }}</div>
                        <div class="card-body text-center">
                            <p class="card-text" style="font-size:150%;">{{ $test_result->num_correctas }}</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-header">{{ __('Wrong answers') }}</div>
                        <div class="card-body text-center">
                            <p class="card-text" style="font-size:150%;">{{ $test_result->num_incorrectas }}</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-header">{{ __('Result') }}</div>
                        <div class="card-body text-center">
                            <p class="card-text" style="font-size:150%;">{{ $test_result->resultado() }}/100</p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="col">
                <p class="mb-0">{{ __('There are no results yet.') }}</p>
            </div>
        @endif
    </div>
    @if(Auth::user()->hasRole('profesor'))
        <hr class="my-0">
        <div class="card-body">
            {{ html()->modelForm($test_result, 'PUT', route('test_results.rellenar', $test_result->id))->open() }}
            @include('components.label-text', [
                'label' => __('Number of right answers'),
                'name' => 'num_correctas',
            ])
            @include('components.label-text', [
                'label' => __('Number of wrong answers'),
                'name' => 'num_incorrectas',
            ])
            @include('partials.guardar')
            @include('layouts.errors')
            {{ html()->closeModelForm() }}
        </div>
    @endif
</div>
