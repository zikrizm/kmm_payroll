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
        <div class="w-full py-5">
            <form class="submit-login" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="px-4 flex flex-col gap-2">
                    <section class="flex flex-col gap-1">
                        <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Username*</label>
                        {!! FormCustom::input('username', null, ['placeholder' => 'Enter new your username','required' => true]) !!}
                    </section>
                    @error('username')
                    <span class="invalid-feedback text-red-500 text-xs" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                    <section class="flex flex-col gap-1">
                        <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Password*</label>
                        {!! FormCustom::input('password', null, ['placeholder' => 'Enter new your password', 'type' => 'password', 'required' => true]) !!}
                    </section>
                    @error('password')
                    <span class="invalid-feedback text-red-500 text-xs" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                    <div class="flex items-center gap-2 mt-2.5">
                        <input class="accent-violet-500" type="checkbox" name="remember" id="remember" {{ old('remember')
                            ? 'checked' : '' }}>
                        <label class="text-sm text-gray-500" for="remember">
                            {{ __('Remember Me') }}
                        </label>
                    </div>
                </div>
        </div>
        <div class="px-4">
            <hr class="mb-2" style="border: none; border-top: 1px solid rgba(238, 238, 238, 0.8);">
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