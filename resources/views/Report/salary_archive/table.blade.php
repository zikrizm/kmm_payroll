<main class='border border-gray-200 rounded-lg shadow-sm w-max overflow-hidden'>
    <table class='table border-collapse w-full'>
        <thead class='border-b border-gray-200 bg-gray-50'>
            <tr class=''>
                <th class='text-left'>
                    <div class='flex items-center'>
                        <div class='pl-6 pr-3 py-3 cursor-pointer flex-1'>
                            <x-ui.sort-table text="Tanggal penggajian" url="{{ route('salary-archive.index') }}"
                                field="ots_date" order="{{ $order }}" />
                        </div>
                    </div>
                </th>
                <th class='px-3 py-3 text-left cursor-pointer'>
                    <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Tanggal kalkulasi</p>
                </th>
                <th class='px-3 py-3 text-left cursor-pointer'>
                    <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Bagian</p>
                </th>
                {{-- @canany(['salary-archive.create']) --}}
                <th class='px-3 py-3 text-left text-gray-500 text-xs font-medium'></th>
                {{-- @endcanany --}}
            </tr>
        </thead>
        <tbody>
            @foreach ($salary_archives as $item)
            <tr class='hover:bg-gray-50 border-b border-gray-200'>
                <td class='text-left'>
                    <div class="flex items-center">
                        <div class="flex gap-3 items-center px-6 py-3 cursor-pointer text-gray-500 text-sm"
                            onclick="get_modal('{{ $item->id }}')">
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
                <td class='text-left'>
                    <div class="px-3 py flex gap-3 items-center text-gray-500 text-sm"
                        onclick="get_modal('{{ $item->id }}')">
                        <x-icon icon="calendar" width=18 height=18 viewBox="20 20" />
                        <p class="truncate">
                            {{ date('d-m-Y', strtotime($item->created_at)) }}
                        </p>
                    </div>
                </td>
                <td class='px-3 py text-gray-500 text-sm'>
                    {{ $item->dept_name }}
                </td>
                {{-- @canany(['salary-archive.create']) --}}
                <td class='px-3 py'>
                    <div class="flex items-center">
                        <button class='px-2.5  py-1.5 cursor-pointer text-gray-500 edit-btn flex items-center gap-2 border rounded-lg shadow' onclick="get_modal('{{ $item->id }}')">
                            <x-icon icon="calculator" width=14 height=14 viewBox="20 20" />
                            <p class="text-xs">Re-calculation</p>
                        </button>
                    </div>
                </td>
                {{-- @endcanany --}}

            </tr>
            @endforeach
        </tbody>
    </table>
    <footer class='flex justify-between items-center px-6 pt-3 pb-4'>
        <p class="text-gray-700 text-sm">Page <span>{{ $salary_archives->currentPage() }}</span> of <span>
                {{ $salary_archives->lastPage() }}</span></p>
        <div class='flex gap-3'>
            @if (!$salary_archives->onFirstPage())
            <button data-pagination-url="{{ $salary_archives->previousPageUrl() }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Previous</button>
            @endif
            @if ($salary_archives->hasMorePages())
            <button data-pagination-url="{{ $salary_archives->nextPageUrl() }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Next</button>
            @endif
        </div>
    </footer>
</main>