<main class='border border-gray-200 rounded-lg shadow-sm w-max overflow-hidden'>
    <div class="w-full overflow-auto overflow-y-hidden">
        <table class='table border-collapse w-full'>
            <thead class='border-b border-gray-200 bg-gray-50'>
                <tr class=''>
                    <th class='text-left'>
                        <div class='flex items-center'>
                            <div class='pl-4 py-2 flex items-center'>
                                {!! FormCustom::checkbox() !!}
                            </div>
                            <div class='px-6 py-3 cursor-pointer flex-1'>
                                <x-ui.sort-table text="NIK" url="{{ route('transaction.index') }}" field="emp_code"
                                    order="{{ $order }}" />
                            </div>
                        </div>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <x-ui.sort-table text="Nama" url="{{ route('transaction.index') }}" field="first_name"
                            order="{{ $order }}" />
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Bagian</p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <x-ui.sort-table text="Tanggal" url="{{ route('transaction.index') }}" field="punch_time"
                            order="{{ $order }}" />
                    </th>
                    <th class='px-3 py-3 text-center cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Jam</p>
                    </th>
                    <th class='px-3 py-3 text-center cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Status absen</p>
                    </th>
                    <th class='px-3 py-3 text-center cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Sumber data</p>
                    </th>
                    @canany(['transaction.delete'])
                    <th class='px-3 py-3 text-left text-gray-500 text-xs font-medium'></th>
                    @endcanany
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions['data'] as $item)
                <tr class='hover:bg-gray-50 border-b border-gray-200'>
                    <td class='text-left'>
                        <div class="flex items-center">
                            <div class="pl-4 py ">
                                {!! FormCustom::checkbox() !!}
                            </div>
                            <div class="flex gap-3 items-center px-6 py-3 hover:underline hover:text-gray-500 cursor-pointer"
                                onclick="get_modal('{{ $item['emp_code'] }}')">
                                <p class="text-gray-500 text-sm">
                                    {{ $item['emp_code'] }}
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        {{ $item['first_name'] }}
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        <p class="truncate">
                            {{ (!empty($item['department']) ? $item['department'] : '-') }}
                        </p>
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        <div class="flex items-center gap-2">
                            <x-icon icon="calendar" width=18 height=18 viewBox="20 20" />
                            <p class="truncate">
                                {{ date('Y-m-d', strtotime($item['punch_time'])); }}
                            </p>
                        </div>
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        <div class="flex items-center gap-2">
                            <x-icon icon="clock" width=18 height=18 viewBox="20 20" />
                            <p class="truncate text-center">
                                {{ date('H:i', strtotime($item['punch_time'])); }}
                            </p>
                        </div>
                    </td>
                    <td class='px-3 py text-center text-gray-500 text-sm'>
                        <p class="truncate">
                            {{ $item['punch_state_display'] }}
                        </p>
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        <p
                            class="truncate text-center {{ $item['verify_type_display'] == 'Manual' ? 'line-through decoration-red-500': '' }}">
                            device
                        </p>
                    </td>
                    @can('transaction.delete')
                    <td class='px-3 py'>
                        <div class='flex gap-1'>
                            <button onclick="open_modal_confirm('{{ $item['id'] }}')"
                                class='px-2.5 cursor-pointer text-gray-500 delete-btn'>
                                <x-icon icon="trash-2" width=18 height=18 viewBox="20 20" />
                            </button>
                        </div>
                    </td>
                    @endcan
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <footer class='flex justify-between items-center px-6 pt-3 pb-4'>
        <div class="flex items-center gap-3">
            <select class="select2-page w-14" name="" id="">
                <option value="10" @selected($page_size=="10" )>10</option>
                <option value="20" @selected($page_size=="20" )>20</option>
                <option value="50" @selected($page_size=="50" )>50</option>
                <option value="100" @selected($page_size=="100" )>100</option>
            </select>
            <p class='text-gray-700 text-sm'> Page <span> {{ $transactions['currentPage']}} </span>
                of <span> {{ $transactions['lastPage']}}</span>
            </p>
        </div>
        <div class='flex gap-3'>
            @if (!empty($transactions['previous']))
            <button data-pagination-page="{{ $transactions['previous'] }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Previous</button>
            @endif

            @if (!empty($transactions['next']))
            <button data-pagination-page="{{ $transactions['next'] }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Next</button>
            @endif
        </div>
    </footer>
    {{-- <footer class='flex justify-between items-center px-6 pt-3 pb-4'>
        @php
        $page_of = ceil($transactions['count'] / (int)$page_size);
        if($transactions['next']) {
        $parts = parse_url($transactions['next']);
        parse_str($parts['query'], $query);
        $page = (int)$query['page'] -1;
        }else {
        $page = $page_of;
        }
        @endphp
        <div class="flex items-center gap-3">
            <select class="select2-page w-14" name="" id="">
                <option value="10" @selected($page_size=="10" )>10</option>
                <option value="20" @selected($page_size=="20" )>20</option>
                <option value="50" @selected($page_size=="50" )>50</option>
                <option value="100" @selected($page_size=="100" )>100</option>
            </select>
            <p class='text-gray-700 text-xs'>
                Page <span> {{ $page}} </span> of <span>{{ $page_of }}</span>
            </p>
        </div>
        <div class='flex gap-3'>
            @if (!empty($transactions['previous']))
            <button data-pagination-url="{{ $transactions['previous'] }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Previous</button>
            @endif

            @if (!empty($transactions['next']))
            <button data-pagination-url="{{ $transactions['next'] }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Next</button>
            @endif
        </div>
    </footer> --}}
</main>