@extends('layouts.app')
@section('title', 'Print | Payroll Report')
@section('content')
{{-- @if (empty($startdate_ot) || empty($enddate_ot) ||empty($startdate_work) ||empty($enddate_work) )
<div class="w-full h-screen flex items-center justify-center">
    <div class="flex flex-col items-center justify-center gap-4">
        <span>
            <x-icon icon="alert-triangle" width=50 height=50 viewBox="20 20" />
        </span>
        <div class="text-center">
            <p class="text-2xl font-semibold">Laporan tidak ada</p>
            <p>Karena anda tidak memberikan data-data berikut ini <br> atau format data salah</p>
        </div>
        <div class="h-5"></div>
        <form action="/print/payroll_report" method="get" class="flex flex-col items-center justify-center gap-4">
            <div class="flex flex-col gap-2.5 w-52">
                <div class="w-full flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-900 text-center">
                        <span class="text-xs text-red-600">*</span> Tanggal hari kerja <span
                            class="text-xs text-red-600">*</span>
                    </label>
                    <div class="">
                        <div class="flex-1">
                            {!! FormCustom::input('startdate_work', $startdate_work, [
                            'placeholder' => 'Tanggal awal',
                            'class' => 'date_input',
                            'readonly' => true,
                            'prefixiconname' => 'calendar',
                            ]) !!}
                        </div>
                        <div class="flex-1">
                            {!! FormCustom::input('enddate_work', $enddate_work, [
                            'placeholder' => 'Tanggal akhir',
                            'class' => 'date_input',
                            'readonly' => true,
                            'prefixiconname' => 'calendar',
                            ]) !!}
                        </div>
                    </div>
                </div>
                <div class="w-full flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-900 text-center">
                        <span class="text-xs text-red-600">*</span> Tanggal lembur <span
                            class="text-xs text-red-600">*</span>
                    </label>
                    <div class="">
                        <div class="flex-1">
                            {!! FormCustom::input('startdate_ot', $startdate_ot, [
                            'placeholder' => 'Tanggal awal',
                            'class' => 'date_input',
                            'readonly' => true,
                            'prefixiconname' => 'calendar',
                            ]) !!}
                        </div>
                        <div class="flex-1">
                            {!! FormCustom::input('enddate_ot', $enddate_ot, [
                            'placeholder' => 'Tanggal akhir',
                            'class' => 'date_input',
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
@else --}}
<div class="flex flex-col w-full p-4">
    <div class="w-full flex flex-col justify-center items-center text-center">
        <p class="text-sm font-medium uppercase">PT. KENCANA MAS MULIA</p>
        <p class="text-sm font-medium uppercase">DAFTAR UPAH PEGAWAI HARIAN</p>
    </div>
    <div class="h-4"></div>
    <div class="flex flex-col gap-10">
        @foreach ($datas as $data)
        <div>
            <div class="flex items-start justify-between pl-10 pr-20">
                <div class="border border-gray-900 pl-2 pr-6 py-0.5">
                    <p class="text-xs font-medium">
                        Bagian: Mesin
                    </p>
                </div>
                <div>
                    <div class="flex items-center gap-4 text-xs font-medium">
                        <div class="flex items-center justify-between w-[62px]">
                            <p>Hari Kerja</p>
                            <p>:</p>
                        </div>
                        <p>
                            {{-- {{ Carbon\Carbon::createFromFormat('Y-m-d', $start_date)->format('d/m/Y')}}
                            -
                            {{ Carbon\Carbon::createFromFormat('Y-m-d', $end_date)->format('d/m/Y')}} --}}
                        </p>
                    </div>
                    <div class="flex items-center gap-4 text-xs font-medium">
                        <div class="flex items-center justify-between w-[62px]">
                            <p>Lembur</p>
                            <p>:</p>
                        </div>
                        <p>
                            {{-- {{ Carbon\Carbon::createFromFormat('Y-m-d', $start_date)->format('d/m/Y')}}
                            -
                            {{ Carbon\Carbon::createFromFormat('Y-m-d', $end_date)->format('d/m/Y')}} --}}
                        </p>
                    </div>
                </div>
            </div>
            <div class="h-3"></div>
            <table class='table border-collapse w-full'>
                <thead class='border border-black'>
                    <tr class='border-t border-black'>
                        <th class='bg-white border border-black border-t-0 border-l-0 py-1 text-center w-10'
                            rowspan="2">
                            <p class="text-xs font-medium truncate">No.</p>
                        </th>
                        <th class='bg-white border border-black border-t-0 px-2 py-1 text-center'>
                            <p class="text-xs font-medium truncate">Nama</p>
                        </th>
                        @foreach ($data['range_dates'] as $item)
                        <th class="border border-black border-t-0 px-2 py-1 text-left w-[56px] min-w-[56px]">
                            <p class="text-xs font-medium truncate text-center">
                                {{$item['d']}}
                            </p>
                        </th>
                        @endforeach
                        <th class='bg-white border border-black border-t-0 px-2 py-1 text-left' rowspan="2">
                            <p class="text-xs font-medium truncate text-center">HK</p>
                        </th>
                        <th class='bg-white border border-black border-t-0 px-2 py-1 text-left' rowspan="2">
                            <p class="text-xs font-medium truncate text-center">JL</p>
                        </th>
                        <th class='bg-white border border-black border-t-0 border-r-0 px-2 py-1 text-center'
                            colspan="6">
                            <p class="text-xs font-medium truncate">Jumlah( Rupiah )</p>
                        </th>

                    </tr>
                    <tr class=''>
                        <th class='bg-white border border-black px-2 py-1 text-center'>
                            <p class="text-xs font-medium truncate">Pegawai</p>
                        </th>
                        @foreach ($data['range_dates'] as $item)
                        <th class='bg-white border border-black px-2 py-1 text-left w-[56px] min-w-[56px]'>
                            <div class="tooltip-custom">
                                <p class="text-xs font-medium truncate text-center">
                                    {{ $item['key'] }}
                                </p>
                                {{-- @if ($item['is_holiday'])
                                <div
                                    class="tooltip-custom-text border p-1 px-1.5 top-[-30px] rounded bg-white after:!border-t-gray-300 flex flex-col items-center">
                                    <p class="truncate text-[10px] font-normal">{{ $item['holiday_name'] }}</p>
                                </div>
                                @endif --}}
                            </div>
                        </th>
                        @endforeach
                        <th class='bg-white border border-black px-2 py-1 text-center'>
                            <p class="text-xs font-medium truncate">Gaji</p>
                        </th>
                        <th class='bg-white border border-black px-2 py-1 text-center'>
                            <p class="text-xs font-medium truncate">Kasbon</p>
                        </th>
                        <th class='bg-white border border-black px-2 py-1 text-left'>
                            <p class="text-xs font-medium truncate">Lembur</p>
                        </th>
                        <th class='bg-white border border-black px-2 py-1 text-center'>
                            <p class="text-xs font-medium truncate">Rbhn+U.Libur</p>
                        </th>
                        <th class='bg-white border border-black px-2 py-1 text-center'>
                            <p class="text-xs font-medium truncate">Jabatan</p>
                        </th>
                        <th class='bg-white border border-black border-r-0 px-2 py-1 text-center'>
                            <p class="text-xs font-medium truncate">TOTAL</p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['reports'] as $key => $item)
                    <tr class='border-black'>
                        <td class='border border-black px-2 py-2 text-xs text-right w-10'>
                            {{ $key +1 }}
                        </td>
                        <td class='border border-black px-2 py-2 text-xs'>
                            <p class="truncate">{{ $item['employee']['first_name'] ?? '-' }} {{
                                $item['employee']['last_name'] ?? ''}}
                            </p>
                        </td>
                        @foreach (($item['attendances'] ?? []) as $attendance)
                        <td class='border border-black px-2 py-2  text-left w-[56px] min-w-[56px]'>
                            {{-- <div
                                class="tooltip-custom flex justify-center relative {{ (!empty($item_report['timetable']['status']) && !$item_report['timetable']['status']['valid']) ? 'border-l-2 border-l-red-500 cursor-pointer' :'' }} w-full h-full">
                                <p
                                    class="text-xs truncate text-center {{ !empty($item_report['timetable']['is_holiday']) && $item_report['timetable']['is_holiday'] ? 'text-gray-300' : ' }} ">
                                    {{ $item_report['timetable']['atten_value_day'] }}
                                </p>
                                @if (!empty($item_report['timetable']['calculate_one_shift']))
                                <p class="absolute text-[8px] text-violet-600 right-[-6px] top-[-3px]">+{{
                                    $item_report['timetable']['calculate_one_shift'] }}</p>
                                @endif

                                @if (!empty($item_report['timetable']['status']) &&
                                !$item_report['timetable']['status']['valid'])
                                <div
                                    class="tooltip-custom-text border p-1 px-1.5 top-[-30px] rounded bg-white after:!border-t-gray-300 flex flex-col items-center">
                                    <p class="truncate text-[10px]">{{
                                        $item_report['timetable']['status']['noted'] ?? '-' }}</p>
                                </div>
                                @endif

                            </div> --}}
                        </td>
                        @endforeach
                        <td class='border border-black px-2 py-2 text-xs'>
                            <p class="truncate text-center">{{ $item['HK_value'] ?? 0 }}</p>
                        </td>
                        <td class='border border-black px-2 py-2 text-xs'>
                            <p class="truncate text-center">{{ $item['JL_value'] ?? 0
                                }}</p>
                        </td>
                        <td class='border border-black px-2 py-2 text-xs'>
                            <p class="truncate text-right">
                                @if (isset($item['salary_value']))
                                @convertnorp($item['salary_value'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border border-black px-2 py-2 text-xs'>
                            <p class="truncate text-right">
                                @if (isset($item['kasbon_value']))
                                @convertnorp($item['kasbon_value'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border border-black px-2 py-2 text-xs'>
                            <p class="truncate text-right">
                                @if (isset($item['overtime_value']))
                                @convertnorp($item['overtime_value'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border border-black px-2 py-2 text-xs'>
                            <p class="truncate text-right">
                                @if (isset($item['tbhn_u_libur_value']))
                                @convertnorp($item['tbhn_u_libur_value'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border border-black px-2 py-2 text-xs'>
                            <p class="truncate text-right">
                                @if (isset($item['position_value']))
                                @convertnorp($item['position_value'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border border-black  px-2 py-2 text-xs'>
                            <p class="truncate text-right">
                                @if (isset($item['total_value']))
                                @convertnorp($item['total_value'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                    </tr>
                    @endforeach
                    <tr class='border-black'>
                        <td colspan="{{ count($data['range_dates']) + 3 }}"></td>
                        <td class='border border-black px-2 py-2 text-xs'>
                            <p class="truncate text-center">{{ $item['HK_value'] ?? 0 }}</p>
                        </td>
                        <td class='border border-black px-2 py-2 text-xs'>
                            <p class="truncate text-right">
                                @if (isset($item['salary_value']))
                                @convertnorp($item['salary_value'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border border-black px-2 py-2 text-xs'>
                            <p class="truncate text-right">
                                @if (isset($item['kasbon_value']))
                                @convertnorp($item['kasbon_value'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border border-black px-2 py-2 text-xs'>
                            <p class="truncate text-right">
                                @if (isset($item['overtime_value']))
                                @convertnorp($item['overtime_value'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border border-black px-2 py-2 text-xs'>
                            <p class="truncate text-right">
                                @if (isset($item['tbhn_u_libur_value']))
                                @convertnorp($item['tbhn_u_libur_value'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border border-black px-2 py-2 text-xs'>
                            <p class="truncate text-right">
                                @if (isset($item['position_value']))
                                @convertnorp($item['position_value'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                        <td class='border border-black px-2 py-2 text-xs'>
                            <p class="truncate text-right">
                                @if (isset($item['total_value']))
                                @convertnorp($item['total_value'])
                                @else
                                -
                                @endif
                            </p>
                        </td>
                    </tr>
                    <tr class='border-black'>
                        <td colspan="{{ count($data['range_dates']) + 8 }}"></td>
                        <td colspan="2" class='border border-black px-2 py-2 text-xs bg-gray-100'>
                            <div class="flex items-center justify-between">
                                <p class="truncate text-right">Total: </p>
                                <p class="truncate text-right">
                                    @if (isset($data['grand_total_value']))
                                    @convertnorp($data['grand_total_value'])
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
{{-- @endif --}}
<script>
    window.addEventListener('DOMContentLoaded', (event) => {
     $('.date_input').daterangepicker({
            autoUpdateInput: false,
            locale: { format: 'YYYY-MM-DD', cancelLabel: 'Clear' },
            singleDatePicker: true,
            showDropdowns: true,
            minYear: 1945,
            drops: "auto",
            maxYear: parseInt(moment().format('YYYY'), 10)
        });

        $('.date_input').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
        });

        $('.date_input').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    });
</script>
@endsection