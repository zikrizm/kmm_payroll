<div class="flex items-center gap-2 sort-table {{ $order == 'ASC' ? 'active' : '' }}" onclick="sort_data(this)"
    data-sort-url={{ $url }} data-sort-key="{{ $field }}">
    <p class="text-xs font-medium text-gray-500 truncate cursor-pointer hover:underline">
        {{ $text }}</p>
    <span class="sort-icon {{ $order == 'ASC' ? 'rotate-180' : '' }}">
        <x-icon icon="{{ $icon }}" width=12 height=12 viewBox="20 20" />
    </span>
</div>
