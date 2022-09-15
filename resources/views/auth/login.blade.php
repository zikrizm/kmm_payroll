{{-- @extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address')
                                }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password')
                                }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password"
                                    class="form-control @error('password') is-invalid @enderror" name="password"
                                    required autocomplete="current-password">

                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{
                                        old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    {{ __('Forgot Your Password?') }}
                                </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> --}}

@extends('layouts.app')
@section('content')
    <div class="w-full h-full flex justify-center items-center relative">
        <div class="absolute top-4 right-4">
            <a href="{{ route('business.index.register') }}">
                <button
                    class="flex items-center gap-1 py-1 px-1.5 bg-violet-600 rounded shadow text-xs font-normal text-white">
                    <x-icon icon="file-plus" width=12 height=12 viewBox="20 20" />
                    Register business now
                </button>
            </a>
        </div>
        <div
            class="w-full rounded-xl border-2 relative border-gray-300 bg-white pb-4 overlow-hidden shadow-md  w-full max-w-[385px]">
            <div class="bg-violet-600 w-full text-center py-1 rounded-t-[10px]">
                <p class="text-white text-center text-lg font-semibold">LOGIN</p>
            </div>
            <div class="w-full py-3">
                <form class="submit-login" method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="px-4 flex flex-col gap-2">
                        <div class="flex-1 flex flex-col gap-1">
                            <label class="text-gray-500 font-normal text-sm">Username*</label>
                            <input
                                class="h-10 py-1 px-2 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent"
                                type="text" name="username" placeholder="Username Anda" aria-label="username" required>
                            @error('username')
                                <span class="invalid-feedback text-red-500 text-xs" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="flex-1 flex flex-col gap-1">
                            <label class="text-gray-500 font-normal text-sm">Password*</label>
                            <input
                                class="h-10 py-1 px-2 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 
                            focus:border-transparent"
                                type="password" name="password" placeholder="Password Anda" arial-label="password" required>
                            @error('password')
                                <span class="invalid-feedback text-red-500 text-xs" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="flex items-center gap-2 mt-2.5">
                            <input class="mt-0.5" type="checkbox" name="remember" id="remember"
                                {{ old('remember') ? 'checked' : '' }}>
                            <label class="text-sm text-gray-500" for="remember">
                                {{ __('Remember Me') }}
                            </label>
                        </div>
                    </div>
            </div>
            <div class="px-4">
                <hr class="my-2" style="border: none; border-top: 1px solid rgba(238, 238, 238, 0.8);">
                <div class="mt-4 items-center flex justify-between">
                    <button type="submit"
                        class="text-white shadow-md bg-violet-600 hover:bg-violet-800 focus:ring-4 focus:ring-violet-300 font-medium rounded-lg text-xs inline-flex items-center px-10 py-2.5 text-center ">Login
                    </button>
                    @if (Route::has('password.request'))
                        <a class="inline-block text-xs right-0 align-baseline font-bold text-sm text-red-500 text-white hover:text-red-400"
                            href="{{ route('password.request') }}">Lupa Password?</a>
                </div>
                @endif
            </div>

            </form>
        </div>
    </div>
    {{-- @isset($something_wrong) --}}
    {{-- <p></p> --}}
    <script type="application/javascript">
        // window.addEventListener('DOMContentLoaded', (event) => {
        //     $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    
        //     // $(".search-input").on('keyup', debounce(function() {
        //     //     onInit(null, $(this).val());
        //     // }, 250));
    
        //     // onInit();

        //     var resSubmit = Utils.submit('.submit-login', (data) => { 
        //         console.log('data', data)
        //     });
        // });
    </script>
@endsection
