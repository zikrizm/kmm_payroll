<div class="flex items-center justify-center relative">
    <input {{ $atr->checked ? 'checked': ''}} id="{{ $atr->id }}" type='checkbox' name="{{ $name }}" value="{{ $value }}"
        class="min-h-[16px] min-w-[16px] w-4 h-4 opacity-0 z-10 peer cursor-pointer {{ $atr->class }}" />
    <span class="absolute min-h-[16px] min-w-[16px] w-4 h-4 border rounded cursor-pointer
        flex items-center justify-center peer-checked:border-violet-600 invisible peer-checked:visible">
        <x-icon icon="check" width=12 height=12 viewBox="20 20" />
    </span>
    <span class="absolute min-h-[16px] min-w-[16px] w-4 h-4 border rounded
         visible peer-checked:invisible cursor-pointer">
    </span>
</div>