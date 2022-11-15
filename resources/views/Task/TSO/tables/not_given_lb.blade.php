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
                                <x-ui.sort-table text="Tanggal" url="{{ route('request-task.index') }}" field="ots_date"
                                    order="{{ $order }}" />
                            </div>
                        </div>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Karyawan</p>
                    </th>
                    <th class='px-3 py-3 text-center cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Masuk</p>
                    </th>
                    <th class='px-3 py-3 text-center cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Keluar</p>
                    </th>
                    <th class='px-3 py-3 text-center cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Shift</p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Status</p>
                    </th>
                    @canany(['approved-not-given-employee-holiday-pay.approved'])
                    <th class='px-3 py-3 text-left text-gray-500 text-xs font-medium'></th>
                    @endcanany
                </tr>
            </thead>
            <tbody>
                @foreach ($employee_tso_datas['data'] as $item)
                <tr class='hover:bg-gray-50 border-b border-gray-200 cursor-pointer'>
                    <td class='text-left'>
                        <div class="flex items-center">
                            <div class="pl-4 py-2">
                                {!! FormCustom::checkbox() !!}
                            </div>
                            <div
                                class="flex gap-3 items-center px-6 py-3 {{ $item['timetable']['is_holiday'] ? 'text-red-500' : 'text-gray-500' }} text-sm">
                                <x-icon icon="calendar" width=18 height=18 viewBox="20 20" />
                                <p class="flex truncate items-center text-sm">
                                    {{ date('d-m-Y', strtotime($item['date'])) }}
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        <p class="text-gray-500 text-sm truncate cursor-pointer">
                            {{ $item['employee']['first_name'] ??'' }}
                            {{ $item['employee']['last_name'] ??'' }}
                        </p>
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        <div class="flex items-center justify-center gap-2">
                            @if (!empty($item['first_punch']))
                            <x-icon icon="clock" width=18 height=18 viewBox="20 20" />
                            <p class="truncate">
                                {{ date('H:i', strtotime($item['first_punch'])); }}
                            </p>
                            @else
                            x
                            @endif
                        </div>
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        <div class="flex items-center justify-center gap-2">
                            @if (!empty($item['last_punch']))
                            <x-icon icon="clock" width=18 height=18 viewBox="20 20" />
                            <p class="truncate">
                                {{ date('H:i', strtotime($item['last_punch'])) }}
                            </p>
                            @else
                            x
                            @endif
                        </div>
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        @if (!empty($item['timetable']['name']))
                        {{ $item['timetable']['name'] }}
                        @else
                        <p class="text-center">x</p>
                        @endif
                    </td>

                    <td class='px-3 py text-gray-500 text-sm'>
                        @if (!empty($item['timetable']['status']))
                        <div class="flex items-center justify-center gap-1">
                            @if ($item['timetable']['status']['slug'] == 'LB')
                            <p class="text-gray-500">LB</p>
                            @endif
                            @if ($item['timetable']['status']['slug'] == 'not-setting')
                            <p class="text-blue-500">!</p>
                            @endif
                            @if ($item['timetable']['status']['slug'] == 'not-allowed')
                            <x-icon icon="x" class="text-red-500" width=12 height=12 viewBox="20 20" />
                            @endif
                            @if ($item['timetable']['status']['slug'] == 'check')
                            <x-icon icon="check" class="text-green-600" width=12 height=12 viewBox="20 20" />
                            @endif
                            @if ($item['timetable']['status']['slug'] == 'plusmn')
                            <p class="text-gray-500">{{ $item['timetable']['status']['value'] }}</p>
                            @endif
                        </div>
                        @else
                        @endif
                    </td>

                    @canany(['approved-not-given-employee-holiday-pay.approved'])
                    <td class='px-3 py'>
                        <button
                            onclick="get_modal_approve_not_given_lb({{ $item['employee']['id'] }},{{ $item['date'] }})"
                            class="flex items-center gap-2.5 px-2 py-1 text-gray-500 text-sm font-medium flex items-center border border-gray-200 shadow-sm rounded-lg">
                            <x-icon icon="x" width=18 height=18 viewBox="20 20" />
                            Tidak
                        </button>
                    </td>
                    @endcanany
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{-- <footer class='flex justify-between items-center px-6 pt-3 pb-4'>
        <div class="flex items-center gap-3">
            <select class="select2-page w-14" name="" id="">
                <option value="10" @selected($page_size=="10" )>10</option>
                <option value="20" @selected($page_size=="20" )>20</option>
                <option value="50" @selected($page_size=="50" )>50</option>
                <option value="100" @selected($page_size=="100" )>100</option>
            </select>
            <p class='text-gray-700 text-sm'> Page <span> {{ $employee_tso_datas['currentPage']}} </span>
                of <span> {{ $employee_tso_datas['lastPage']}}</span>
            </p>
        </div>
        <div class='flex gap-3'>
            @if (!empty($employee_tso_datas['previous']))
            <button data-pagination-page="{{ $employee_tso_datas['previous'] }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Previous</button>
            @endif

            @if (!empty($employee_tso_datas['next']))
            <button data-pagination-page="{{ $employee_tso_datas['next'] }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Next</button>
            @endif
        </div>
    </footer> --}}
</main>