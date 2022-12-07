<main class='border border-gray-200 rounded-lg shadow-sm w-max overflow-hidden'>
    <div class="w-full overflow-auto overflow-y-hidden">
        <table class='table border-collapse w-full'>
            <thead class='border-b border-gray-200 bg-gray-50'>
                <tr class=''>
                    <th class='px-3 py-3 text-left cursor-pointer border'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Status</p>
                    </th>
                    <th class='text-left'>
                        <div class='flex items-center'>
                            <div class='px-3 py-3 cursor-pointer flex-1'>
                                <x-ui.sort-table text="Tanggal kasbon" url="{{ route('kasbon.index') }}" field="date"
                                    order="{{ $order }}" />
                            </div>
                        </div>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <x-ui.sort-table text="Karyawan" url="{{ route('kasbon.index') }}" field="first_name"
                            order="{{ $order }}" />
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Kasbon</p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Cicilan & Jumlah tercicil
                        </p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer text-right'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Sisa kasbon</p>
                    </th>
                    @canany(['kasbon.update', 'kasbon.delete'])
                    <th class='px-3 py-3 text-left text-gray-500 text-xs font-medium'></th>
                    @endcanany
                </tr>
            </thead>
            <tbody>
                @foreach ($kasbons as $item)
                @php
                $cicilan = (!empty($kasbons->employee_debt_pays))?
                array_sum(array_column($kasbons->employee_debt_pays->toArray(),
                'instalment_debt')): 0;
                @endphp
                <tr class='hover:bg-gray-50 border-b border-gray-200 cursor-pointer'>
                    <td class='px-3 py text-center border'>
                        @if ($item->paid)
                        <div class="flex justify-center">
                            <x-icon icon="check-circle" class="text-green-600" width=22 height=22 viewBox="20 20" />
                        </div>
                        @endif
                    </td>
                    <td class='text-left'>
                        <div class="flex items-center">
                            <div class="flex gap-3 items-center px-3 py-3 cursor-pointer text-gray-500 text-sm"
                                onclick="get_detail_kasbon_modal('{{ $item->id }}')">
                                <x-icon icon="calendar" width=18 height=18 viewBox="20 20" />
                                <p class="truncate text-violet-600 underline">
                                    {{ date('d-m-Y', strtotime($item->date)) }}
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        <div class="flex items-center gap-2">
                            {{ $item->emp_code }} - {{ $item->first_name }}
                        </div>
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        @convert($item->debt)
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        <div class="flex items-center gap-2">
                            <p class="border-r-2 pr-2">@convert($item->instalment)</p>
                            <p class="font-semibold">@convert($item->employee_debt_pays->sum('payment'))</p>
                        </div>
                    </td>
                    <td class='px-3 py text-gray-500 text-sm text-right'>
                        @php
                        $sisa = $item->debt - $item->employee_debt_pays->sum('payment');
                        @endphp
                        @convert($sisa)
                    </td>
                    @canany(['kasbon.update', 'kasbon.delete'])
                    @if (!$item->paid)
                    <td class='px-3 py'>
                        <div class='flex gap-1'>
                            @can('kasbon.delete')
                            <button onclick="open_modal_confirm('{{ $item['id'] }}')"
                                class='px-2.5 cursor-pointer text-gray-500 delete-btn'>
                                <x-icon icon="trash-2" width=18 height=18 viewBox="20 20" />
                            </button>
                            @endcan
                            @can('kasbon.update')
                            <button class='px-2.5 cursor-pointer text-gray-500 edit-btn'
                                onclick="get_modal('{{ $item['id'] }}')">
                                <x-icon icon="edit" width=18 height=18 viewBox="20 20" />
                            </button>
                            @endcan
                        </div>
                    </td>
                    @endif
                    @endcanany
                </tr>
                @endforeach
            </tbody>
        </table>
        <footer class='flex justify-between items-center px-6 pt-3 pb-4'>
            <p class="text-gray-700 text-sm">Page <span>{{ $kasbons->currentPage() }}</span> of <span>
                    {{ $kasbons->lastPage() }}</span></p>
            <div class='flex gap-3'>
                @if (!$kasbons->onFirstPage())
                <button data-pagination-url="{{ $kasbons->previousPageUrl() }}"
                    class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Previous</button>
                @endif
                @if ($kasbons->hasMorePages())
                <button data-pagination-url="{{ $kasbons->nextPageUrl() }}"
                    class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Next</button>
                @endif
            </div>
        </footer>
</main>