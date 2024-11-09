<div class="d-flex flex-row justify-content-between align-items-center">
    {{ html()->label(__('Organization'), 'organization_id')->class('form-label m-0') }}
    <div class="flex-fill mx-3">
        {{ html()->select('organization_id')->class('form-select')->open() }}
        {{ html()->option(__('--- None --- '), -1) }}
        @foreach($organizations as $organization)
            {{ html()->option($organization->full_name,
                                $organization->id,
                                session('filtrar_organization_actual') == $organization->id) }}
        @endforeach
        {{ html()->select()->close() }}
    </div>
    <div>
        {{ html()->submit(__('Filter'))->class('btn btn-primary') }}
    </div>
</div>
