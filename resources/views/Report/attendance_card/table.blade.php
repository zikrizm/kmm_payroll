<div class="w-full overflow-auto overflow-y-hidden flex-1">
    <div class="grid gap-5 grid-cols-3 xl/max:grid-cols-2 lg/max:grid-cols-1 w-max items-start">
        @foreach ($datas as $data)
        @foreach ($data['attendance_reports'] as $item)
        <div class="flex flex-col gap-2.5 border rounded p-4 w-[375px]" style="min-width: 375px;">
            <header class="relative">
                <div>
                    <p class="font-bold text-gray-700 text-xl">KARTU ABSEN</p>
                    <p class="font-medium text-gray-500 text-xs ">{{ Auth::user()->business->name }}</p>
                </div>
                <div class="flex items-end absolute top-[5px] w-full">
                    <hr class="flex-1 border-[1.5px] bg-black rounded ">
                    <div class="w-14 h-14 bg-white mb-[-10px] rounded-full overflow-hidden">
                        @if (!empty($item['employee']['photo']))
                        <img class="w-full h-full object-cover"
                            src="{{config('constants.api_zkteco')}}{{ $item['employee']['photo'] }}" alt="">
                        @else
                        <img class="w-full h-full object-cover"
                            src="{{config('constants.api_zkteco')}}/files/nophoto.gif" alt="">
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
                            {{-- <p class="text-gray-500 text-xs text-middle mt-0.5">
                                {{ date('d-m-Y', strtotime($item['range_date']['start_time'])) }} -
                                {{ date('d-m-Y', strtotime($item['range_date']['end_time'])) }} --}}
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
                                {{-- {{ $item['employee']['department']['dept_name'] ?? '-' }} --}}
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
                            <p class="text-xs font-medium text-gray-500 text-center truncate">Lembur</p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($item['attendances'] as $key => $attendance)
                    <tr class='hover:bg-gray-50 border '>
                        <td
                            class="text-xs border text-center w-8 {{ $attendance['is_holiday'] ? 'text-red-500' : 'text-gray-500' }}">
                            {{ date('d', strtotime($attendance['date'])) }}
                        </td>
                        <td class='text-xs border text-gray-500 text-center w-12'>
                            @if (!empty($attendance['first_punch']))
                            {{ date('H:i', strtotime($attendance['first_punch'])) }}
                            @else
                            -
                            @endif
                        </td>
                        <td class='text-xs border text-gray-500 text-center w-14 '>
                            @if (!empty($attendance['last_punch']))
                            <div class="relative">
                                {{ date('H:i', strtotime($attendance['last_punch'])) }}
                                {{-- <span class="absolute text-[8px] text-violet-600 top-[-5px]">
                                    {{ $attendance['is_diff_day'] ? '+1' : '' }}
                                </span> --}}
                            </div>
                            @else
                            -
                            @endif
                        </td>
                        <td class='text-xs border text-gray-500 text-center'>
                            <div class="flex items-center justify-between gap-1">
                                <div class="flex justify-center text-center flex-1 overflow-hidden">
                                    @if (!empty($attendance['timetable']))
                                    <div class="flex relative">
                                        <p class="text-[10px] truncate">{{ $attendance['timetable']['name'] ?? '-' }}</p>
                                    </div>
                                    @else
                                    -
                                    @endif
                                </div>
                                <div class="flex justify-center bg-gray-100 text-gray-700">
                                    <p class="text-[9px]">{{ !empty($attendance['value_string']) ?
                                        '( '.$attendance['value_string'].' )' : '' }}</p>
                                </div>
                            </div>

                        </td>
                        <td class='text-xs border text-gray-500 text-center w-14'>
                            {{ $attendance['JL'] ?? '-' }}
                            {{-- @php
                            $overtime = ($item_report['timetable']['overtime'] ?? 0) +
                            ($item_report['timetable']['early_check_in'] ?? 0);
                            @endphp
                            {{ !empty($item_report['timetable']['per_day'])? $overtime: '-' }} --}}
                        </td>
                    </tr>
                    @endforeach
                    <tr class='hover:bg-gray-50'>
                        <td class='text-xs text-center'>
                        </td>
                        <td class='text-xs text-center'>
                        </td>
                        <td class='text-xs text-center'>
                        </td>
                        <td class='text-xs text-center'>
                        </td>
                        <td class='text-center'>
                            <div>
                                <div class="flex items-center">
                                    <p class="text-gray-500 flex-1 text-center text-[10px] border">HK</p>
                                    <p class="text-gray-500 flex-1 text-center text-[10px] border">JL</p>
                                </div>
                                <div class="flex items-center">
                                    <p class="text-gray-500 flex-1 text-center text-[10px]">
                                        {{ $item['HK_value'] ?? 0 }}
                                    </p>
                                    <p class="text-gray-500 flex-1 text-center text-[10px]">
                                        {{ ($item['JL_value'] ?? 0)}}</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endforeach
        @endforeach
    </div>
</div>