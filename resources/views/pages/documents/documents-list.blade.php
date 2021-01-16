@extends('layout.app')
@section('title', __('pages.documents'))

@section('content')

    @include('layout.table.table-top', ['title' => 'pages.documents'])
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
        @foreach($documents as $document)
            <tr>
                <td>{{ $document->id }}</td>
                <td>{{ $document->name }}</td>
                <td>{{ $document->created_at }}</td>
                <td>
                    @include('layout.table.table-row-options', ['row' => $document])
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    @if(!count($documents))
        @include('layout.table.no-data')
    @endif

@endsection
