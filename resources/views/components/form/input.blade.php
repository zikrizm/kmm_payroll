@php
$padding_class = 'px-2.5';
$is_prefix = 'hidden';
$is_suflix = 'hidden';
if (!empty($atr->prefixiconname) || !empty($atr->prefixtext)) {
$padding_class = 'pl-12 pr-2.5';
$is_prefix = '';
}
if (!empty($atr->suffixiconname) || !empty($atr->suffixtext)) {
$padding_class = 'pr-8 pl-2.5';
$is_suflix = '';
}
@endphp
<div class="flex flex-col gap-1">
    <div class="w-full h-9 flex items-center relative">
        <span
            class="{{ $is_prefix }} absolute left-0 h-full w-9 flex justify-center items-center text-center border-r border-gray-300 text-gray-500 text-sm">
            {{ $atr->prefixtext }}
            <div class="{{ $atr->prefixiconname ? '' : 'hidden' }}">
                <x-icon icon="{{ $atr->prefixiconname }}" width=16 height=16 viewBox="20 20" />
            </div>
        </span>

        <input name="{{ $name }}" value="{{ $value }}" type="{{ $atr->type }}" placeholder="{{ $atr->placeholder }}"
            class="{{ $padding_class }}  w-full h-full text-sm rounded-lg shadow-sm border border-gray-300 focus:outline-none focus:ring-0
             {{ $atr->class }} {{ $atr->block_input ? 'cursor-not-allowed text-gray-300': 'focus:shadow-xs/focused(4px-primary) focus:border-violet-300' }}"
            {{ $atr->required ? 'required' : '' }} {{ $atr->disabled ? 'disabled' : '' }} {{
        ($atr->readonly||$atr->block_input) ? 'readonly'
        : '' }}
        autocomplete="{{ $atr->autocomplete }}" />

        <span class="absolute right-0 px-2.5 {{ $is_suflix }}">
            {{ $atr->suffixtext }}
            <x-icon icon="{{ $atr->suffixiconname }}" width=16 height=16 viewBox="20 20" />
        </span>
    </div>
    <label class="font-normal text-xs text-red-500 hint-text {{ $atr->hintclass }} {{ $name }}">
        {{ $atr->hint }}</label>
</div>