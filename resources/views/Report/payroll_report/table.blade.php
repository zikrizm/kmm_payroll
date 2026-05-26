@forelse ($result['departments'] as $department)
<div class="">
    <div class="flex items-start justify-between pl-10 pr-20">
        <div class="border pl-2 pr-6 py-1">
            <p class="text-xs">
                Bagian: {{ $department['department']['dept_name'] ?? '-' }}
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
    <div class="h-2"></div>
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
                        @foreach ($result['range_dates'] as $item)
                        <th class='border border-t-0 px-3 py-1 text-left w-[56px] min-w-[56px]'>
                            <p
                                class="text-xs font-medium truncate text-center {{ $item['holiday']['status'] ? 'text-red-500' : '' }} ">
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
                        <th class='border border-t-0 border-r-0 px-3 py-1 text-center' colspan="6">
                            <p class="text-xs font-medium truncate">Jumlah( Rupiah )</p>
                        </th>

                    </tr>
                    <tr class='border-b-2'>
                        <th class='border px-3 py-1 text-center'>
                            <p class="text-xs font-medium truncate">Pegawai</p>
                        </th>
                        @foreach ($result['range_dates'] as $item)
                        <th class='border px-3 py-1 text-left w-[56px] min-w-[56px]'>
                            <p
                                class="text-xs font-medium truncate text-center {{ $item['holiday']['status'] ? 'text-red-500' : '' }} ">
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
                            <p class="text-xs font-medium truncate">Tbhn+U.Libur</p>
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
                    @foreach ($department['employees'] as $key => $employee)
                    <tr class='hover:bg-gray-50 border-gray-200'>
                        <td class='border border-l-0 px-3 py-2 text-gray-500 text-xs text-right'>
                            {{ $key +1 }}
                        </td>
                        <td class='border px-3 py-2 text-gray-500 text-xs'>
                            <p class="truncate">
                                {{ $employee['employee']['first_name'] ?? '-' }}
                                {{ $employee['employee']['last_name'] ?? '' }}
                            </p>
                        </td>
                        @foreach ($employee['attendances'] ?? [] as $attendance)
                        <td class='border px-3 py-2 text-left text-xs'>
                            <div class="flex justify-center relative w-full h-full">
                                <p
                                    class="runcate text-center {{$attendance['total_shifted_overtime'] > 0 ? 'font-bold': ''}} {{ !empty($attendance['is_holiday']) ? 'text-red-500' : 'text-gray-500' }} ">
                                    {{ $attendance['text_value'] }}
                                </p>
                            </div>
                        </td>
                        @endforeach
                        <td class='border px-3 py-2 text-gray-500 text-xs'>
                            <p class="truncate text-center">{{ $employee['total_hk'] ?? 0 }}</p>
                        </td>
                        <td class='border px-3 py-2 text-gray-500 text-xs'>
                            <p class="truncate text-center">{{ $employee['total_jl'] ?? 0 }}</p>
                        </td>
                        <td class='border px-3 py-2 text-gray-500 text-xs'>
                            <p class="truncate text-right">
                                @if (isset($employee['total_salary']))
                                @convertnorp($employee['total_salary'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border px-3 py-2 text-gray-500 text-xs'>
                            <p class="truncate text-right">
                                @if (isset($employee['total_loan_paid_for_payroll']))
                                -@convertnorp($employee['total_loan_paid_for_payroll']) <span class="font-semibold">({{ $employee['loan_installment_count_for_payroll'] ?? '' }})</span>
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border px-3 py-2 text-gray-500 text-xs'>
                            <p class="truncate text-right">
                                @if (isset($employee['total_overtime']))
                                @convertnorp($employee['total_overtime'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border px-3 py-2 text-gray-500 text-xs'>
                            <p class="truncate text-right">
                                @if (isset($employee['total_tbhn_plus_u_libur']))
                                @convertnorp($employee['total_tbhn_plus_u_libur'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border px-3 py-2 text-gray-500 text-xs'>
                            <p class="truncate text-right">
                                @if (isset($employee['total_job_bonus']))
                                @convertnorp($employee['total_job_bonus'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border border-r-0 px-3 py-2 text-gray-500 text-xs'>
                            <p class="truncate text-right">
                                @if (isset($employee['final_total_for_payroll']))
                                @convertnorp($employee['final_total_for_payroll'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                    </tr>
                    @endforeach
                    <tr class='border-black'>
                        <td colspan="{{ count($result['range_dates']) + 2 }}"></td>
                        <td class='border px-3 py-2 text-gray-500 text-xs'>
                            <p class="truncate text-center">{{ $department['total_hk'] ?? 0 }}</p>
                        </td>
                        <td class='border px-3 py-2 text-gray-500 text-xs'>
                            <p class="truncate text-center">{{ $department['total_jl'] ?? 0 }}</p>
                        </td>
                        <td class='border px-3 py-2 text-gray-500 text-xs'>
                            <p class="truncate text-right">
                                @if (isset($department['total_salary']))
                                @convertnorp($department['total_salary'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border px-3 py-2 text-gray-500 text-xs'>
                            <p class="truncate text-right">
                                @if (isset($department['total_loan_paid_for_payroll']))
                                @convertnorp($department['total_loan_paid_for_payroll'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border px-3 py-2 text-gray-500 text-xs'>
                            <p class="truncate text-right">
                                @if (isset($department['total_overtime']))
                                @convertnorp($department['total_overtime'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border px-3 py-2 text-gray-500 text-xs'>

                            <p class="truncate text-right">
                                @if (isset($department['total_tbhn_plus_u_libur']))
                                @convertnorp($department['total_tbhn_plus_u_libur'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border px-3 py-2 text-gray-500 text-xs'>
                            <p class="truncate text-right">
                                @if (isset($department['total_job_bonus']))
                                @convertnorp($department['total_job_bonus'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border px-3 py-2 text-gray-500 text-xs'>
                            <p class="truncate text-right">
                                @if (isset($department['final_total_for_payroll']))
                                @convertnorp($department['final_total_for_payroll'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                    </tr>
                    <tr class='border-black'>
                        <td colspan="{{ count($result['range_dates']) + 8 }}"></td>
                        <td colspan="2" class='border px-3 py-2 text-gray-500 text-xs bg-gray-200 grand-total-bg-color'>
                            <div class="flex items-center justify-between">
                                <p class="truncate text-right">Total: </p>
                                <p class="truncate text-right">
                                    @if (isset($department['final_total_for_payroll']))
                                    @convertnorp($department['final_total_for_payroll'])
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
            <p>Silahkan pilih bagian terlebih dahulu</p>
        </div>
    </div>
</div>
@endforelse