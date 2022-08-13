<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ZKTeco</title>
    <style>

    </style>
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.js"
        integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}
    {{--
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" /> --}}
    {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> --}}
    @vite(['resources/css/app.css','resources/js/app.js'])
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @yield('css')
</head>

<body>
    <div class="flex w-full h-screen">
        @include('partials.drawer')
        @yield('content')
    </div>

    <script type="module">
        // $(document).ready(function () {
        //     $('.openModal').on('click', function(e){
        //         $('#interestModal').removeClass('invisible hidden');
        //         $(window).on('click', function(e){
        //             if($('.inner-modal').is($(e.target)))
        //                 $('#interestModal').addClass('invisible hidden');
        //         });
        //     });
        //     $('.closeModal').on('click', function(e){
        //         $('#interestModal').addClass('invisible hidden');
        //     });
            
        // });
        
    </script>
</body>
@stack('script')
<script src="{{ asset('js/helper.js') }}"></script>
@yield('javascript')

</html>