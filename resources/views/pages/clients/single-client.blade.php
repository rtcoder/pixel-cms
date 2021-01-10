@extends('layout.app')
@section('title', __('pages.contacts'))

@section('content')

    @if($client ?? false)
        <h1>@lang('common.edit')</h1>
    @else
        <h1>@lang('common.add')</h1>
    @endif

    <form action="" method="post">
        @csrf

        <div>
            <label>
                Nazwa:
                <input type="text" name="name"
                       value="{{ old('name', $client->name ?? '') }}">
            </label>
        </div>
        <div>
            <label>
                Slug:
                <input type="text" name="slug"
                       @if($client ?? false)
                       readonly
                       @endif
                       value="{{ old('slug', $client->slug ?? '') }}">
            </label>
        </div>

        <div>
            <label>
                Email:
                <input type="email" name="email"
                       value="{{ old('email', $client->email ?? '') }}">
            </label>
        </div>
        <div>
            <label>
                Numer telefonu:
                <input type="tel" name="phone_number"
                       value="{{ old('phone_number', $client->phone_number ?? '') }}">
            </label>
        </div>
        <div>
            @php
                $locale = old('locale', $client->locale ?? '');
            @endphp
            <label>
                Język systemu:
                <select name="locale">
                    @foreach($locales as $code=>$name)
                        <option
                            value="{{ $code }}"
                            @if($locale == $code)
                            selected
                            @endif
                        >@lang($name)</option>
                    @endforeach
                </select>
            </label>
        </div>
        <div>
            @php
                $availableLocales = old('available_locales', $client->available_locales ?? []);
            @endphp
            Dostępne języki tłumaczeń:
            @foreach($locales as $code=>$name)
                <label>
                    <input
                        type="checkbox"
                        name="available_locales[]"
                        value="{{ $code }}"
                        @if(in_array($code, $availableLocales))
                        checked
                        @endif
                    >
                    @lang($name)
                </label>
            @endforeach
        </div>
        <div class="modules">
            @php
                $modules = old('modules', $client->modules ?? []);
            @endphp
            <div class="label">@lang('permissions.title')</div>

            @foreach($modulesNames as $module => $name)
                <label>
                    <input
                        type="checkbox"
                        name="modules[]"
                        value="{{ $module }}"
                        @if(in_array($module, $modules))
                        checked
                        @endif
                    >
                    @lang($name)
                </label>
            @endforeach
        </div>

        <button type="submit">@lang('common.save')</button>
    </form>

@endsection
