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
                                <x-ui.sort-table text="Device name" url="{{ route('device.index') }}" field="alias"
                                    order="{{ $order }}" />
                            </div>
                        </div>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <x-ui.sort-table text="Serial number" url="{{ route('device.index') }}" field="sn"
                            order="{{ $order }}" />
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Area</p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Device IP</p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">State</p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Last activity</p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">User Qty</p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">FP Qty</p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Face Qty</p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Palm Qty</p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Transaction Qty</p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Cmd</p>
                    </th>
                    @canany(['device.update', 'device.delete'])
                    <th class='px-3 py-3 text-left text-gray-500 text-xs font-medium'></th>
                    @endcanany
                </tr>
            </thead>
            <tbody>
                @foreach ($devices['data'] as $item)
                <tr class='hover:bg-gray-50 border-b border-gray-200 cursor-pointer'>
                    <td class='text-left'>
                        <div class="flex items-center">
                            <div class="pl-4 py-2">
                                {!! FormCustom::checkbox() !!}
                            </div>
                            <div class="flex gap-3 items-center px-6 py-3 hover:underline hover:text-gray-500 cursor-pointer"
                                onclick="get_modal('{{ $item['id'] }}')">
                                <p class="text-gray-500 text-sm">
                                    {{ $item['alias'] }}
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        {{ $item['sn'] }}
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        {{ (!empty($item['area'])) ? $item['area']['area_name'] : '-' }}
                    </td>
                    <td class='px-3 py text-gray-500 text-sm truncate'>
                        {{ $item['ip_address'] }}
                    </td>
                    <td class='px-3 py text-gray-500 text-sm truncate'>
                        {{ $item['state'] }}
                    </td>
                    <td class='px-3 py text-gray-500 text-sm truncate'>
                        {{ $item['last_activity'] }}
                    </td>
                    <td class='px-3 py text-gray-500 text-sm truncate'>
                        {{ $item['user_count'] ?? '-' }}
                    </td>
                    <td class='px-3 py text-gray-500 text-sm truncate'>
                        {{ $item['fp_count']?? '-' }}
                    </td>
                    <td class='px-3 py text-gray-500 text-sm truncate'>
                        {{ $item['face_count'] ?? '-' }}
                    </td>
                    <td class='px-3 py text-gray-500 text-sm truncate'>
                        {{ $item['palm_count'] ?? '-' }}
                    </td>
                    <td class='px-3 py text-gray-500 text-sm truncate'>
                        {{ $item['transaction_count']?? '-' }}
                    </td>
                    <td class='px-3 py text-gray-500 text-sm truncate'>
                        {{ $item['cmd_count'] ?? '-' }}
                    </td>
                    @canany(['device.update', 'device.delete'])
                    <td class='px-3 py'>
                        <div class='flex gap-1'>
                            @can('device.delete')
                            <button onclick="open_modal_confirm('{{ $item['id'] }}')"
                                class='px-2.5 cursor-pointer text-gray-500 delete-btn'>
                                <x-icon icon="trash-2" width=18 height=18 viewBox="20 20" />
                            </button>
                            @endcan
                            @can('device.update')
                            <button class='px-2.5 cursor-pointer text-gray-500 edit-btn'
                                onclick="get_modal('{{ $item['id'] }}')">
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
    </div>
    <footer class='flex justify-between items-center px-6 pt-3 pb-4'>
        @php $page = 1; @endphp
        <p class='text-gray-700 text-xs'>
            Page <span> {{ $page }} </span> of <span>{{ ceil($devices['count'] / 10) }}</span>
        </p>
        <div class='flex gap-3'>
            @if (!empty($devices['previous']))
            <button data-pagination-url="{{ $devices['previous'] }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-xs hover:bg-gray-50'>Previous</button>
            @elseif (!empty($devices['next']))
            <button data-pagination-url="{{ $devices['next'] }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-xs hover:bg-gray-50'>Next</button>
            @endif
        </div>
    </footer>
</main>