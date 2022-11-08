<div class="w-full overflow-auto overflow-y-hidden flex-1">
    <div class="grid gap-5 grid-cols-1 items-start">
        @foreach ($attendance_reports as $item)
        <div class="flex flex-col gap-2.5 border rounded p-4 w-max">
            <header class="relative">
                <div>
                    <p class="font-bold text-gray-700 text-xl">KARTU ABSEN</p>
                    <p class="font-medium text-gray-500 text-xs ">{{ Auth::user()->business->name }}</p>
                </div>
                <div class="flex items-end absolute top-[5px] w-full">
                    <hr class="flex-1 border-[1.5px] bg-black rounded ">
                    <div class="w-14 h-14 bg-white mb-[-10px] rounded-full overflow-hidden">
                        @if (!empty($item['employee']['photo']))
                        <img class="w-full h-full object-cover" src="@zkPhoto({{ $item['employee']['photo'] }})" alt="">
                        @else
                        <img class="w-full h-full object-cover" src="@zkPhoto(files/nophoto.gif)" alt="">
                        @endif
                    </div>
                    <hr class="w-10 border-[1.5px] bg-black rounded ">
                </div>
            </header>
            <div class="flex flex-col mt-4">
                <table class='table border-collapse w-full '>
                    <tr class="align-middle">
                        <td class="w-14 text-gray-500">
                            <table class='table border-collapse w-full '>
                                <tr class="align-middle">
                                    <td class="w-6 text-gray-500">
                                        <x-icon icon="calendar" width=14 height=14 viewBox="20 20" />
                                    </td>
                                    <td class="align-middle">
                                        <p class="text-gray-500 text-xs text-middle mt-0.5 pl-0.5">
                                            Tanggal&nbsp;&nbsp;:</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td class="align-middle">
                            <p class="text-gray-500 text-xs text-middle mt-0.5">
                                {{ date('d-m-Y', strtotime($item['range_date']['start_time'])) }} -
                                {{ date('d-m-Y', strtotime($item['range_date']['end_time'])) }}
                        </td>
                    </tr>
                </table>
                <table class='table border-collapse w-full '>
                    <tr class="align-middle">
                        <td class="w-14 text-gray-500">
                            <table class='table border-collapse w-full '>
                                <tr class="align-middle">
                                    <td class="w-6 text-gray-500">
                                        <x-icon icon="user" width=14 height=14 viewBox="20 20" />
                                    </td>
                                    <td class="align-middle">
                                        <p class="text-gray-500 text-xs text-middle mt-0.5 pl-0.5">
                                            Nama&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td class="align-middle">
                            <p class="text-gray-500 text-xs text-middle mt-0.5">
                                {{ $item['employee']['first_name'] }}
                                {{ $item['employee']['last_name'] }}
                            </p>
                        </td>
                    </tr>
                </table>
                <table class='table border-collapse w-full '>
                    <tr class="align-middle">
                        <td class="w-14 text-gray-500">
                            <table class='table border-collapse w-full '>
                                <tr class="align-middle">
                                    <td class="w-6 text-gray-500">
                                        <x-icon icon="briefcase" width=14 height=14 viewBox="20 20" />
                                    </td>
                                    <td class="align-middle">
                                        <p class="text-gray-500 text-xs text-middle mt-0.5 pl-0.5">
                                            Bagian&nbsp;&nbsp;&nbsp;&nbsp;:</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td class="align-middle">
                            <p class="text-gray-500 text-xs text-middle mt-0.5">
                                {{ $item['employee']['department']['dept_name'] ?? '-' }}
                            </p>
                        </td>
                    </tr>
                </table>
                <table class='table border-collapse w-full '>
                    <tr class="align-middle">
                        <td class="w-14 text-gray-500">
                            <table class='table border-collapse w-full '>
                                <tr class="align-middle">
                                    <td class="w-6 text-gray-500">
                                        <x-icon icon="employee-position" width=14 height=14 viewBox="20 20" />
                                    </td>
                                    <td class="align-middle">
                                        <p class="text-gray-500 text-xs text-middle mt-0.5 pl-0.5">
                                            Jabatan&nbsp;&nbsp;:</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td class="align-middle">
                            <p class="text-gray-500 text-xs text-middle mt-0.5">
                                @if (!empty($item['employee']))
                                {{ implode(', ', array_column($item['employee']['position'], 'position_name')) }}
                                @else
                                -
                                @endif
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
            <table class='table border-collapse w-full border '>
                <thead class=''>
                    <tr class='border border-2 '>
                        <th class='text-left border '>
                            <p class="text-xs font-medium text-gray-500 text-center truncate">Tgl</p>
                        </th>
                        <th class='text-left border '>
                            <p class="text-xs font-medium text-gray-500 text-center truncate">Masuk</p>
                        </th>
                        <th class='text-left border '>
                            <p class="text-xs font-medium text-gray-500 text-center truncate">Keluar</p>
                        </th>
                        <th class='text-left border '>
                            <p class="text-xs font-medium text-gray-500 text-center truncate">Shift</p>
                        </th>
                        <th class='text-left border '>
                            <p class="text-xs font-medium text-gray-500 text-center truncate px-1.5">Status Operasional
                            </p>
                        </th>
                        <th class='text-left border '>
                            <p class="text-xs font-medium text-gray-500 text-center truncate">Lembur</p>
                        </th>
                        <th class='text-left border '>
                            <p class="text-xs px-1.5 font-medium text-gray-500 text-center truncate">Gaji</p>
                        </th>
                        <th class='text-left border '>
                            <p class="text-xs px-1.5 font-medium text-gray-500 text-center truncate">Lembur</p>
                        </th>
                        <th class='text-left border '>
                            <p class="text-xs px-1.5 font-medium text-gray-500 text-center truncate">Tbhn+U.Libur</p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($item['reports'] as $key => $item_report)
                    <tr class='hover:bg-gray-50 border '>
                        @php
                        $isSunday = Carbon\Carbon::parse($item_report['date'])->isSunday();
                        @endphp
                        <td
                            class="text-xs border text-center w-8 {{ $isSunday || !empty($item_report['holidays_by_date']) ? 'text-red-500' : 'text-gray-500' }}">
                            {{ date('d', strtotime($item_report['date'])) }}
                        </td>
                        <td class='text-xs border text-gray-500 text-center w-14'>
                            @if (!empty($item_report['first_punch']))
                            {{ date('H:i', strtotime($item_report['first_punch'])) }}
                            @else
                            -
                            @endif
                        </td>
                        <td class='text-xs border text-gray-500 text-center w-14 
                            {{ $item_report["is_less_than_time"]? ' text-red-500' : '' }}'>
                            @if (!empty($item_report['last_punch']))
                            <div class="relative">
                                {{ date('H:i', strtotime($item_report['last_punch'])) }}
                                <span class="absolute text-[10px] text-violet-600 top-[-5px]">
                                    {!! $item_report['is_diff_day'] ? '&#8800;' : '' !!}
                                </span>
                            </div>
                            @else
                            -
                            @endif
                        </td>
                        <td class='text-xs border text-gray-500 text-center w-[150px]'>
                            @if (!empty($item_report['timetable']))
                            <div class="flex items-center justify-between gap-1">
                                <div class="flex justify-center flex-1">
                                    <div class="flex relative w-max">
                                        <p class="text-[10px]">{{ $item_report['timetable']['name'] ?? '-' }}</p>
                                        @if (!empty($item_report['timetable']['cross_day']))
                                        <p class="text-[9px] text-violet-600 mt-[-4px]">+{{
                                            $item_report['timetable']['cross_day'] ?? '' }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="w-7 pl-0.5 flex justify-center bg-gray-100 text-gray-700">
                                    <p class="text-[9px]">{{ !empty($item_report['timetable']['per_day']) ?
                                        '( '.$item_report['timetable']['per_day'].' )' : '' }}</p>
                                </div>
                            </div>
                            @else
                            -
                            @endif

                        </td>

                        <td class='text-xs border text-center '>
                            @if (!empty($item_report['timetable']['status']))
                            <div class="flex items-center justify-center gap-1">
                                @if ($item_report['timetable']['status']['slug'] == 'LB')
                                <p class="text-gray-500">LB</p>
                                @endif
                                @if ($item_report['timetable']['status']['slug'] == 'not-setting')
                                <p class="text-blue-500">!</p>
                                @endif
                                @if ($item_report['timetable']['status']['slug'] == 'not-allowed')
                                <x-icon icon="x" class="text-red-500" width=12 height=12 viewBox="20 20" />
                                @endif
                                @if ($item_report['timetable']['status']['slug'] == 'check')
                                <x-icon icon="check" class="text-green-600" width=12 height=12 viewBox="20 20" />
                                @endif
                                @if ($item_report['timetable']['status']['slug'] == 'plusmn')
                                <p class="text-gray-500">{{ $item_report['timetable']['status']['value'] }}</p>
                                @endif
                            </div>
                            @else
                            @endif

                        </td>
                        <td class='text-xs border text-gray-500 text-center w-14'>
                            @php
                            $overtime = ($item_report['timetable']['overtime'] ?? 0) +
                            ($item_report['timetable']['early_check_in'] ?? 0);
                            @endphp
                            {{ !empty($item_report['timetable']['per_day'])? $overtime: '-' }}
                        </td>
                        <td class='text-[10px] border text-gray-500 text-center'>
                            <div class="flex justify-end">
                                <div class="w-max px-1.5">@if ($item_report['timetable']['daily_salary_per_day'])
                                    @convertnorp($item_report['timetable']['daily_salary_per_day'])
                                    @else
                                    -
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class='text-[10px] border text-gray-500 text-center'>
                            <div class="flex justify-end">
                                <div class="w-max px-1.5">@if ($item_report['timetable']['total_overtime_pay_per_day'])
                                    @convertnorp($item_report['timetable']['total_overtime_pay_per_day'])
                                    @else
                                    -
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class='text-[10px] border text-gray-500 text-center'>
                            <div class="flex justify-end">
                                <div class="w-max px-1.5">@if ($item_report['timetable']['tbhn_u_libur'])
                                    @convertnorp($item_report['timetable']['tbhn_u_libur'])
                                    @else
                                    -
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    <tr class='hover:bg-gray-50'>
                        <td class='bg-gray-100 border' colspan="3">
                            <p class="text-gray-700 text-center text-[10px]">
                                TOTAL
                            </p>
                        </td>
                        <td class=''>
                            <div class="flex justify-end">
                                <div class="w-7 pl-0.5 flex justify-center bg-gray-100 text-gray-700">
                                    <p class="text-[9px]">( {{ $item['amount_day'] ?? 0 }} )</p>
                                </div>
                            </div>
                        </td>
                        <td class='text-xs text-center'>
                        </td>
                        <td class='text-center'>
                            <p class="text-gray-500 flex-1 text-center text-[10px]">
                                {{ $item['amount_of_ot'] ?? 0 }}</p>
                        </td>
                        <td>
                            <p class="text-gray-500 flex-1 text-right px-1.5 text-[10px]">
                                @convertnorp($item['daily_salary_total'] ?? 0)</p>
                        </td>
                        <td>
                            <p class="text-gray-500 flex-1 text-right px-1.5 text-[10px]">
                                @convertnorp($item['amout_of_ot_pay'] ?? 0)</p>
                        </td>
                        <td>
                            <p class="text-gray-500 flex-1 text-right px-1.5 text-[10px]">
                                @convertnorp($item['tbhn_u_libur_total'] ?? 0)</p>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div>
                <div class="flex items-center gap-1">
                    <p class="text-xs text-gray-500 font-medium flex items-center">Cicilan kasbon&nbsp;&nbsp;: </p>
                    <p class="text-gray-700 text-xs">@convertnorp($item['instalment_debt_total'])</p>
                </div>
                <div class="flex items-center gap-1">
                    <p class="text-xs text-gray-500 font-medium flex items-center">Upah jabatan&nbsp;&nbsp;&nbsp;&nbsp;:
                    </p>
                    <p class="text-gray-700 text-xs">@convertnorp($item['position_extra_pay'])</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>