<div>
    <div class="w-full max-w-[280px] min-w-[180px] h-9 relative flex items-center">
        <div class="absolute left-0 h-full w-9 flex justify-center items-center text-center text-gray-500 text-sm">
            <span class="">
                <x-icon icon="search" width=16 height=16 viewBox="20 20" />
            </span>
        </div>
        <input placeholder="{{ $placeholder }}" data-search-url="{{ $url }}"
            class="search-data-input pl-9 pr-2.5 w-full h-full text-sm rounded-lg shadow-sm border border-gray-300 focus:outline-none focus:ring-0
        focus:shadow-xs/focused(4px-primary) focus:border-violet-300" />
    </div>
</div>
