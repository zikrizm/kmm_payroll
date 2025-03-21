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
                @foreach ($datas as $data)
                    @foreach ($data['attendance_reports'] as $item)
                        <div class="flex flex-col gap-2.5 border rounded p-4 w-full max-w-[375px]">
                            <header class="relative">
                                <div>
                                    <p class="font-bold text-gray-700 text-xl">KARTU ABSEN</p>
                                    <p class="font-medium text-gray-500 text-xs ">{{ Auth::user()->business->name }}</p>
                                </div>
                                <div class="flex items-end absolute top-[5px] w-full">
                                    <hr class="flex-1 border-[1.5px] bg-black rounded ">
                                    <div class="w-14 h-14 bg-white mb-[-10px] rounded-full overflow-hidden">
                                        @if (!empty($item['employee']['photo']))
                                            <img class="image w-full h-full object-cover"
                                                src="{{ config('constants.api_zkteco') }}{{ $item['employee']['photo'] }}"
                                                alt="">
                                        @else
                                            <img class="w-full h-full object-cover"
                                                src="{{ config('constants.api_zkteco') }}/files/nophoto.gif" alt="">
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
                                                {{ date('d-m-Y', strtotime($data['start_date'])) }} -
                                                {{ date('d-m-Y', strtotime($data['end_date'])) }}
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
                                                {{ $data['department']['dept_name'] ?? '-' }}
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
                                                    </div>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class='text-xs border text-gray-500 text-center'>
                                                <div class="relative flex items-center justify-between gap-1">
                                                    <div class="flex justify-center text-center flex-1 overflow-hidden">
                                                        @if (!empty($attendance['timetable']))
                                                            <p class="text-[10px] truncate">
                                                                {{ $attendance['timetable']['name'] ?? '-' }}</p>
                                                        @else
                                                            -
                                                        @endif
                                                    </div>
                                                </div>

                                            </td>
                                            <td class='text-xs border text-gray-500 text-center w-14'>
                                                {{ $attendance['JL'] ?? '-' }}
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
                                                        {{ $item['JL_value'] ?? 0 }}</p>
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
    <script>
        // Mendapatkan semua gambar dengan class 'image'
        var images = document.querySelectorAll('.image');

        // Menambahkan event error pada setiap gambar
        images.forEach(function(img) {
            img.onerror = function() {
                // Jika terjadi error, ubah URL gambar
                img.src = img.src.replace('auth_files/photo', 'auth_files/biophoto');
            };
        });
</script>
@endsection
