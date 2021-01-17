<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @auth
        <meta name="api-token" content="{{ auth()->user()->api_token }}">
    @endauth
    @yield('meta')

    <title>@yield('title')</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('css/app.css') }}">
    <script src="{{ url('js/app.js') }}"></script>

    @yield('styles')
</head>
<body>

@auth
    <header>
        <div class="logo">
            <a href="/">
                <img src="{{ url('img/logo.png') }}" alt="">
                Pixel Admin
            </a>
        </div>

        <div class="links">
            <div class="dropdown">
                <button class="drop-btn">
                    <span class="material-icons">account_circle</span>
                    {{auth()->user()->name}}
                </button>
                <div class="dropdown-content">
                    <a href="{{ route('logout') }}">
                        @lang('common.logout')
                        <span class="material-icons">logout</span>
                    </a>
                </div>
            </div>
        </div>
    </header>
@endauth

<div class="wrapper">
    @auth
        @include('layout.sidebar')
    @endauth
    <div class="content">
        @include('vendor.flash.message')
        @include('layout.errors')
        @yield('content')
    </div>
</div>

<footer>
</footer>

@yield('scripts')
</body>
</html>
