@extends('layout.app')
@section('title', __('pages.users'))

@section('content')
    @include('layout.search-form')

    <h1>@lang('pages.users')</h1>
    <table>
        <thead>
        <tr>
            <th class="id-column">ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Rola</th>
            <th>Dodany</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role->name }}</td>
                <td>{{ $user->created_at }}</td>
                <td>
                    @include('layout.table.table-row-options', ['row' => $user, 'canDelete' => $user->id !== auth()->id()])
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
