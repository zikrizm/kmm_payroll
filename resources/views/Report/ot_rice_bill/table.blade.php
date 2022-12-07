<main class='border border-gray-200 rounded-lg shadow-sm w-max overflow-hidden'>
    <table class='table border-collapse w-full'>
        <thead class='border-b border-gray-200 bg-gray-50'>
            <tr class=''>
                <th class='text-left'>
                    <div class='flex items-center'>
                        <div class='pl-6 pr-3 py-3 cursor-pointer flex-1'>
                            <x-ui.sort-table text="Tanggal nasi lembur" url="{{ route('ot-rice-bill.index') }}"
                                field="ots_date" order="{{ $order }}" />
                        </div>
                    </div>
                </th>
                <th class='px-3 py-3 text-left cursor-pointer'>
                    <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Bagian</p>
                </th>
                <th class='px-3 py-3 text-left cursor-pointer'>
                    <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Total</p>
                </th>
                <th class='px-3 py-3 text-left text-gray-500 text-xs font-medium'></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ot_rice_bills as $item)
            <tr class='hover:bg-gray-50 border-b border-gray-200'>
                <td class='text-left'>
                    <div class="flex items-center">
                        <div class="flex gap-3 items-center px-6 py-3 cursor-pointer text-gray-500 text-sm">
                            <x-icon icon="calendar" width=18 height=18 viewBox="20 20" />
                            <div class="flex items-center gap-2">
                                <p class="truncate">
                                    {{ date('d-m-Y', strtotime($item->start_date)) }}
                                </p>
                                <p>-</p>
                                <p class="truncate ">
                                    {{ date('d-m-Y', strtotime($item->end_date)) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </td>
                <td class='px-3 py text-gray-500 text-sm'>
                    {{ $item->dept_name }}
                </td>
                <td class='px-3 py text-gray-500 text-sm'>
                    {{ $item->total }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <footer class='flex justify-between items-center px-6 pt-3 pb-4'>
        <p class="text-gray-700 text-sm">Page <span>{{ $ot_rice_bills->currentPage() }}</span> of <span>
                {{ $ot_rice_bills->lastPage() }}</span></p>
        <div class='flex gap-3'>
            @if (!$ot_rice_bills->onFirstPage())
            <button data-pagination-url="{{ $ot_rice_bills->previousPageUrl() }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Previous</button>
            @endif
            @if ($ot_rice_bills->hasMorePages())
            <button data-pagination-url="{{ $ot_rice_bills->nextPageUrl() }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Next</button>
            @endif
        </div>
    </footer>
</main>