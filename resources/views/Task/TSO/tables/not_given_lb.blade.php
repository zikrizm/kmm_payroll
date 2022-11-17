<main class='border border-gray-200 rounded-lg shadow-sm w-max overflow-hidden'>
    <div class="w-full overflow-auto overflow-y-hidden">
        <table class='table border-collapse w-full table-not-given-lb'>
            <thead class='border-b border-gray-200 bg-gray-50'>
                <tr class=''>
                    <th class='text-left'>
                        <div class='flex items-center'>
                            <div class='pl-4 py-2 flex items-center'>
                                {!! FormCustom::checkbox(null, true, ['class' => 'select-all-card']) !!}
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
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Bagian</p>
                    </th>
                    @canany(['approved-not-given-employee-holiday-pay.approved'])
                    <th class='px-3 py-3 text-left text-gray-500 text-xs font-medium'></th>
                    @endcanany
                </tr>
            </thead>
            <tbody>
                @foreach ($employee_lb_datas['data'] as $item)
                <tr class='hover:bg-gray-50 border-b border-gray-200'>
                    <td class='text-left'>
                        <div class="flex items-center">
                            <div class="pl-4 py-2">
                                @if ($item['is_approved_not_given_lb'])
                                <span class="min-h-[16px] min-w-[16px] w-4 h-4 block"></span>
                                @else
                                <input type="hidden" name="emp_id" value="{{ $item['employee']['id'] }}">
                                <input type="hidden" name="lb_date" value="{{ $item['date'] }}">
                                <input type="hidden" name="dept_id" value="{{ $item['employee']['department']['id'] }}">
                                {!! FormCustom::checkbox(null, true, ['class' => 'select-card']) !!}
                                @endif
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
                        <p class="text-gray-500 text-sm truncate">
                            {{ $item['employee']['first_name'] ??'' }}
                            {{ $item['employee']['last_name'] ??'' }}
                        </p>
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        <p class="text-gray-500 text-sm truncate">
                            {{ $item['employee']['department']['dept_name'] ?? '-' }}
                        </p>
                    </td>
                    @canany(['approved-not-given-employee-holiday-pay.approved'])
                    <td class='px-3 py'>
                        <button @disabled($item['is_approved_not_given_lb'])
                            onclick="get_modal_approve_not_given_lb('{{ $item['employee']['id'] }}', '{{ $item['date'] }}', '{{ $item['employee']['department']['id'] }}')"
                            class="flex items-center gap-2.5 px-2 py-1 text-gray-500 text-sm font-medium flex items-center border border-gray-200 shadow-sm rounded-lg {{ $item['is_approved_not_given_lb'] ? 'cursor-not-allowed' : '' }}"
                            style="opacity: {{ $item['is_approved_not_given_lb'] ? '0.5' : '' }};">
                            <x-icon icon="x" width=18 height=18 viewBox="20 20" />
                            Tidak dapat LB
                        </button>
                    </td>
                    @endcanany
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
            <p class='text-gray-700 text-sm'> Page <span> {{ $employee_lb_datas['currentPage']}} </span>
                of <span> {{ $employee_lb_datas['lastPage']}}</span>
            </p>
        </div>
        <div class='flex gap-3'>
            @if (!empty($employee_lb_datas['previous']))
            <button data-pagination-page="{{ $employee_lb_datas['previous'] }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Previous</button>
            @endif

            @if (!empty($employee_lb_datas['next']))
            <button data-pagination-page="{{ $employee_lb_datas['next'] }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Next</button>
            @endif
        </div>
    </footer>
</main>