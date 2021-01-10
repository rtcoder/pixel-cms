@extends('layout.app')
@section('title', __('pages.users'))

@section('content')

    @if($user ?? false)
        <h1>@lang('common.edit')</h1>
    @else
        <h1>@lang('common.add')</h1>
    @endif

    <form action="" method="post">
        @csrf

        <label>
            Nazwa:
            <input type="text" name="name"
                   value="{{ old('name', $user->name ?? '') }}"
                   required>
        </label>
        <label>
            Email:
            <input type="email" name="email"
                   value="{{ old('email', $user->email ?? '') }}"
                   required>
        </label>
        <label>
            Rola:
            @php
                $roleId = old('email', $user->email ?? '');
            @endphp
            <select name="role_id" required>
                @foreach($roles as $role)
                    <option
                        @if($role->id == $roleId)
                        selected
                        @endif
                        value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
            </select>
        </label>
        <label>
            Aktywny:
            @php
                $active = old('is_active', $user->is_active ?? '');
            @endphp
            <select name="is_active" required>
                <option
                    @if($active)
                    selected
                    @endif
                    value="1">@lang('common.yes')</option>
                <option
                    @if($active)
                    selected
                    @endif
                    value="0">@lang('common.no')</option>
            </select>
        </label>

        <button type="submit">@lang('common.save')</button>
    </form>



@endsection
