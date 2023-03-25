@extends('layouts.app')
@section('title', 'Print | Payroll Report')
@section('content')
@if (empty($start_date_work_day) ||
empty($end_date_work_day) ||
empty($start_date_overtime) ||
empty($end_date_overtime))
<div class="w-full h-screen flex items-center justify-center">
    <div class="flex flex-col items-start justify-start gap-4">
        <span>
            <x-icon icon="alert-triangle" width=50 height=50 viewBox="20 20" />
        </span>
        <div class="text-left">
            <p class="text-2xl font-semibold">Laporan tidak ada</p>
            <p>Karena anda tidak memberikan data-data berikut ini <br> atau format data salah</p>
        </div>
        <div class="h-5"></div>
        <form action="/print/payroll_report" method="get" class="flex flex-col items-start justify-center gap-4 w-80">
            <div class="flex flex-col gap-2.5 w-full">
                <div class="w-full flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-900">
                        Tanggal hari kerja <span class="text-[8px] text-red-600">*</span>
                    </label>
                    <div class="">
                        <input type="hidden" name="start_date_work_day">
                        <input type="hidden" name="end_date_work_day">
                        <input type="hidden" name="start_date_overtime">
                        <input type="hidden" name="end_date_overtime">
                        <div class="flex-1">
                            {!! FormCustom::input('', null, [
                            'placeholder' => 'Tanggal hari kerja',
                            'class' => 'date-input date-input-work-day',
                            'readonly' => true,
                            'prefixiconname' => 'calendar',
                            ]) !!}
                        </div>
                    </div>
                </div>
                <div class="w-full flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-900">
                        Tanggal hari lembur <span class="text-[8px] text-red-600">*</span>
                    </label>
                    <div class="">
                        <div class="flex-1">
                            {!! FormCustom::input('', null, [
                            'placeholder' => 'Tanggal hari lembur',
                            'class' => 'date-input date-input-overtime-day',
                            'readonly' => true,
                            'prefixiconname' => 'calendar',
                            ]) !!}
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="px-10 py-1.5 rounded-lg bg-purple-600 text-white ">
                Muat ulang
            </button>
        </form>
    </div>
