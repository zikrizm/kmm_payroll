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
                                {{ date('Y-m-d', strtotime($item['range_date']['start_time'])) }} -
                                {{ date('Y-m-d', strtotime($item['range_date']['end_time'])) }}
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
                    </tr>
                </thead>
                <tbody>
                    @foreach ($item['reports'] as $key => $item_report)
                    <tr class='hover:bg-gray-50 border '>
                        <td class=' text-xs border text-gray-500 text-center w-8'>
                            {{ date('d', strtotime($item_report['date'])) }}
                        </td>
                        <td class=' text-xs border text-gray-500 text-center w-12'>
                            @if (!empty($item_report['first_punch']))
                            {{ date('H:i', strtotime($item_report['first_punch'])) }}
                            @else
                            -
                            @endif
                        </td>
                        <td class=' text-xs border text-gray-500 text-center w-12'>
                            @if (!empty($item_report['last_punch']))
                            {{ date('H:i', strtotime($item_report['last_punch'])) }}
                            @else
                            -
                            @endif
                        </td>
                        <td class=' text-xs border text-gray-500 text-center'>
                            @if (!empty($item_report['timetable']))
                            <div class="flex justify-center">
                                <div class="flex items-center gap-1">
                                    <div class="flex relative w-max">
                                        <p>{{ $item_report['timetable']['name'] ?? '' }}</p>
                                        @if (!empty($item_report['timetable']['cross_day']))
                                        <p class="text-[9px] text-violet-600 mt-[-4px]">+{{
                                            $item_report['timetable']['cross_day'] ?? '' }}</p>
                                        @endif
                                    </div>
                                    @if ($item_report['timetable']['is_half_day'])
                                    <p class="text-[9px]">(1/2)</p>
                                    @else
                                    <p class="text-[9px]">{{ !empty($item_report['timetable']['per_day']) ?
                                        '( '.$item_report['timetable']['per_day'].' )' : '' }}</p>

                                    @endif
                                </div>
                            </div>
                            @else

                            @endif

                        </td>
                        <td class=' text-xs border text-gray-500 text-center w-14'>
                            @php
                            $overtime = ($item_report['timetable']['overtime'] ?? 0) +
                            ($item_report['timetable']['early_check_in'] ?? 0);
                            @endphp
                            {{ !empty($overtime) ? $overtime: '' }}
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
                                        {{-- {{ $item['in_count'] }} --}}
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