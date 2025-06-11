<div>
    @include('partials.subtitulo', ['subtitulo' => __('Your data')])
    <div class="card mb-3">
        <div class="card-body pb-0">
            <div class="row mb-3">
                <div class="col-sm-2 d-flex align-items-top">
                    {{ html()->label(__('Data export'), 'intellij_projects')->class('form-label mt-2') }}
                </div>
                <div class="col-sm-10">
                    <button wire:click="export"
                            title="{{ __('Request an export') }}"
                            class="btn single_click {{ !$already_exported && !$exporting ? 'btn-primary' : 'btn-secondary disabled' }}">
                        <i class="fas fa-spinner fa-spin" style="display:none;"></i>
                        {{ __('Request an export') }}
                    </button>
                    @if($already_exported)
                        <p class="small m-0 mt-2">{{ __('Only one export per 24 hours is allowed.') }}
                            <a href="{{ $url }}">{{ __('Click here to download current export.') }}</a>
                        </p>
                    @elseif(!$exporting)
                        <p class="small m-0 mt-2">{{ __('Click on the button to request a data export.') }}</p>
                    @else
                        <p class="small m-0 mt-2">{{ __('Export in progress. You will receive an email when the download is ready.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
