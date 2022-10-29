<main class='border border-gray-200 rounded-lg shadow-sm w-max overflow-hidden'>
    <table class='table border-collapse w-full'>
        <thead class='border-b border-gray-200 bg-gray-50'>
            <tr class=''>
                <th class='text-left'>
                    <div class='flex items-center'>
                        <div class='pl-4 py-2 flex items-center'>
                            {!! FormCustom::checkbox() !!}
                        </div>
                        <div class='px-6 py-3 cursor-pointer flex-1'>
                            <x-ui.sort-table text="Nama" url="{{ route('holiday.index') }}" field="name"
                                order="{{ $order }}" />
                        </div>
                    </div>
                </th>
                <th class='px-3  py-3 text-left cursor-pointer'>
                    <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Tanggal mulai</p>
                </th>
                <th class='px-3  py-3 text-left cursor-pointer'>
                    <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Tanggal akhir</p>
                </th>
                <th class='px-3 py-3 text-left cursor-pointer'>
                    <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Jumlah hari</p>
                </th>
                @canany(['holiday.update', 'holiday.delete'])
                <th class='px-3  py-3 text-left text-gray-500 text-xs font-medium'></th>
                @endcanany
            </tr>
        </thead>
        <tbody>
            @foreach ($holidays as $item)
            <tr class='hover:bg-gray-50 border-b border-gray-200'>
                <td class='text-left'>
                    <div class="flex items-center">
                        <div class="pl-4 py ">
                            {!! FormCustom::checkbox() !!}
                        </div>
                        <div class="flex gap-3 items-center px-6 py-3 hover:underline hover:text-gray-500 cursor-pointer"
                            onclick="get_modal('{{ $item['id'] }}')">
                            <p class="text-gray-500 text-sm">
                                {{ $item->name }}
                            </p>
                        </div>
                    </div>
                </td>
                <td class='px-3 py text-gray-500 text-sm'>
                    <div class="flex items-center gap-2">
                        <x-icon icon="calendar" width=18 height=18 viewBox="20 20" />
                        <p class="truncate">
                            {{ date('d-m-Y', strtotime($item->start_date)) }}
                        </p>
                    </div>
                </td>
                <td class='px-3 py text-gray-500 text-sm'>
                    <div class="flex items-center gap-2">
                        <x-icon icon="calendar" width=18 height=18 viewBox="20 20" />
                        <p class="truncate">
                            {{ date('d-m-Y', strtotime($item->end_date)) }}
                        </p>
                    </div>
                </td>
                <td class='px-3 py text-gray-500 text-sm'>
                    {{ date_diff(new \DateTime($item->start_date), new \DateTime($item->end_date.' +1
                    day'))->format("%a"); }} hari
                </td>
                @canany(['holiday.update', 'holiday.delete'])
                <td class='px-3 py'>
                    <div class='flex gap-1'>
                        @can('holiday.delete')
                        <button onclick="open_modal_confirm('{{ $item->id }}')"
                            class='p-2.5 cursor-pointer text-gray-500 delete-btn'>
                            <x-icon icon="trash-2" width=18 height=18 viewBox="20 20" />
                        </button>
                        @endcan
                        @can('holiday.update',)
                        <button class='p-2.5 cursor-pointer text-gray-500 edit-btn'
                            onclick="get_modal('{{ $item->id }}')">
                            <x-icon icon="edit" width=18 height=18 viewBox="20 20" />
                        </button>
                        @endcan
                    </div>
                </td>
                @endcanany
            </tr>
            @endforeach
        </tbody>
    </table>
    <footer class='flex justify-between items-center px-6 pt-3 pb-4'>
        <p class="text-gray-700 text-sm">Page <span>{{ $holidays->currentPage() }}</span> of <span>
                {{ $holidays->lastPage() }}</span></p>
        <div class='flex gap-3'>
            @if (!$holidays->onFirstPage())
            <button data-pagination-url="{{ $holidays->previousPageUrl() }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Previous</button>
            @endif
            @if ($holidays->hasMorePages() )
            <button data-pagination-url="{{ $holidays->nextPageUrl() }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Next</button>
            @endif
        </div>
    </footer>
</main>