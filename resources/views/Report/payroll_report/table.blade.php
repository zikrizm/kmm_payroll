@forelse ($datas as $data)
<div class="">
    <div class="flex items-start justify-between pl-10 pr-20">
        <div class="border pl-2 pr-6 py-1">
            <p class="text-xs">
                Bagian: {{ $data['department']['dept_name'] }}
            </p>
        </div>
        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-4 text-xs">
                <div class="flex items-center justify-between w-[80px] ">
                    <p>Hari Kerja</p>
                    <p>:</p>
                </div>
                <p>
                    {{ $start_date_work_day->format('d/m/Y') }}
                    -
                    {{ $end_date_work_day->format('d/m/Y') }}
                </p>
            </div>
            <div class="flex items-center gap-4 text-xs">
                <div class="flex items-center justify-between w-[80px]">
                    <p>Lembur</p>
                    <p>:</p>
                </div>
                <p>
                    {{ $start_date_overtime->format('d/m/Y') }}
                    -
                    {{ $end_date_overtime->format('d/m/Y') }}
                </p>
            </div>
        </div>
    </div>
    <div class="h-3"></div>
    <main class='border border-gray-200 rounded-lg shadow-sm overflow-hidden'>
        <div class="w-full overflow-auto overflow-y-hidden">
            <table class='table border-collapse w-full'>
                <thead class='border border-gray-200'>
                    <tr class='border-t'>
                        <th class='border border-t-0 border-l-0 px-3 py-1 text-left' rowspan="2">
                            <p class="text-xs font-medium truncate">No.</p>
                        </th>
                        <th class='border border-t-0 px-3 py-1 text-center'>
                            <p class="text-xs font-medium truncate">Nama</p>
                        </th>
                        @foreach ($data['range_dates'] as $item)
                        <th class='border border-t-0 px-3 py-1 text-left w-[56px] min-w-[56px]'>
                            <p
                                class="text-xs font-medium truncate text-center {{ $item['is_holiday'] ? 'text-red-500' : '' }} ">
                                {{ $item['d'] }}
                            </p>
                        </th>
                        @endforeach
                        <th class='border border-t-0 px-3 py-1 text-left' rowspan="2">
                            <p class="text-xs font-medium truncate text-center">HK</p>
                        </th>
                        <th class='border border-t-0 px-3 py-1 text-left' rowspan="2">
                            <p class="text-xs font-medium truncate text-center">JL</p>
                        </th>
                        <th class='border border-t-0 border-r-0 px-3 py-1 text-center' colspan="7">
                            <p class="text-xs font-medium truncate">Jumlah( Rupiah )</p>
                        </th>

                    </tr>
                    <tr class='border-b-2'>
                        <th class='border px-3 py-1 text-center'>
                            <p class="text-xs font-medium truncate">Pegawai</p>
                        </th>
                        @foreach ($data['range_dates'] as $item)
                        <th class='border px-3 py-1 text-left w-[56px] min-w-[56px]'>
                            <p
                                class="text-xs font-medium truncate text-center {{ $item['is_holiday'] ? 'text-red-500' : '' }} ">
                                {{ $item['key'] }}
                            </p>
                        </th>
                        @endforeach
                        <th class='border px-3 py-1 text-center'>
                            <p class="text-xs font-medium truncate">Gaji</p>
                        </th>
                        <th class='border px-3 py-1 text-center'>
                            <p class="text-xs font-medium truncate">Kasbon</p>
                        </th>
                        <th class='border px-3 py-1 text-left'>
                            <p class="text-xs font-medium truncate">Lembur</p>
                        </th>
                        <th class='border px-3 py-1 text-center'>
                            <p class="text-xs font-medium truncate">Rbhn+U.Libur</p>
                        </th>
                        <th class='border px-3 py-1 text-center'>
                            <p class="text-xs font-medium truncate">Jabatan</p>
                        </th>
                        <th class='border border-r-0 px-3 py-1 text-center'>
                            <p class="text-xs font-medium truncate">TOTAL</p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['attendance_reports'] as $key => $item)
                    <tr class='hover:bg-gray-50 border-gray-200'>
                        <td class='border border-l-0 px-3 py-2 text-gray-500 text-xs text-right'>
                            {{ $key +1 }}
                        </td>
                        <td class='border px-3 py-2 text-gray-500 text-xs'>
                            <p class="truncate">
                                {{ $item['employee']['first_name'] ?? '-' }}
                                {{ $item['employee']['last_name'] ?? '' }}
                            </p>
                        </td>
                        @foreach ($item['attendances'] ?? [] as $attendance)
                        <td class='border px-3 py-2 text-left text-xs'>
                            <div class="flex justify-center relative w-full h-full">
                                <p
                                    class="runcate text-center {{ !empty($attendance['is_holiday']) ? 'text-red-500' : 'text-gray-500' }} ">
                                    {{ $attendance['value_string'] }}
                                </p>
                            </div>
                        </td>
                        @endforeach
                        <td class='border px-3 py-2 text-gray-500 text-xs'>
                            <p class="truncate text-center">{{ $item['HK_value'] ?? 0 }}</p>
                        </td>
                        <td class='border px-3 py-2 text-gray-500 text-xs'>
                            <p class="truncate text-center">{{ $item['JL_value'] ?? 0 }}</p>
                        </td>
                        <td class='border px-3 py-2 text-gray-500 text-xs'>
                            <p class="truncate text-right">
                                @if ( isset($item['employee']['daily_salary']))
                                @convertnorp($item['employee']['daily_salary'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border px-3 py-2 text-gray-500 text-xs'>
                            <p class="truncate text-right">
                                @if (isset($item['salary_pay_value']))
                                @convertnorp($item['salary_pay_value'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border px-3 py-2 text-gray-500 text-xs'>
                            <p class="truncate text-right">
                                @if (isset($item['kasbon_pay_value']))
                                @convertnorp($item['kasbon_pay_value'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border px-3 py-2 text-gray-500 text-xs'>
                            <p class="truncate text-right">
                                @if (isset($item['overtime_pay_value']))
                                @convertnorp($item['overtime_pay_value'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border px-3 py-2 text-gray-500 text-xs'>
                            <p class="truncate text-right">
                                @if (isset($item['tbhn_u_libur_pay_value']))
                                @convertnorp($item['tbhn_u_libur_pay_value'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border px-3 py-2 text-gray-500 text-xs'>
                            <p class="truncate text-right">
                                @if (isset($item['tbhn_u_position_pay_value']))
                                @convertnorp($item['tbhn_u_position_pay_value'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border border-r-0 px-3 py-2 text-gray-500 text-xs'>
                            <p class="truncate text-right">
                                @if (isset($item['total_pay_value']))
                                @convertnorp($item['total_pay_value'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                    </tr>
                    @endforeach
                    <tr class='border-black'>
                        <td colspan="{{ count($data['range_dates']) + 3 }}"></td>
                        <td class='border px-3 py-2 text-gray-500 text-xs'>
                            <p class="truncate text-center">{{ $data['total_HK_value'] ?? 0 }}</p>
                        </td>
                        <td class='border px-3 py-2 text-gray-500 text-xs'>
                            <p class="truncate text-right">
                                @if (isset($data['total_salary_pay_value']))
                                @convertnorp($data['total_salary_pay_value'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border px-3 py-2 text-gray-500 text-xs'>
                            <p class="truncate text-right">
                                @if (isset($data['total_kasbon_pay_value']))
                                @convertnorp($data['total_kasbon_pay_value'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border px-3 py-2 text-gray-500 text-xs'>
                            <p class="truncate text-right">
                                @if (isset($data['total_overtime_pay_value']))
                                @convertnorp($data['total_overtime_pay_value'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border px-3 py-2 text-gray-500 text-xs'>

                            <p class="truncate text-right">
                                @if (isset($data['total_tbhn_u_libur_pay_value']))
                                @convertnorp($data['total_tbhn_u_libur_pay_value'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border px-3 py-2 text-gray-500 text-xs'>
                            <p class="truncate text-right">
                                @if (isset($data['total_tbhn_u_position_pay_value']))
                                @convertnorp($data['total_tbhn_u_position_pay_value'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border px-3 py-2 text-gray-500 text-xs'>
                            <p class="truncate text-right">
                                @if (isset($data['grand_total_pay_value']))
                                @convertnorp($data['grand_total_pay_value'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                    </tr>
                    <tr class='border-black'>
                        <td colspan="{{ count($data['range_dates']) + 8 }}"></td>
                        <td colspan="2" class='border px-3 py-2 text-gray-500 text-xs bg-gray-200 grand-total-bg-color'>
                            <div class="flex items-center justify-between">
                                <p class="truncate text-right">Total: </p>
                                <p class="truncate text-right">
                                    @if (isset($data['grand_total_pay_value']))
                                    @convertnorp($data['grand_total_pay_value'])
                                    @else
                                    -
                                    @endif
                                </p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>
</div>
@empty
<div class="h-full w-full flex justify-center items-center">
    <div class="flex flex-col items-start justify-start gap-4">
        <span>
            <x-icon icon="alert-triangle" width=50 height=50 viewBox="20 20" />
        </span>
        <div class="text-left">
            <p class="text-2xl font-semibold">Laporan tidak ada</p>
            <p>Silahkan pilih bagian lainnya</p>
        </div>
    </div>
</div>
@endforelse