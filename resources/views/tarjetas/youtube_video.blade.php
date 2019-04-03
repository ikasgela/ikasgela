<div class="card">
    <div class="card-header"><i class="fas fa-video"></i> {{ __('Video') }}</div>
    <div class="card-body">
        <h5 class="card-title">{{ $youtube_video->titulo }}</h5>
        <p class="card-text">{{ $youtube_video->descripcion }}</p>
        <div class="p-1 mb-1" style="background-color:#eee;">
            <div class="embed-responsive embed-responsive-16by9">
                <iframe class="embed-responsive-item"
                        src="https://www.youtube.com/embed/{{ $youtube_video->codigo }}?rel=0&modestbranding=1"
                        allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen></iframe>
            </div>
        </div>
        {{--
                <form>
                    <div class="card-text mt-3">
                        <button type="submit" class="btn btn-primary">Marcar como visto</button>
                    </div>
                </form>
        --}}
    </div>
</div>
