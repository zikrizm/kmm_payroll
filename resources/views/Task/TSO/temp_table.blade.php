<main class='border border-gray-200 rounded-lg shadow-sm w-full overflow-hidden'>
    <div class="w-full overflow-auto overflow-y-hidden">
        <table class='table border-collapse w-full table-tso'>
            <thead class='border-b border-gray-200 bg-gray-50'>
                <tr class=''>
                    <th class='text-left'>
                        <div class='flex items-center'>
                            <div class='pl-4 py-2 flex items-center'>
                                <div class="flex items-center justify-center relative">
                                    <input type='checkbox' onchange="selectAllAttendance(this)"
                                        class="min-h-[16px] min-w-[16px] w-4 h-4 opacity-0 z-10 peer cursor-pointer" />
                                    <span
                                        class="absolute min-h-[16px] min-w-[16px] w-4 h-4 border border-gray-300 rounded cursor-pointer
                                        flex items-center justify-center peer-checked:border-violet-600 invisible peer-checked:visible">
                                        <x-icon icon="check" width=12 height=12 viewBox="20 20" />
                                    </span>
                                    <span class="absolute min-h-[16px] min-w-[16px] w-4 h-4 border border-gray-300 rounded
                                         visible peer-checked:invisible cursor-pointer">
                                    </span>
                                </div>
                            </div>
                            <div class='pl-6 pr-3 py-3 cursor-pointer flex-1'>
                                <p class="text-xs font-medium text-gray-500 truncate">Tanggal absensi dan Waktu absensi
                                </p>
                            </div>
                        </div>
                    </th>
                    <th class='px-3 py-3 text-left'>
                        <p class="text-xs font-medium text-gray-500 truncate">Karyawan</p>
                    </th>
                    <th class='px-3 py-3 text-left'>
                        <p class="text-xs font-medium text-gray-500 truncate">Bagian</p>
                    </th>
                    <th class='px-3 py-3 text-center'>
                        <p class="text-xs font-medium text-gray-500 truncate">Shift</p>
                    </th>
                    <th class='px-3 py-3 text-left'>
                        <p class="text-xs font-medium text-gray-500 truncate">Keterangan</p>
                    </th>
                    <th class='px-3 py-3 text-center'>
                        <p class="text-xs font-medium text-gray-500 truncate">Status</p>
                    </th>
                    {{-- @canany(['attendance-tso.approved']) --}}
                    <th class='px-3 py-3 text-left text-gray-500 text-xs font-medium'></th>
                    {{-- @endcanany --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($attendance_tsos as $item)
                <tr class='hover:bg-gray-50 border-b border-gray-200' data-tso-item>
                    <td class='text-left'>
                        <div class="flex items-center">
                            <div class="pl-4 py-2">
                                @if ($item['attendance_tso_id'])
                                <span class="min-h-[16px] min-w-[16px] w-4 h-4 block"></span>
                                @else
                                <input type="hidden" name="emp_id" value="{{ $item['employee']['id'] }}">
                                <input type="hidden" name="dept_id" value="{{ $item['employee']['department']['id'] }}">
                                <input type="hidden" name="tso_date" value="{{ $item['date'] }}">
                                <input type="hidden" name="first_punch" value="{{ $item['first_punch'] }}">
                                <input type="hidden" name="last_punch" value="{{ $item['last_punch'] }}">
                                <input type="hidden" name="operational_id" value="{{ $item['operational_id'] }}">
                                <input type="hidden" name="timetable_id"
                                    value="{{ !empty($item['timetable'])? $item['timetable']['id'] : null }}">
                                <div class="flex items-center justify-center relative">
                                    <input type='checkbox' onchange="selectAttendance()" data-checkbox-tso-item
                                        class="min-h-[16px] min-w-[16px] w-4 h-4 opacity-0 z-10 peer cursor-pointer" />
                                    <span
                                        class="absolute min-h-[16px] min-w-[16px] w-4 h-4 border border-gray-300 rounded cursor-pointer
                                        flex items-center justify-center peer-checked:border-violet-600 invisible peer-checked:visible">
                                        <x-icon icon="check" width=12 height=12 viewBox="20 20" />
                                    </span>
                                    <span class="absolute min-h-[16px] min-w-[16px] w-4 h-4 border border-gray-300 rounded
                                         visible peer-checked:invisible cursor-pointer">
                                    </span>
                                </div>
                                @endif
                            </div>
                            <div class="flex-1 flex gap-3 items-center pl-6 pr-3 py-3">
                                <div
                                    class="flex gap-3 items-center {{ $item['is_holiday'] ? 'text-red-500' : 'text-gray-500' }} text-sm">
                                    <x-icon icon="calendar" width=18 height=18 viewBox="20 20" />
                                    <p class="flex truncate items-center text-sm">
                                        {{ date('d-m-Y', strtotime($item['date'])) }}
                                    </p>
                                </div>
                                <div
                                    class="border-l-2 pl-2 flex items-center gap-1 justify-around flex-1 text-sm text-gray-500">
                                    <div class="flex items-center justify-center gap-2">
                                        @if (!empty($item['first_punch']))
                                        <p class="truncate text-sm"> {{ date('H:i', strtotime($item['first_punch'])); }}
                                        </p>
                                        @else
                                        -
                                        @endif
                                    </div>
                                    @if (!empty($item['last_punch']))
                                    <p>-</p>
                                    @endif
                                    <div class="flex items-center justify-center gap-2">
                                        @if (!empty($item['last_punch']))
                                        <p class="truncate text-sm"> {{ date('H:i', strtotime($item['last_punch'])) }}
                                        </p>
                                        @else
                                        -
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class='px-3 py-3 text-gray-500 text-sm'>
                        <p class="text-gray-500 text-sm truncate">
                            {{ $item['employee']['first_name'] ?? '' }} {{ $item['employee']['last_name'] ??'' }}
                        </p>
                    </td>
                    <td class='px-3 py-3 text-gray-500 text-sm'>
                        <p class="text-gray-500 text-sm truncate">
                            {{ $item['employee']['department']['dept_name'] ?? '-' }}
                        </p>
                    </td>
                    <td class='px-3 py-3 text-gray-500 text-sm'>
                        @if (!empty($item['timetable']))
                        <p class="truncate">{{ $item['timetable']['name'] }}</p>
                        @else
                        <p class="text-center">-</p>
                        @endif
                    </td>
                    <td class='px-3 py-3 text-gray-500 text-sm min-w-[240px]'>
                        <p>{{ $item['operational_note']??'' }}</p>
                    </td>
                    <td class='px-3 py-3 text-gray-500 text-sm'>
                        <div class="flex items-center justify-center gap-1">
                            @if ($item['attendance_tso_id'] || $item['operational_status'] == 'valid')
                            <x-icon icon="check" class="text-green-600" width=12 height=12 viewBox="20 20" />
                            @else
                            @if ($item['attendance_lb_status'] == 'accept')
                            <p class="text-gray-500">LB</p>
                            @endif
                            @if ($item['operational_status'] == 'invalid')
                            <x-icon icon="x" class="text-red-500" width=12 height=12 viewBox="20 20" />
                            @endif
                            @if (!empty($item['operational_plusm_value']))
                            <p class="text-gray-500">{{ $item['operational_plusm_value'] }}</p>
                            @endif
                            @endif
                        </div>
                    </td>
                    {{-- @canany(['attendance-tso.approved', 'attendance-lb.set-status']) --}}
                    <td class='px-3 py-3'>
                        <div class="flex justify-center items-center gap-2.5 w-full">
                            @if (!empty($item['attendance_lb_id']) && $item['attendance_lb_status'] == 'cancel')
                            <button disabled
                                class="truncate flex items-center gap-2.5 px-2 py-1 text-gray-500 text-sm font-medium flex items-center border border-gray-200 shadow-sm rounded-lg cursor-not-allowed"
                                style="opacity: 0.5;">
                                <x-icon icon="x" width=16 height=16 viewBox="20 20" />
                                Tidak dapat LB
                            </button>
                            @elseif (!empty($item['attendance_lb_id']) && $item['attendance_lb_status'] == 'cancel')
                            <button disabled
                                class="truncate flex items-center gap-2.5 px-2 py-1 text-gray-500 text-sm font-medium flex items-center border border-gray-200 shadow-sm rounded-lg cursor-not-allowed"
                                style="opacity: 0.5;">
                                <x-icon icon="check" width=16 height=16 viewBox="20 20" />
                                Dapat LB
                            </button>
                            @elseif($item['attendance_lb_status'] == 'accept')
                            <button
                                class="truncate flex items-center gap-2.5 px-2 py-1 text-gray-500 text-sm font-medium flex items-center border border-gray-200 shadow-sm rounded-lg ">
                                <input type="hidden" name="action" value="cancel-lb">
                                <x-icon icon="x" width=16 height=16 viewBox="20 20" />
                                Tidak dapat LB
                            </button>
                            @elseif(empty($item['first_punch']))
                            <button
                                class="truncate flex items-center gap-2.5 px-2 py-1 text-gray-500 text-sm font-medium flex items-center border border-gray-200 shadow-sm rounded-lg ">
                                <input type="hidden" name="action" value="accept-lb">
                                <x-icon icon="check" width=16 height=16 viewBox="20 20" />
                                Dapat LB
                            </button>
                            @endif
                            @if ($item['attendance_tso_id'])
                            <button disabled
                                class="truncate flex items-center gap-2.5 px-2 py-1 text-gray-500 text-sm font-medium flex items-center border border-gray-200 shadow-sm rounded-lg cursor-not-allowed"
                                style="opacity: 0.5;">
                                <x-icon icon="check" width=16 height=16 viewBox="20 20" />
                                Disetujui
                            </button>
                            @else
                            <button
                                class="truncate flex items-center gap-2.5 px-2 py-1 text-gray-500 text-sm font-medium flex items-center border border-gray-200 shadow-sm rounded-lg ">
                                <input type="hidden" name="action" value="approved-tso">
                                <x-icon icon="check" width=16 height=16 viewBox="20 20" />
                                Disetujui
                            </button>
                            @endif



                            {{-- @if ($item['timetable']['status']['for'] == 'cancel-LB')
                            @if ($item['is_approved'])
                            <button disabled
                                class="truncate flex items-center gap-2.5 px-2 py-1 text-gray-500 text-sm font-medium flex items-center border border-gray-200 shadow-sm rounded-lg cursor-not-allowed"
                                style="opacity: 0.5;">
                                <x-icon icon="x" width=16 height=16 viewBox="20 20" />
                                Tidak dapat LB
                            </button>
                            @else
                            <button onclick="get_modal_change_status_given_lb({{ json_encode([
                                    'emp_id' => $item['employee']['id'],
                                    'dept_id' => $item['employee']['department']['id'],
                                    'date' => $item['date'],
                                    'type' => 'cancel',
                                ]) }})"
                                class="truncate flex items-center gap-2.5 px-2 py-1 text-gray-500 text-sm font-medium flex items-center border border-gray-200 shadow-sm rounded-lg ">
                                <x-icon icon="x" width=16 height=16 viewBox="20 20" />
                                Tidak dapat LB
                            </button>
                            @endif
                            @elseif($item['timetable']['status']['for'] == 'approved-LB')
                            @if ($item['is_approved'])
                            <button disabled
                                class="truncate flex items-center gap-2.5 px-2 py-1 text-gray-500 text-sm font-medium flex items-center border border-gray-200 shadow-sm rounded-lg cursor-not-allowed"
                                style="opacity: 0.5;">
                                <x-icon icon="check" width=16 height=16 viewBox="20 20" />
                                Dapat LB
                            </button>
                            @else
                            <button onclick="get_modal_change_status_given_lb({{ json_encode([
                                'emp_id' => $item['employee']['id'],
                                'dept_id' => $item['employee']['department']['id'],
                                'date' => $item['date'],
                                'type' => 'given',
                            ]) }})"
                                class="truncate flex items-center gap-2.5 px-2 py-1 text-gray-500 text-sm font-medium flex items-center border border-gray-200 shadow-sm rounded-lg ">
                                <x-icon icon="check" width=16 height=16 viewBox="20 20" />
                                Dapat LB
                            </button>
                            @endif
                            @elseif($item['timetable']['status']['for'] == 'approved-tso')
                            @if ($item['is_approved'])
                            <button disabled
                                class="truncate flex items-center gap-2.5 px-2 py-1 text-gray-500 text-sm font-medium flex items-center border border-gray-200 shadow-sm rounded-lg cursor-not-allowed"
                                style="opacity: 0.5;">
                                <x-icon icon="check" width=16 height=16 viewBox="20 20" />
                                Disetujui
                            </button>
                            @else
                            <button onclick="get_modal_approve_tso({{ json_encode([
                                    'emp_id' => $item['employee']['id'],
                                    'dept_id' => $item['employee']['department']['id'],
                                    'id_operasional' => $item['id_operasional'],
                                    'tso_date' => $item['date'],
                                    'status' => $item['timetable']['status'] ?? null,
                                ]) }})"
                                class="truncate flex items-center gap-2.5 px-2 py-1 text-gray-500 text-sm font-medium flex items-center border border-gray-200 shadow-sm rounded-lg ">
                                <x-icon icon="check" width=16 height=16 viewBox="20 20" />
                                Disetujui
                            </button>
                            @endif
                            @endif --}}
                        </div>
                    </td>
                    {{-- @endcanany --}}
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