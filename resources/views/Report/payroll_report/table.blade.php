<main class='border border-gray-200 rounded-lg shadow-sm  overflow-hidden'>
    <div class="w-full overflow-auto overflow-y-hidden">
        <table class='table border-collapse w-full'>
            <thead class='border border-gray-200 bg-gray-50'>
                <tr class='border-t'>
                    <th class='border border-t-0 border-l-0 px-3 py-1 text-left cursor-pointer' rowspan="2">
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">No.</p>
                    </th>
                    <th class='border border-t-0 px-3 py-1 text-center cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Nama</p>
                    </th>
                    @foreach ($th_dates as $item)
                    {{-- @php
                    $isSunday = Carbon\Carbon::parse($item_report['date'])->isSunday();
                @endphp --}}
                    <th class='border border-t-0 px-3 py-1 text-left cursor-pointer'>
                        <p class="text-xs font-medium truncate cursor-pointer text-center {{ $item['is_holiday'] ? 'text-red-500' : 'text-gray-500' }}">
                            {{ date('d', strtotime($item['date'])) }}
                        </p>
                    </th>
                    @endforeach
                    <th class='border border-t-0 px-3 py-1 text-left cursor-pointer' rowspan="2">
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer text-center">HK</p>
                    </th>
                    <th class='border border-t-0 px-3 py-1 text-left cursor-pointer' rowspan="2">
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer text-center">JL</p>
                    </th>
                    <th class='border border-t-0 border-r-0 px-3 py-1 text-center cursor-pointer' colspan="6">
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Jumlah( Rupiah )</p>
                    </th>

                </tr>
                <tr class='border-b-2'>
                    <th class='border px-3 py-1 text-center cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Pegawai</p>
                    </th>
                    @foreach ($th_dates as $item)
                    <th class='border px-3 py-1 text-left cursor-pointer'>
                        <p class="text-xs font-medium truncate cursor-pointer text-center text-gray-500">
                            {{ $item['slug'] }}
                        </p>
                    </th>
                    @endforeach
                    <th class='border px-3 py-1 text-center cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Gaji</p>
                    </th>
                    <th class='border px-3 py-1 text-center cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Kasbon</p>
                    </th>
                    <th class='border px-3 py-1 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Lembur</p>
                    </th>
                    <th class='border px-3 py-1 text-center cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Rbhn+U.Libur</p>
                    </th>
                    <th class='border px-3 py-1 text-center cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Jabatan</p>
                    </th>
                    <th class='border border-r-0 px-3 py-1 text-center cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">TOTAL</p>
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
                    <td class='border border-b-0 px-3 py-2 text-left'>
                        <p class="text-xs text-gray-500 truncate text-center">
                            {{ $item_report['timetable']['calculate_atten_per_day'] }}
                            {{-- @php
                                $calculate_atten = 
                            @endphp --}}
                            {{-- @if (!empty($item_report['timetable']['per_day']))
                                {{ $item_report['timetable']['per_day'] }}
                            @endif --}}
                            {{-- @php
                            $overtime = ($item_report['timetable']['overtime'] ?? 0) +
                            ($item_report['timetable']['early_check_in'] ?? 0);
                            @endphp
                            {{ !is_null($item_report['timetable']['per_day'])? $overtime: '' }} --}}
                            {{-- @if (!empty($item_report['timetable']['per_day']) &&
                            $item_report['timetable']['per_day'] > 1)
                            {{ $item_report['timetable']['per_day']+($item_report['timetable']['overtime'] ?? 0) }}
                            @elseif (!empty($item_report['timetable']['per_day']) &&
                            $item_report['timetable']['per_day'] >= 1)
                            {{ $item_report['timetable']['overtime'] ?? 0 }}
                            @elseif (empty($item_report['timetable']['per_day']))
                            {{-- x --}}
                            {{-- @endif
                            {{ $item_report['timetable']['overtime']??'' }}
                            {{ $item_report['timetable']['early_check_in']??'' }} --}}
                            {{-- {{ ($item_report['timetable']['overtime'] ?? 0) + ($item_report['in'] ?? 0) }} --}}
                        </p>
                    </td>
                    @endforeach
                    <td class='border border-b-0 px-3 py-2 text-gray-500 text-xs'>
                        <p class="truncate text-center">{{ $item['amount_day'] ?? 0 }}</p>
                    </td>
                    <td class='border border-b-0 px-3 py-2 text-gray-500 text-xs'>
                        <p class="truncate text-center">{{ ($item['amount_of_ot'] ?? 0) + ($item['early_check_in'] ?? 0)
                            }}</p>
                    </td>
                    <td class='border border-b-0 px-3 py-2 text-gray-500 text-xs'>
                        <p class="truncate text-right">@convertnorp($item['daily_salary_total'])</p>
                    </td>
                    <td class='border border-b-0 px-3 py-2 text-gray-500 text-xs'>
                        <p class="truncate text-right">
                            @if ($item['instalment_debt_total'])
                            @convertnorp($item['instalment_debt_total'])
                            @else
                            -
                            @endif
                        </p>
                    </td>
                    <td class='border border-b-0 px-3 py-2 text-gray-500 text-xs'>
                        <p class="truncate text-right">
                            @if ($item['amout_of_ot_pay'])
                            @convertnorp($item['amout_of_ot_pay'])
                            @else
                            -
                            @endif
                        </p>
                    </td>
                    <td class='border border-b-0 px-3 py-2 text-gray-500 text-xs'>
                        <p class="truncate text-right">
                            @if ($item['tbhn_u_libur_total'])
                            @convertnorp($item['tbhn_u_libur_total'])
                            @else
                            -
                            @endif
                        </p>
                    </td>
                    <td class='border border-b-0 px-3 py-2 text-gray-500 text-xs'>
                        <p class="truncate text-right">
                            @if ($item['position_extra_pay'])
                            @convertnorp($item['position_extra_pay'])
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
        {{-- <footer class='flex justify-between items-center px-6 pt-3 pb-4'>
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
        </footer> --}}
    </div>
</main>