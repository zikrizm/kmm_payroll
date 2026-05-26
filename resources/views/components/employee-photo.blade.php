@props(['photo' => null, 'class' => 'image w-full h-full object-cover'])

@php
    $api = config('constants.api_zkteco');
    $path = $photo ?? '';
    if ($path !== '' && str_contains($path, 'auth_files/photo')) {
        $path = str_replace('auth_files/photo', 'auth_files/biophoto', $path);
    }
    $src = $path !== '' ? $api . $path : $api . '/files/nophoto.gif';
@endphp

@if ($path !== '')
    <img {{ $attributes->merge(['class' => $class, 'alt' => '']) }} src="{{ $src }}">
@else
    <img {{ $attributes->merge(['class' => $class, 'alt' => '']) }} src="{{ $src }}"
        onerror="this.parentElement.style.width='0';">
@endif
