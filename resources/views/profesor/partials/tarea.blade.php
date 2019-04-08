@if(session('tutorial'))
    <div class="callout callout-success b-t-1 b-r-1 b-b-1">
        <small class="text-muted">{{ __('Tutorial') }}</small>
        <p>Aquí puedes valorar la actividad y dar el feedback oportuno.</p>
    </div>
@endif
@include('profesor.partials.tarjeta_usuario')
<div class="row mt-4">
    <form class="col-md-12 p-0 form-inline"
          method="POST"
          action="{{ route('actividades.estado', [$tarea->id]) }}">
        @csrf
        @method('PUT')

        <div class="col-md-12">
            {{-- Tarjeta --}}
            <div class="card border-dark">
                <div class="card-header text-white bg-dark d-flex justify-content-between">
                    <span>{{ $actividad->unidad->curso->nombre }} » {{ $actividad->unidad->nombre }}</span>
                </div>
                <div class="card-body">
                    <h2>{{ $actividad->nombre }}</h2>
                    <p>{{ $actividad->descripcion }}</p>
                    <div>
                        <button type="submit" name="nuevoestado" value="40"
                                class="btn btn-success"> {{ __('Ok') }}
                        </button>
                        <button type="submit" name="nuevoestado" value="41"
                                class="btn btn-danger"> {{ __('Error') }}
                        </button>
                    </div>
                </div>
            </div>
            {{-- Fin tarjeta--}}
        </div>
        @if($tarea->estado > 10)
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-bullhorn"></i>
                        <label class="m-0" for="feedback">{{ __('Feedback') }}</label>
                    </div>
                    <div class="card-body">
                                <textarea class="form-control"
                                          id="feedback"
                                          name="feedback"
                                          rows="3">{{ $tarea->feedback }}</textarea>
                    </div>
                </div>
            </div>
            @foreach($actividad->youtube_videos()->get() as $youtube_video)
                <div class="col-md-6">
                    @include('tarjetas.youtube_video')
                </div>
            @endforeach
            @foreach($actividad->intellij_projects()->get() as $intellij_project)
                <div class="col-md-6">
                    @php($repositorio = $intellij_project->gitlab())
                    @include('tarjetas.intellij_project')
                </div>
            @endforeach
        @endif
    </form>
</div>
