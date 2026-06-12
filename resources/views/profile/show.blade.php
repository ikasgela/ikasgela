@extends('layouts.app')

@section('content')
    @include('partials.titular', ['titular' => __('Profile'), 'subtitulo' => ''])

    @include('partials.tutorial', [
        'color' => 'success',
        'texto' => trans('tutorial.perfil')
    ])

    <div class="card mb-3">
        <div class="card-body">
            {{ html()->modelForm($user, 'PUT', route('profile.update.user', $user->id))->open() }}

            @include('components.label-text', [
                'label' => __('Name'),
                'name' => 'name',
            ])

            @include('components.label-text', [
                'label' => __('Surname'),
                'name' => 'surname',
            ])

            @include('components.label-text-readonly', [
                'label' => __('Email'),
                'name' => 'email',
            ])

            <div class="row mb-3">
                <div class="col-sm-2">
                    {{ html()->label(__('Avatar'), 'avatar')->class('form-label') }}
                </div>
                <div class="col-sm-10">
                    <div class="mb-3">
                        @include('users.partials.avatar', ['user' => $user, 'width' => 100])
                    </div>
                    {!! __('You can manage your profile picture on <a href="https://gravatar.com" target="_blank">Gravatar</a>.') !!}
                </div>
            </div>

            @include('components.label-text', [
                'label' => __('Email for Gravatar'),
                'name' => 'gravatar_email',
            ])

            @include('partials.guardar')

            @include('layouts.errors')

            {{ html()->closeModelForm() }}
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5>{{ __('API Access Token') }}</h5>
            <p class="text-muted">{{ __('Generate a token to access the application via API. The token will be shown only once.') }}</p>

            <div class="mb-3">
                <button type="button" id="generateTokenBtn" class="btn btn-primary">
                    {{ __('Generate Token') }}
                </button>
            </div>

            <div id="tokenResult" class="mt-3" style="display: none;">
                <label for="generatedToken" class="form-label">{{ __('Your Token (shown only once)') }}</label>
                <div class="input-group">
                    <input type="text" id="generatedToken" class="form-control" readonly>
                    <button type="button" class="btn btn-outline-secondary" id="copyTokenBtn">
                        {{ __('Copy') }}
                    </button>
                </div>
                <small class="text-danger">{{ __('This token will not be shown again. Store it securely.') }}</small>
            </div>

            <script>
                document.getElementById('generateTokenBtn').addEventListener('click', function() {
                    const btn = this;
                    btn.disabled = true;
                    btn.textContent = '{{ __('Generating...') }}';

                    fetch('{{ route('profile.generate.token') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('generatedToken').value = data.token;
                        document.getElementById('tokenResult').style.display = 'block';
                        btn.style.display = 'none';
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('{{ __('Failed to generate token. Please try again.') }}');
                        btn.disabled = false;
                        btn.textContent = '{{ __('Generate Token') }}';
                    });
                });

                document.getElementById('copyTokenBtn').addEventListener('click', function() {
                    const tokenInput = document.getElementById('generatedToken');
                    tokenInput.select();
                    document.execCommand('copy');

                    const originalText = this.textContent;
                    this.textContent = '{{ __('Copied!') }}';
                    setTimeout(() => {
                        this.textContent = originalText;
                    }, 2000);
                });
            </script>

    @if($cursos_finalizados > 0 || Auth::user()->hasAnyRole(['admin']))
        @livewire('exportar-usuario', ['user' => $user])
    @endif

    @if(Auth::user()->hasAnyRole(['admin']))
        @include('partials.subtitulo', ['subtitulo' => __('Tests')])
        <p>IP: {{ $clientIP }} | Egibide: {{ $ip_egibide ? 'Sí' : 'No' }}</p>
        <p>Host: {{ env("HOSTNAME") }}</p>
        <p>Token válido: {{ $user->curso_actual()?->token_valido() ? "Sí" : "No" }}</p>
    @endif
@endsection
