@extends('layout.app')
@section('title', __('pages.documents'))

@section('content')

    @if($document ?? false)
        <h1>@lang('common.edit')</h1>
    @else
        <h1>@lang('common.add')</h1>
    @endif

    <form action="" method="post">
        @csrf

        <label>
            Nazwa:
            <input type="text" name="name"
                   value="{{ old('name', $document->name ?? '') }}">
        </label>


        @include('layout.ckeditor')


        <button type="submit">@lang('common.save')</button>
    </form>

@endsection
