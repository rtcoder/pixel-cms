@extends('layout.app')
@section('title', 'Zaloguj się')


@section('styles')
    <style>
        body {
            width: 100%;
            height: 100vh;
        }

        .content {
            width: 100%;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        form {
            width: 400px;
            max-width: 100%;
            height: 300px;
            border: 1px solid #ccc;
            padding: 5px;
            display: flex;
            justify-content: space-between;
            flex-direction: column;
            align-items: center;
        }
    </style>
@endsection


@section('content')
    <form class="form" method="post" action="{{ route('login') }}">
        @csrf

        <h1>Logowanie</h1>

        <label>
            <span class="label">Email:</span>
            <input type="email" name="email" value="{{$oldLogin ?? ''}}" required>
        </label>
        <label>
            <span class="label">Hasło:</span>
            <input type="password" name="password" value="{{$oldPassword ?? ''}}" required>
        </label>

        <button type="submit" class="primary">Zaloguj się</button>

    </form>
@endsection
