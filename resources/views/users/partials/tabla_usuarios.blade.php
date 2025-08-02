<div class="table-responsive">
    <table class="table table-hover">
        <thead class="thead-dark">
        <tr>
            <th>
                <input type="checkbox" id="seleccionar_usuarios">
            </th>
            <th>#</th>
            <th></th>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Email') }}</th>
            <th class="text-center">{{ __('Courses') }}</th>
            <th class="text-center">{{ __('Verified') }}</th>
            <th class="text-center">{{ trans('tutorial.titular') }}</th>
            <th>{{ __('Roles') }}</th>
            <th>{{ __('Actions') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td>
                    <input form="asignar" type="checkbox"
                           data-chkbox-shiftsel="grupo1"
                           name="usuarios_seleccionados[{{ $user->id }}]" value="{{ $user->id }}">
                </td>
                <td>{{ $user->id }}</td>
                <td>@include('users.partials.avatar', ['user' => $user, 'width' => 35])</td>
                <td>
                    {{ $user->full_name }}
                    @include('profesor.partials.status_usuario')
                    @include('profesor.partials.etiquetas_usuario_filtro')
                    @include('profesor.partials.baja_ansiedad_usuario')
                </td>
                <td>@include('partials.mailto', ['user' => $user])</td>
                <td class="text-center">{{ $user->cursos()->count() }}</td>
                <td class="text-center">@include('partials.check_yes_no', ['checked' => $user->hasVerifiedEmail()])</td>
                <td class="text-center">@include('partials.check_yes_no', ['checked' => $user->tutorial])</td>
                <td>
                    @foreach($user->roles as $rol)
                        {{ !$loop->last ? $rol->name . ', ' : $rol->name }}
                    @endforeach
                </td>
                <td>
                    @include('users.partials.acciones')
                </td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr class="bg-dark">
            <th colspan="3">{{ __('Total') }}: {{ $users->count() }}</th>
            <th colspan="100"></th>
        </tr>
        @if(count($etiquetas) > 0)
            <tr>
                <td colspan="100">
                    {{ __('Filter by tag') }}:
                    @foreach($etiquetas as $etiqueta)
                        <span class="ms-2">
                        {{ html()
                            ->a(route(explode('.', Route::currentRouteName())[0] . '.index.filtro', ['tag_usuario' => $etiqueta]), $etiqueta)
                            ->class('badge bg-body-secondary text-body-secondary') }}
                        </span>
                    @endforeach
                </td>
            </tr>
        @endif
        </tfoot>
    </table>
</div>

{{ html()->form('POST', route('users.acciones_grupo'))->id('asignar')->open() }}
<div class="row row-cols-lg-auto g-3 align-items-center mb-3">
    <div class="col-12">
        <button title="{{ __('Enroll') }}"
                type="submit"
                name="action" value="enroll"
                class="btn btn-light btn-sm"><i class="bi bi-plus-lg"></i>
        </button>
    </div>
    <div class="col-12">
        <span>{{ __('on course') }}</span>
    </div>
    <div class="col-12">
        <select class="form-select" id="curso_id" name="curso_id">
            @foreach($cursos as $curso)
                <option value="{{ $curso->id }}">{{ $curso->full_name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <div class="btn-group">
            <button title="{{ __('Manual verification') }}"
                    type="submit"
                    name="action" value="verify"
                    class="btn btn-light btn-sm"><i class="bi bi-person-check"></i>
            </button>
            <button title="{{ __('Block') }}"
                    type="submit"
                    name="action" value="block"
                    class="btn btn-light btn-sm"><i class="bi bi-lock"></i>
            </button>
            <button title="{{ __('Unblock') }}"
                    type="submit"
                    name="action" value="unblock"
                    class="btn btn-light btn-sm"><i class="bi bi-unlock2"></i>
            </button>
            <button title="{{ __('Delete') }}"
                    type="submit" onclick="return confirm('{{ __('Are you sure?') }}')"
                    name="action" value="delete"
                    class="btn btn-light btn-sm"><i class="fas fa-trash text-danger"></i>
            </button>
        </div>
    </div>
    <div class="col-12">
        <input type="text" class="form-control" size="30" name="tags" id="tags"
               placeholder="{{ __('Tags, separated by commas') }}"/>
    </div>
    <div class="col-12">
        <button title="{{ __('Add tags') }}"
                type="submit"
                name="action" value="tag"
                class="btn btn-light btn-sm"><i class="fas fa-tag"></i>
        </button>
    </div>
</div>
{{ html()->form()->close() }}
