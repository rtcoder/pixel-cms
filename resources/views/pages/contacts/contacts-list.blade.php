@extends('layout.app')
@section('title', __('pages.contacts'))

@section('content')
    @include('layout.search-form')

    <h1>@lang('pages.contacts')</h1>
    <table>
        <thead>
        <tr>
            <th class="id-column">ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Dodany</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($contacts as $contact)
            <tr>
                <td>ID</td>
                <td>Name</td>
                <td>Email</td>
                <td>{{ $contact->created_at }}</td>
                <td></td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
