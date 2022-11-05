<div class="w-full overflow-auto overflow-y-hidden flex-1">
    <div class="grid gap-5 grid-cols-3 xl/max:grid-cols-2 lg/max:grid-cols-1 w-max items-start">
        @foreach ($attendance_reports as $item)
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
                        <th class='text-left border '>
                            <p class="text-xs font-medium text-gray-500 text-center truncate">Status</p>
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
                            class="text-xs border text-center w-8 {{ $isSunday || !empty($item_report['holiday']) ? 'text-red-500' : 'text-gray-500' }}">
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
                            {{ $item_report["is_lessthan_punch"]? ' text-red-500' : '' }}'>
                            @if (!empty($item_report['last_punch']))
                            <div class="relative">
                                {{ date('H:i', strtotime($item_report['last_punch'])) }}
                                <span class="absolute text-[8px] text-violet-600 top-[-5px]">
                                    {{ $item_report['is_diff_day'] ? '+1' : '' }}
                                </span>
                            </div>
                            @else
                            -
                            @endif
                        </td>
                        <td class='text-xs border text-gray-500 text-center'>
                            <div class="w-full flex justify-center">
                                @if (!empty($item_report['timetable']))
                                <div class="w-10 pl-0.5 flex justify-center text-gray-700">
                                    <p class="text-[9px]">{{ !empty($item_report['timetable']['per_day']) ?
                                        '( '.$item_report['timetable']['per_day'].' )' : '' }}</p>
                                    @if (!empty($item_report['timetable']['cross_day']))
                                    <p class="text-[9px] text-violet-600 mt-[-4px]">+{{
                                        $item_report['timetable']['cross_day'] ?? '' }}</p>
                                    @endif
                                </div>
                                @else
                                -
                                @endif
                            </div>
                        </td>
                        <td class='text-xs border text-gray-500 text-center w-14'>
                            @php
                            $overtime = ($item_report['timetable']['overtime'] ?? 0) +
                            ($item_report['timetable']['early_check_in'] ?? 0);
                            @endphp
                            {{ !empty($item_report['timetable']['per_day'])? $overtime: '-' }}
                        </td>
                        <td class='text-xs border text-center w-12'>
                            @if (!empty($item_report['timetable']['status']))
                            <div class="flex items-center justify-center gap-1">
                                @if ($item_report['timetable']['status']['slug'] == 'LB')
                                <p class="text-gray-500">LB</p>
                                @endif
                                @if ($item_report['timetable']['status']['slug'] == 'not-allowed')
                                <x-icon icon="x-circle" class="text-red-500" width=12 height=12 viewBox="20 20" />
                                @endif
                                @if ($item_report['timetable']['status']['slug'] == 'check')
                                <x-icon icon="check-circle" class="text-green-600" width=12 height=12 viewBox="20 20" />
                                @endif
                                @if ($item_report['timetable']['status']['slug'] == 'plusmn')
                                <p class="text-gray-500">{{ $item_report['timetable']['status']['value'] }}</p>
                                @endif
                            </div>
                            @else
                            @endif

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
                                        {{ $item['amount_day'] ?? 0 }}
                                    </p>
                                    <p class="text-gray-500 flex-1 text-center text-[10px]">
                                        {{ ($item['amount_of_ot'] ?? 0) + ($item['early_check_in'] ?? 0)}}</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endforeach
    </div>
</div>