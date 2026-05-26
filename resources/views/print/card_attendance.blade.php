@extends('layouts.app')
@section('title', 'Print | Card Attendance')
@section('content')
    <div class="flex flex-col items-center gap-8 w-full h-screen overflow-auto py-8">
        <div class="w-full flex justify-center items-center">
            <button type="button" onclick="print()"
                class="text-white shadow bg-violet-600 hover:bg-violet-700 focus:ring-2 focus:ring-violet-700 font-medium rounded-lg xs/max:rounded-md text-sm xs/max:text-xs inline-flex items-center xs/max:px-4 px-6 xs/max:py-1.5 py-2 text-center">Cetak</button>
        </div>
        <div id="print-card-attendance" class="w-max">
            <div class="grid gap-5 grid-cols-2">
                @foreach ($result['departments'] as $department)
                    @foreach ($department['employees'] as $employee)
                        <div class="flex flex-col gap-2.5 border rounded p-4 w-[375px]" style="min-width: 375px;">
                            <header class="relative">
                                <div>
                                    <p class="font-bold text-gray-700 text-xl">KARTU ABSEN</p>
                                    <p class="font-medium text-gray-500 text-xs ">{{ Auth::user()->business->name }}</p>
                                </div>
                                <div class="flex items-end absolute top-[5px] w-full">
                                    <hr class="flex-1 border-[1.5px] bg-black rounded ">
                                    <div class="w-14 h-14 bg-white mb-[-10px] rounded-full overflow-hidden">
                                        <x-employee-photo :photo="$employee['employee']['photo'] ?? null" />
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
                                                {{ date('d-m-Y', strtotime($result['start_date_work_day'])) }} -
                                                {{ date('d-m-Y', strtotime($result['end_date_work_day'])) }}
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
                                                {{ $employee['employee']['first_name'] }}
                                                {{ $employee['employee']['last_name'] }}
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
                                                {{ $department['department']['dept_name'] ?? '-' }}
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
                                    @foreach ($employee['attendances'] as $attendance)
                                        @forelse ($attendance['shifts'] as $key => $shift)
                                            <tr class='hover:bg-gray-50 border '>
                                                @if ($key == 0)
                                                    <td rowspan="{{ count($attendance['shifts']) }}"
                                                        class="text-xs border text-center w-8 {{ $attendance['is_holiday'] ? 'text-red-500' : 'text-gray-500' }}">
                                                        {{ date('d', strtotime($attendance['date'])) }}
                                                    </td>
                                                @endif
                                                <td class='text-xs border text-gray-500 text-center w-12'>
                                                    @if (!empty($shift['working']['start_punch']))
                                                        {{ date('H:i', strtotime($shift['working']['start_punch'])) }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class='text-xs border text-gray-500 text-center w-14 '>
                                                    @if (!empty($shift['working']['end_punch']))
                                                        <div class="relative">
                                                            {{ date('H:i', strtotime($shift['working']['end_punch'])) }}
                                                        </div>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class='text-xs border text-gray-500 text-center'>
                                                    <div class="relative flex items-center justify-between gap-1">
                                                        <div class="flex justify-center text-center flex-1 overflow-hidden">
                                                            @if (!empty($shift['timetable']))
                                                                <p class="text-[10px] truncate">
                                                                    {{ $shift['timetable']['name'] ?? '-' }}</p>
                                                            @else
                                                                -
                                                            @endif
                                                        </div>
                                                        <div class="absolute right-0 flex items-center bg-white pl-2">
                                                            @if (!empty($shift['total_hk']))
                                                                <p class="text-[10px] truncate">
                                                                    ({{ $shift['total_hk'] ?? '-' }})
                                                                </p>
                                                            @endif
                                                            @if ($key == 0 && !empty($attendance['total_hk_holiday']))
                                                                <p class="text-[10px] text-red-500 truncate">
                                                                    +({{ $attendance['total_hk_holiday'] ?? '-' }})
                                                                </p>
                                                            @endif
                                                        </div>
                                                    </div>

                                                </td>
                                                <td class='text-xs border text-gray-500 text-center w-14'>
                                                    {{ $shift['total_jl'] ?? '-' }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr class='hover:bg-gray-50 border '>
                                                <td
                                                    class="text-xs border text-center w-8 {{ $attendance['is_holiday'] ? 'text-red-500' : 'text-gray-500' }}">
                                                    {{ date('d', strtotime($attendance['date'])) }}
                                                </td>
                                                <td class='text-xs border text-gray-500 text-center w-12'>
                                                    -
                                                </td>
                                                <td class='text-xs border text-gray-500 text-center w-14 '>
                                                    -
                                                </td>
                                                <td class='text-xs border text-gray-500 text-center'>
                                                    <div class="relative flex items-center justify-end gap-1">
                                                        @if (!empty($attendance['total_hk']))
                                                            <p class="text-[10px] truncate">
                                                                ({{ $attendance['total_hk'] ?? '-' }})
                                                            </p>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class='text-xs border text-gray-500 text-center w-14'>
                                                    -
                                                </td>
                                            </tr>
                                        @endforelse
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
                                                        {{ $employee['total_hk'] ?? 0 }}
                                                    </p>
                                                    <p class="text-gray-500 flex-1 text-center text-[10px]">
                                                        {{ $employee['total_jl'] ?? 0 }}</p>
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
    </div>
    <script>
        function print(params) {
            var originalContents, popupWin, printContents;
            return printContents = document.getElementById(
                    "print-card-attendance").innerHTML,
                originalContents = document.body.innerHTML,
                headContents = document.head.innerHTML,
                popupWin = window.open(),
                popupWin.document.open(),
                popupWin.document.write(
                    '<html><head>' + headContents +
                    '<body onload="window.print()">' +
                    printContents + "</html>"),
                popupWin.document.close()
        }
    </script>
@endsection
