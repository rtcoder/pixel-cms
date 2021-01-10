@extends('layout.app')
@section('title', __('pages.contacts'))

@section('content')
    @include('layout.search-form')

    @include('layout.table.table-title', ['title' => 'pages.clients'])
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
        @foreach($clients as $client)
            <tr>
                <td>{{ $client->id }}</td>
                <td>{{ $client->name }}</td>
                <td>{{ $client->created_at }}</td>
                <td>
                    @include('layout.table.table-row-options', ['row' => $client])
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    @if(!count($clients))
        @include('layout.table.no-data')
    @endif

@endsection
