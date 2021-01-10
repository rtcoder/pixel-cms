@extends('layout.app')
@section('title', __('pages.contacts'))

@section('content')
    @include('layout.search-form')

    @include('layout.table.table-title', ['title' => 'pages.roles'])
    <table>
        <thead>
        <tr>
            <th class="id-column">ID</th>
            <th>Name</th>
            <th>Dodany</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($roles as $role)
            <tr>
                <td>{{ $role->id }}</td>
                <td>{{ $role->name }}</td>
                <td>{{ $role->created_at }}</td>
                <td>
                    @include('layout.table.table-row-options', ['row' => $role, 'canDelete' => !$role->is_admin && !$role->is_super_admin])
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    @if(!count($roles))
        @include('layout.table.no-data')
    @endif

@endsection
