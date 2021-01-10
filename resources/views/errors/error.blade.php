@extends('layout.app')

@section('styles')
    <style>
        .error {
            letter-spacing: .05em;
            text-transform: uppercase;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            padding: 30px 0;
            font-size: 1.5rem;
            height: calc(100vh - var(--headerHeight));
        }

        .code {
            padding-left: 1rem;
            padding-right: 1rem;
            border-right: 1px solid var(--fontColor);
        }

        .message {
            margin-left: 1rem
        }
    </style>
@endsection

@section('content')
    <div class="error">
        <div class="code">@yield('code')</div>
        <div class="message">@yield('message')</div>
    </div>
@endsection
