<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styling -->
    <link href="{{ asset('plugins/Daterangepicker/daterangepicker.css') }}" rel="stylesheet">

    <!-- Scripts -->
    @vite([
        'resources/js/app.js',
        'resources/plugins/Select2/css/select2.min.css',
        'resources/plugins/Select2/js/select2.full.min.js',
        'resources/plugins/Toastr/toastr.css',
        'resources/plugins/Toastr/toastr.js',
        'resources/plugins/JIC/JIC.js',
        // 'resources/js/Remote/ApiService.js',
        'resources/css/app.css',
        'resources/css/custom.css',
    ])

    <script src="{{ asset('plugins/Moment/moment.js') }}" type="text/javascript"></script>
    <script src="{{ asset('plugins/AutoNumeric/autoNumeric.js') }}" type="text/javascript"></script>
    <script src="{{ asset('plugins/Daterangepicker/daterangepicker.js') }}" type="module"></script>
    <script src="{{ asset('js/Remote/networkUtils.js') }}"></script>
    <script src="{{ asset('js/Remote/apiService.js') }}"></script>
    {{-- <script src="{{ asset('js/Helper/helper.js') }}"></script>
    <script src="{{ asset('js/Remote/network_utils.js') }}"></script>
    <script src="{{ asset('js/Remote/api_service.js') }}"></script> --}}
</head>

<body>

    <div id="app">
        {{-- <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                        @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                        @endif

                        @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                        @endif
                        @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav> --}}

        <div class="flex w-full h-screen">
            @include('partials.menus.menu')
            <div class="flex flex-col h-full flex-1 overflow-auto bg-white">
                @include('partials.header')
                <main class="w-full bg-light-bg flex-1">
                    @yield('content')
                </main>
            </div>
        </div>
        <x-modal-form-input-data></x-modal-form-input-data>
    </div>
</body>
@stack('script')

</html>
