<main class='border border-gray-200 rounded-lg shadow-sm w-max overflow-hidden'>
    <table class='table border-collapse w-full'>
        <thead class='border-b border-gray-200 bg-gray-50'>
            <tr class=''>
                <th class='text-left'>
                    <div class='flex items-center'>
                        <div class='pl-6 pr-3 py-3 cursor-pointer flex-1'>
                            <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Tanggal nasi lembur</p>
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
            @foreach ($food_archives as $item)
                <tr class='hover:bg-gray-50 border-b border-gray-200'>
                    <td class='text-left'>
                        <div class="flex items-center">
                            <div class="flex gap-3 items-center px-6 py-3 cursor-pointer text-gray-500 text-sm">
                                <x-icon icon="calendar" width=18 height=18 viewBox="20 20" />
                                <div class="flex items-center gap-2">
                                    <p class="truncate">
                                        {{ date('d-m-Y', strtotime($item->start_date)) }}
                                    </p>-
                                    <p class="truncate">
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
                    <td class='px-3 py text-gray-500 text-sm'>
                        <button onclick="show('{{ $item->id }}')"
                            class="text-gray-500 flex justify-center items-center gap-2 border rounded-lg shadow px-2.5 py-1.5">
                            <x-icon icon="detail" width=16 height=16 viewBox="20 20" />
                            <p class="text-xs">Detail</p>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <footer class='flex justify-between items-center px-6 pt-3 pb-4'>
        <p class="text-gray-700 text-sm">Page <span>{{ $food_archives->currentPage() }}</span> of <span>
                {{ $food_archives->lastPage() }}</span></p>
        <div class='flex gap-3'>
            @if (!$food_archives->onFirstPage())
                <button data-pagination-url="{{ $food_archives->previousPageUrl() }}"
                    class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Previous</button>
            @endif
            @if ($food_archives->hasMorePages())
                <button data-pagination-url="{{ $food_archives->nextPageUrl() }}"
                    class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Next</button>
            @endif
        </div>
    </footer>
</main>
