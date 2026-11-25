<form wire:submit="save">

    {{ html()->hidden('curso_id')->value($curso_id)->attribute('wire:model', 'curso_id') }}

    <div class="row mb-3">
        <div class="col-sm-2">
            {{ html()->label(__('Name'), 'name')->class('col-form-label') }}
        </div>
        <div class="col-sm-10">
            {{ html()->text('name', $name)->class('form-control')->attribute('wire:model', 'name') }}
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-sm-2">
            {{ html()->label(__('Start date'), 'start_date')->class('col-form-label') }}
        </div>
        <div class="col-sm-10">
            {{ html()->datetime('start_date', $start_date)->class('form-control')->attribute('wire:model', 'start_date') }}
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-sm-2">
            {{ html()->label(__('Date'), 'date')->class('col-form-label') }}
        </div>
        <div class="col-sm-10">
            {{ html()->datetime('date', $date)->class('form-control')->attribute('wire:model', 'date') }}
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-sm-2">
            {{ html()->label(__('Published'), 'published')->class('form-check-label') }}
        </div>
        <div class="col-sm-10">
            {{ html()->checkbox('published')->class('form-check-input')->attribute('wire:model', 'published') }}
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-sm-2">
            {{ html()->label(__('Decimals'), 'decimals')->class('col-form-label') }}
        </div>
        <div class="col-sm-10">
            {{ html()->text('decimals', $decimals)->class('form-control')->attribute('wire:model', 'decimals') }}
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-sm-2">
            {{ html()->label(__('Truncate'), 'truncate')->class('form-check-label') }}
        </div>
        <div class="col-sm-10">
            {{ html()->checkbox('truncate')->class('form-check-input')->attribute('wire:model', 'truncate') }}
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-sm-2">
            {{ html()->label(__('Normalize calification'), 'normalizar_nota')->class('form-check-label') }}
        </div>
        <div class="col-sm-10">
            {{ html()->checkbox('normalizar_nota')->class('form-check-input')->attribute('wire:model', 'normalizar_nota') }}
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-sm-2">
            {{ html()->label(__('Proportional calification adjustment'), 'ajuste_proporcional_nota')->class('col-form-label') }}
        </div>
        <div class="col-sm-10">
            {{ html()->select('ajuste_proporcional_nota')->class('form-select')->attribute('wire:model.live', 'ajuste_proporcional_nota')->open() }}
            {{ html()->option(__('--- None --- '), '', $ajuste_proporcional_nota === null || $ajuste_proporcional_nota === '') }}
            {{ html()->option(__('Average'), 'media', $ajuste_proporcional_nota === 'media') }}
            {{ html()->option(__('Median'), 'mediana', $ajuste_proporcional_nota === 'mediana') }}
            {{ html()->option(__('Fixed'), 'fijo', $ajuste_proporcional_nota === 'fijo') }}
            {{ html()->select()->close() }}
        </div>
    </div>

    @if($ajuste_proporcional_nota === 'fijo')
        <div class="row mb-3">
            <div class="col-sm-2">
                {{ html()->label(__('Number of mandatory activities'), 'num_actividades_obligatorias')->class('col-form-label') }}
            </div>
            <div class="col-sm-10">
                {{ html()->text('num_actividades_obligatorias', $num_actividades_obligatorias)->class('form-control')->attribute('wire:model', 'num_actividades_obligatorias') }}
            </div>
        </div>
    @endif

    <div class="row mb-3">
        <div class="col-sm-2">
            {{ html()->label(__('Qualification (partial)'), 'qualification_id')->class('col-form-label') }}
        </div>
        <div class="col-sm-10">
            {{ html()->select('qualification_id')->class('form-select')->attribute('wire:model', 'qualification_id')->open() }}
            {{ html()->option(__('--- Course qualification (default) ---'), '', $qualification_id === null || $qualification_id === '') }}
            @foreach($qualifications as $qualification)
                {{ html()->option($qualification->name, $qualification->id, (string) $qualification_id === (string) $qualification->id) }}
            @endforeach
            {{ html()->select()->close() }}
            <div class="form-text">{{ __('If set, the grade is computed over this qualification instead of the course one (partial evaluation: only the skills it contains are weighted).') }}</div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-sm-2">
            {{ html()->label(__('Mandatory activities tags'), 'etiquetas')->class('col-form-label') }}
        </div>
        <div class="col-sm-10">
            {{ html()->text('etiquetas', $etiquetas)->placeholder('base')->class('form-control')->attribute('wire:model', 'etiquetas') }}
            <div class="form-text">{{ __('Comma separated. Used to count the completed mandatory activities of the students in this milestone.') }}</div>
        </div>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary">
            <span class="spinner-border spinner-border-sm" wire:loading wire:target="save" style="display:none;"></span>
            {{ __('Save') }}
        </button>
        <a href="{!! anterior() !!}" class="btn btn-link text-secondary">{{ __('Cancel') }}</a>
    </div>

    @include('layouts.errors')

</form>
