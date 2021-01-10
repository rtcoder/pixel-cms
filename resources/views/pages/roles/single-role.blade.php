@extends('layout.app')
@section('title', __('pages.contacts'))

@section('content')

    @if($role ?? false)
        <h1>@lang('common.edit')</h1>
    @else
        <h1>@lang('common.add')</h1>
    @endif

    <form action="" method="post">
        @csrf

        <label>
            Nazwa:
            <input type="text" name="name"
                   value="{{ old('name', $role->name ?? '') }}">
        </label>

        <div class="modules">
            <div class="label">@lang('permissions.title')</div>
            <table>
                <thead>
                <tr>
                    <th></th>
                    @foreach($actions as $action)
                        <th>
                            <label>
                                <input type="checkbox"
                                       onchange="selectAction(this, '{{ $action }}')"
                                       class="header {{ $action }}">
                                @lang('permissions.' . $action)
                            </label>
                        </th>
                    @endforeach
                    <th>
                        <label>
                            <input type="checkbox" onchange="selectAll(this)" class="selectAll">
                            @lang('common.select_all')
                        </label>
                    </th>
                </tr>
                </thead>
                <tbody>

                @php
                    $permissions = old('permissions', $role->permissions ?? []);
                @endphp

                @foreach($modules as $module)
                    <tr>
                        <td>@lang($modulesNames[$module])</td>
                        @foreach($actions as $action)
                            <td>
                                <label>
                                    <input type="checkbox"
                                           @if(isset($permissions[$module][$action]))
                                           checked
                                           @endif
                                           name="permissions[{{ $module }}][{{ $action }}]"
                                           class="permissions {{ $action }} module-{{ $module }}"
                                           onchange="changePermission('{{ $action }}', {{ $module }})">
                                </label>
                            </td>
                        @endforeach
                        <td>
                            <label>
                                <input type="checkbox"
                                       class="selectAllInModule module-{{ $module }}"
                                       onchange="selectAllPermissionsForModule(this, {{ $module }})">
                                @lang('common.select_all')
                            </label>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <button type="submit">@lang('common.save')</button>
    </form>

@endsection

@section('scripts')
    <script>
        document.querySelectorAll('input.permissions').forEach(input=>{
            changePermission(getActionByClass(input), getModuleByClass(input));
        });

        function selectAllPermissionsForModule(input, module) {
            selectInputs(`input[name^="permissions[${module}]"]`, input.checked);
        }

        function selectAll(input) {
            selectInputs(`.modules input[type="checkbox"].permissions`, input.checked);
        }

        function selectAction(input, action) {
            selectInputs(`.modules input.permissions.${action}`, input.checked);
        }

        function selectInputs(selector, value) {
            document.querySelectorAll(selector).forEach(input => {
                input.checked = value;
                changePermission(getActionByClass(input), getModuleByClass(input));
            });
        }

        function changePermission(action, module) {
            checkChecked(`.modules input.permissions.${action}`, `input.header.${action}`)
            checkChecked(`.modules input.permissions.module-${module}`, `input.selectAllInModule.module-${module}`)
            checkChecked('.modules input.selectAllInModule', 'input.selectAll')
        }

        function checkChecked(testSelector, updateSelector) {
            let checked = true;
            document.querySelectorAll(testSelector).forEach(input => {
                if (!input.checked) {
                    checked = false;
                }
            });
            document.querySelector(updateSelector).checked = checked;
        }

        function getActionByClass(element) {
            const classList = [...element.classList];
            for (const action of [...@json($actions)]) {
                if (classList.includes(action)) {
                    return action;
                }
            }
        }

        function getModuleByClass(element) {
            let moduleId = '';
            [...element.classList].forEach(className => {
                if (className.includes('module')) {
                    const [, module] = className.split('-');
                    moduleId = module;
                }
            });
            return moduleId;
        }
    </script>
@endsection