</div>
@else
<style media="print" type="text/css">
    .grand-total-bg-color {
        background-color: rgb(229 231 235) !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
</style>
<div class="flex flex-col p-4 w-full h-screen overflow-auto">
    <div class="w-full flex flex-col justify-center items-center text-center">
        <p class="text-xs font-medium uppercase">PT. KENCANA MAS MULIA</p>
        <p class="text-xs font-medium uppercase">DAFTAR UPAH PEGAWAI HARIAN</p>
    </div>
    <div class="h-4"></div>
    <div class="flex flex-col gap-10 w-full">
        @foreach ($datas as $data)
        <div>
            <div class="flex items-start justify-between pl-10 pr-20">
                <div class="border border-gray-900 pl-2 pr-6 py-0.5">
                    <p class="text-[8px] font-medium">
                        Bagian: {{ $data['department']['dept_name'] }}
                    </p>
                </div>
                <div>
                    <div class="flex items-center gap-4 text-[8px] font-medium">
                        <div class="flex items-center justify-between w-[62px]">
                            <p>Hari Kerja</p>
                            <p>:</p>
                        </div>
                        <p>
                            {{ $start_date_work_day->format('d/m/Y') }}
                            -
                            {{ $end_date_work_day->format('d/m/Y') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-4 text-[8px] font-medium">
                        <div class="flex items-center justify-between w-[62px]">
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
            <table class='table border-collapse w-full'>
                <thead class='border border-black'>
                    <tr class='border-t border-black'>
                        <th class='bg-white border border-black border-t-0 border-l-0 py-1 text-center' rowspan="2">
                            <p class="text-[8px] font-medium truncate">No.</p>
                        </th>
                        <th class='bg-white border border-black border-t-0 px-1 py-0.5 text-center'>
                            <p class="text-[8px] font-medium truncate">Nama</p>
                        </th>
                        @foreach ($data['range_dates'] as $item)
                        <th class="border border-black border-t-0 px-1 py-0.5 text-left">
                            <p
                                class="text-[8px] font-medium truncate text-center {{ $item['is_holiday'] ? 'text-red-500' : '' }}">
                                {{ $item['d'] }}
                            </p>
                        </th>
                        @endforeach
                        <th class='bg-white border border-black border-t-0 px-1 py-0.5 text-left' rowspan="2">
                            <p class="text-[8px] font-medium truncate text-center">HK</p>
                        </th>
                        <th class='bg-white border border-black border-t-0 px-1 py-0.5 text-left' rowspan="2">
                            <p class="text-[8px] font-medium truncate text-center">JL</p>
                        </th>
                        <th class='bg-white border border-black border-t-0 border-r-0 px-1 py-0.5 text-center'
                            colspan="6">
                            <p class="text-[8px] font-medium truncate">Jumlah( Rupiah )</p>
                        </th>

                    </tr>
                    <tr class=''>
                        <th class='bg-white border border-black px-1 py-0.5 text-center'>
                            <p class="text-[8px] font-medium truncate">Pegawai</p>
                        </th>
                        @foreach ($data['range_dates'] as $item)
                        <th class='bg-white border border-black px-1 py-0.5 text-left'>
                                <p
                                    class="text-[8px] font-medium truncate text-center {{ $item['is_holiday'] ? 'text-red-500' : '' }}">
                                    {{ $item['key'] }}
                                </p>
                        </th>
                        @endforeach
                        <th class='bg-white border border-black ≈≈ text-center'>
                            <p class="text-[8px] font-medium truncate">Gaji</p>
                        </th>
                        <th class='bg-white border border-black px-1 py-0.5 text-center'>
                            <p class="text-[8px] font-medium truncate">Kasbon</p>
                        </th>
                        <th class='bg-white border border-black px-1 py-0.5 text-left'>
                            <p class="text-[8px] font-medium truncate">Lembur</p>
                        </th>
                        <th class='bg-white border border-black px-1 py-0.5 text-center'>
                            <p class="text-[8px] font-medium truncate">Rbhn+U.Libur</p>
                        </th>
                        <th class='bg-white border border-black px-1 py-0.5 text-center'>
                            <p class="text-[8px] font-medium truncate">Jabatan</p>
                        </th>
                        <th class='bg-white border border-black border-r-0 px-1 py-0.5 text-center'>
                            <p class="text-[8px] font-medium truncate">TOTAL</p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['attendance_reports'] as $key => $item)
                    <tr class='border-black'>
                        <td class='border border-black px-1 py-0.5 text-[8px] text-right'>
                            {{ $key + 1 }}
                        </td>
                        <td class='border border-black px-1 py-0.5 text-[8px]'>
                            <p class="truncate">
                                {{ $item['employee']['first_name'] ?? '-' }}
                                {{ $item['employee']['last_name'] ?? '' }}
                            </p>
                        </td>
                        @foreach ($item['attendances'] ?? [] as $attendance)
                        <td class='border border-black px-1 py-0.5 text-left'>
                            <div class="flex justify-center relative w-full h-full">
                                <p
                                    class="text-[8px] truncate text-center {{ !empty($attendance['is_holiday']) ? 'text-red-500' : '' }} ">
                                    {{ $attendance['value_html'] }}
                                </p>
                            </div>
                        </td>
                        @endforeach
                        <td class='border border-black px-1 py-0.5 text-[8px]'>
                            <p class="truncate text-center">{{ $item['HK_value'] ?? 0 }}</p>
                        </td>
                        <td class='border border-black px-1 py-0.5 text-[8px]'>
                            <p class="truncate text-center">{{ $item['JL_value'] ?? 0 }}</p>
                        </td>
                        <td class='border border-black px-1 py-0.5 text-[8px]'>
                            <p class="truncate text-right">
                                @if (isset($item['salary_pay_value']))
                                @convertnorp($item['salary_pay_value'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border border-black px-1 py-0.5 text-[8px]'>
                            <p class="truncate text-right">
                                @if (isset($item['kasbon_pay_value']))
                                @convertnorp($item['kasbon_pay_value'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border border-black px-1 py-0.5 text-[8px]'>
                            <p class="truncate text-right">
                                @if (isset($item['overtime_pay_value']))
                                @convertnorp($item['overtime_pay_value'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border border-black px-1 py-0.5 text-[8px]'>
                            <p class="truncate text-right">
                                @if (isset($item['tbhn_u_libur_pay_value']))
                                @convertnorp($item['tbhn_u_libur_pay_value'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border border-black px-1 py-0.5 text-[8px]'>
                            <p class="truncate text-right">
                                @if (isset($item['tbhn_u_position_pay_value']))
                                @convertnorp($item['tbhn_u_position_pay_value'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border border-black  px-1 py-0.5 text-[8px]'>
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
                        <td class='border border-black px-1 py-0.5 text-[8px]'>
                            <p class="truncate text-center">{{ $data['total_HK_value'] ?? 0 }}</p>
                        </td>
                        <td class='border border-black px-1 py-0.5 text-[8px]'>
                            <p class="truncate text-right">
                                @if (isset($data['total_salary_pay_value']))
                                @convertnorp($data['total_salary_pay_value'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border border-black px-1 py-0.5 text-[8px]'>
                            <p class="truncate text-right">
                                @if (isset($data['total_kasbon_pay_value']))
                                @convertnorp($data['total_kasbon_pay_value'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border border-black px-1 py-0.5 text-[8px]'>
                            <p class="truncate text-right">
                                @if (isset($data['total_overtime_pay_value']))
                                @convertnorp($data['total_overtime_pay_value'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border border-black px-1 py-0.5 text-[8px]'>
                            <p class="truncate text-right">
                                @if (isset($data['total_tbhn_u_libur_pay_value']))
                                @convertnorp($data['total_tbhn_u_libur_pay_value'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border border-black px-1 py-0.5 text-[8px]'>
                            <p class="truncate text-right">
                                @if (isset($data['total_tbhn_u_position_pay_value']))
                                @convertnorp($data['total_tbhn_u_position_pay_value'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border border-black px-1 py-0.5 text-[8px]'>
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
                        <td colspan="2"
                            class='border border-black px-1 py-0.5 text-[8px] bg-gray-200 grand-total-bg-color'>
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
        @endforeach
    </div>
</div>
@endif
<script>
    window.addEventListener('DOMContentLoaded', (event) => {
            $('.date-input').daterangepicker({
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                },
                autoUpdateInput: false,
                alwaysShowCalendars: true,
                showCustomRangeLabel: false,
                showDropdowns: true,
                minYear: 2000,
                drops: "auto",
                maxYear: parseInt(moment().format('YYYY'), 10)
            }, function(start, end, label) {
                console.log($(this.element).val())
            });

            $('.date-input-work-day').on('apply.daterangepicker', function(ev, picker) {
                $('input[name="start_date_work_day"]').val(picker.startDate.format('DD-MM-YYYY'));
                $('input[name="end_date_work_day"]').val(picker.endDate.format('DD-MM-YYYY'));
                $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format(
                    'DD-MM-YYYY'));
            });

            $('.date-input-overtime-day').on('apply.daterangepicker', function(ev, picker) {
                $('input[name="start_date_overtime"]').val(picker.startDate.format('DD-MM-YYYY'));
                $('input[name="end_date_overtime"]').val(picker.endDate.format('DD-MM-YYYY'));
                $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format(
                    'DD-MM-YYYY'));
            });
            $('.date_input').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
        });
</script>
@endsection