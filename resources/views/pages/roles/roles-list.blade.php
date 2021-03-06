@extends('layout.app')
@section('title', __('pages.contacts'))

@section('content')
    @include('layout.table.table-top', ['title' => 'pages.roles'])
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
                    @if(!$role->is_admin && !$role->is_super_admin)
                        @include('layout.table.table-row-options', ['row' => $role])
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    @if(!count($roles))
        @include('layout.table.no-data')
    @endif

@endsection
