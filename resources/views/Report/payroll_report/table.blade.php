<main class='border border-gray-200 rounded-lg shadow-sm  overflow-hidden'>
    <div class="w-full overflow-auto overflow-y-hidden">
        <table class='table border-collapse w-full'>
            <thead class='border border-gray-200 bg-gray-50'>
                <tr class='border-t'>
                    <th class='border border-t-0 border-l-0 px-3 py-1 text-left' rowspan="2">
                        <p class="text-xs font-medium text-gray-500 truncate">No.</p>
                    </th>
                    <th class='border border-t-0 px-3 py-1 text-center'>
                        <p class="text-xs font-medium text-gray-500 truncate">Nama</p>
                    </th>
                    @foreach ($th_dates as $item)
                    <th class='border border-t-0 px-3 py-1 text-left w-[56px] min-w-[56px]'>
                        <p
                            class="text-xs font-medium truncate text-center {{ $item['is_holiday'] ? 'text-gray-300' : 'text-gray-500' }} ">
                            {{ date('d', strtotime($item['date'])) }}
                        </p>
                    </th>
                    @endforeach
                    <th class='border border-t-0 px-3 py-1 text-left' rowspan="2">
                        <p class="text-xs font-medium text-gray-500 truncate text-center">HK</p>
                    </th>
                    <th class='border border-t-0 px-3 py-1 text-left' rowspan="2">
                        <p class="text-xs font-medium text-gray-500 truncate text-center">JL</p>
                    </th>
                    <th class='border border-t-0 border-r-0 px-3 py-1 text-center' colspan="6">
                        <p class="text-xs font-medium text-gray-500 truncate">Jumlah( Rupiah )</p>
                    </th>

                </tr>
                <tr class='border-b-2'>
                    <th class='border px-3 py-1 text-center'>
                        <p class="text-xs font-medium text-gray-500 truncate">Pegawai</p>
                    </th>
                    @foreach ($th_dates as $item)
                    <th class='border px-3 py-1 text-left w-[56px] min-w-[56px]'>
                        <div class="tooltip-custom">
                            <p
                                class="text-xs font-medium truncate text-center {{ $item['is_holiday'] ? 'text-gray-300 cursor-pointer' : 'text-gray-500' }} ">
                                {{ $item['slug'] }}
                            </p>
                            @if ($item['is_holiday'])
                            <div
                                class="tooltip-custom-text border p-1 px-1.5 top-[-30px] rounded bg-white after:!border-t-gray-300 flex flex-col items-center">
                                <p class="truncate text-[10px] text-gray-500 font-normal">{{ $item['holiday_name'] }}</p>
                            </div>
                            @endif

                        </div>
                    </th>
                    @endforeach
                    <th class='border px-3 py-1 text-center'>
                        <p class="text-xs font-medium text-gray-500 truncate">Gaji</p>
                    </th>
                    <th class='border px-3 py-1 text-center'>
                        <p class="text-xs font-medium text-gray-500 truncate">Kasbon</p>
                    </th>
                    <th class='border px-3 py-1 text-left'>
                        <p class="text-xs font-medium text-gray-500 truncate">Lembur</p>
                    </th>
                    <th class='border px-3 py-1 text-center'>
                        <p class="text-xs font-medium text-gray-500 truncate">Rbhn+U.Libur</p>
                    </th>
                    <th class='border px-3 py-1 text-center'>
                        <p class="text-xs font-medium text-gray-500 truncate">Jabatan</p>
                    </th>
                    <th class='border border-r-0 px-3 py-1 text-center'>
                        <p class="text-xs font-medium text-gray-500 truncate">TOTAL</p>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendance_reports as $key => $item)
                <tr class='hover:bg-gray-50 border-gray-200'>
                    <td class='border border-b-0 border-l-0 px-3 py-2 text-gray-500 text-xs text-right w-10'>
                        {{ $key +1 }}
                    </td>
                    <td class='border border-b-0 px-3 py-2 text-gray-500 text-xs'>
                        <p class="truncate">{{ $item['employee']['first_name'] ?? '-' }} {{
                            $item['employee']['last_name'] ?? ''}}
                        </p>
                    </td>
                    @foreach (($item['reports'] ?? []) as $item_report)
                    <td class='border border-b-0 px-3 py-2  text-left w-[56px] min-w-[56px]'>
                        <div
                            class="tooltip-custom flex justify-center relative {{ (!empty($item_report['timetable']['status']) && !$item_report['timetable']['status']['valid']) ? 'border-l-2 border-l-red-500 cursor-pointer' :'' }} w-full h-full">
                            <p
                                class="text-xs truncate text-center {{ !empty($item_report['timetable']['is_holiday']) && $item_report['timetable']['is_holiday'] ? 'text-gray-300' : 'text-gray-500' }} ">
                                {{ $item_report['timetable']['atten_value_day'] }}
                            </p>
                            @if (!empty($item_report['timetable']['calculate_one_shift']))
                            <p class="absolute text-[8px] text-violet-600 right-[-6px] top-[-3px]">+{{
                                $item_report['timetable']['calculate_one_shift'] }}</p>
                            @endif

                            @if (!empty($item_report['timetable']['status']) && !$item_report['timetable']['status']['valid'])
                                <div
                                    class="tooltip-custom-text border p-1 px-1.5 top-[-30px] rounded bg-white after:!border-t-gray-300 flex flex-col items-center">
                                    <p class="truncate text-[10px] text-gray-500">{{ $item_report['timetable']['status']['noted'] ?? '-' }}</p>
                                </div>
                            @endif
                            
                        </div>
                    </td>
                    @endforeach
                    <td class='border border-b-0 px-3 py-2 text-gray-500 text-xs'>
                        <p class="truncate text-center">{{ $item['amount_day'] ?? 0 }}</p>
                    </td>
                    <td class='border border-b-0 px-3 py-2 text-gray-500 text-xs'>
                        <p class="truncate text-center">{{ $item['amount_of_ot'] ?? 0
                            }}</p>
                    </td>
                    <td class='border border-b-0 px-3 py-2 text-gray-500 text-xs'>
                        <p class="truncate text-right">@convertnorp($item['total_daily_salary'])</p>
                    </td>
                    <td class='border border-b-0 px-3 py-2 text-gray-500 text-xs'>
                        <p class="truncate text-right">
                            @if (isset($item['total_instalment_debt']))
                            @convertnorp($item['total_instalment_debt'])
                            @else
                            -
                            @endif
                        </p>
                    </td>
                    <td class='border border-b-0 px-3 py-2 text-gray-500 text-xs'>
                        <p class="truncate text-right">
                            @if (isset($item['amount_of_ot_pay']))
                            @convertnorp($item['amount_of_ot_pay'])
                            @else
                            -
                            @endif
                        </p>
                    </td>
                    <td class='border border-b-0 px-3 py-2 text-gray-500 text-xs'>
                        <p class="truncate text-right">
                            @if (isset($item['total_tbhn_u_libur']))
                            @convertnorp($item['total_tbhn_u_libur'])
                            @else
                            -
                            @endif
                        </p>
                    </td>
                    <td class='border border-b-0 px-3 py-2 text-gray-500 text-xs'>
                        <p class="truncate text-right">
                            @if (isset($item['total_position_extra_pay']))
                            @convertnorp($item['total_position_extra_pay'])
                            @else
                            -
                            @endif
                        </p>
                    </td>
                    <td class='border border-b-0 border-r-0 px-3 py-2 text-gray-500 text-xs'>
                        <p class="truncate text-right">@convertnorp($item['total'])</p>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</main>