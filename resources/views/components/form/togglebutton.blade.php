<div class="flex flex-col gap-1">
    <div class="border rounded-xl xs/max:rounded-lg w-max shadow-sm flex overflow-hidden">
        <input type="hidden" name="{{ $name }}" id="togglebutton" value="{{ $value }}">
        @foreach ($list as $item)
        <button type="button"
            class="text-sm xs/max:text-xs text-normal text-gray-600 px-4 py-1.5 btn-status {{ $loop->index != count($list) -1 ? 'border-r': '' }} {{ strtolower($item) == strtolower($value) ? 'bg-gray-100' : '' }}"
            value="{{ strtolower($item) }}">
            {{ $item }}
        </button>
        @endforeach
    </div>
    <label class="font-normal text-xs text-red-500 xs/max:text-xs status text-error"></label>
</div>