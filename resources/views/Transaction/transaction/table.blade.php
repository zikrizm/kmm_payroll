<main class='border border-gray-200 rounded-lg shadow-sm '>
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
                                <x-ui.sort-table text="Employee code" url="{{ route('transaction.index') }}"
                                    field="emp_code" order="{{ $order }}" />
                            </div>
                        </div>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <x-ui.sort-table text="First name" url="{{ route('transaction.index') }}" field="first_name"
                            order="{{ $order }}" />
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Department</p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <x-ui.sort-table text="Date" url="{{ route('transaction.index') }}" field="punch_time"
                            order="{{ $order }}" />
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Time</p>
                    </th>
                    <th class='px-3 py-3 text-center cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Punch state</p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Work code</p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Data sources</p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Terminal alias</p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Terminal sn</p>
                    </th>
                    @canany(['transaction.update', 'transaction.delete'])
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
                            <div class="flex gap-3 items-center px-6 py-3">
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
                            <p class="truncate">
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
                        <p class="truncate">
                            {{ $item['work_code'] }}
                        </p>
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        <p class="truncate">
                            {{ $item['source'] ?? 'device' }}
                        </p>
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        <p class="truncate">
                            {{ $item['terminal_alias'] ?? '-' }}
                        </p>
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        <p class="truncate">
                            {{ $item['terminal_sn'] ?? '-' }}
                        </p>
                    </td>
                    <td class='px-3 py'>
                        <div class='flex gap-1'>
                            <button onclick="open_modal_confirm('{{ $item['id'] }}')"
                                class='p-2.5 cursor-pointer text-gray-500 delete-btn'>
                                <x-icon icon="trash-2" width=18 height=18 viewBox="20 20" />
                            </button>
                            <button class='p-2.5 cursor-pointer text-gray-500 edit-btn'
                                onclick="get_modal({{ $item['emp_code'] }})">
                                <x-icon icon="edit" width=18 height=18 viewBox="20 20" />
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <footer class='flex justify-between items-center px-6 pt-3 pb-4'>
        <p class='text-gray-700 text-xs'>
            Page <span> {{ ((int)$transactions['next'] != 0) ? (int)$transactions['next'] - 1 : 1}} </span>
            of <span> {{ ceil($transactions['count'] / 10)}}</span>
        </p>
        <div class='flex gap-3'>
            @if (!empty($transactions['previous']))
            <button data-pagination-page="{{ $transactions['previous'] }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-xs hover:bg-gray-50'>Previous</button>
            @endif

            @if (!empty($transactions['next']))
            <button data-pagination-page="{{ $transactions['next'] }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-xs hover:bg-gray-50'>Next</button>
            @endif
        </div>
    </footer>
</main>