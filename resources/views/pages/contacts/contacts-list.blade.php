@extends('layout.app')
@section('title', __('pages.contacts'))

@section('content')
    @include('layout.table.table-top', ['title' => 'pages.contacts'])
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
                <td>{{ $contact->id }}</td>
                <td>{{ $contact->full_name }}</td>
                <td>{{ $contact->emailAddresses[0]->value ?? '' }}</td>
                <td>{{ $contact->created_at }}</td>
                <td>
                    @include('layout.table.table-row-options', ['row' => $contact])
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    @if(!count($contacts))
        @include('layout.table.no-data')
    @endif

@endsection
