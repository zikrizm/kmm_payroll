<div class="flex flex-col gap-1">
    <div class="w-full flex items-center relative">
        <textarea name="{{ $name }}" placeholder="{{ $atr->placeholder }}" id="" rows="5"
            class="p-2 w-full h-full text-sm rounded-lg shadow-sm border border-gray-300 focus:outline-none focus:ring-0
                focus:shadow-xs/focused(4px-primary) focus:border-violet-300 xs/max:text-xs xs/max:rounded {{ $atr->class }}" {{
            $atr->required ? 'required': '' }} autocomplete="{{ $atr->autocomplete }}">{{ $value }}</textarea>
    </div>
    <label class="font-normal text-xs text-red-500 xs/max:text-xs hint-text {{ $atr->hintclass }} {{ $name }}"></label>
</div>