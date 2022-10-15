<div class="flex flex-col gap-2.5 items-end flex-1">
    <button onclick="get_modal()"
        class="flex items-center gap-2.5 px-4 py-2 text-gray-500 text-sm font-medium flex items-center border border-gray-200 shadow-sm rounded-lg">
        <x-icon icon="printer" width=18 height=18 viewBox="20 20" />
        Cetak kartu
    </button>
    <div class="flex flex-col gap-2.5 border rounded p-4 w-[375px]">
        <header class="relative">
            <div>
                <p class="font-bold text-gray-700 text-xl ">KARTU ABSEN</p>
                <p class="font-medium text-gray-500 text-xs ">{{ Auth::user()->business->name }}</p>
            </div>
            <div class="flex items-end absolute top-[5px] w-full">
                <hr class="flex-1 border-[1.5px] bg-black rounded ">
                <div class="w-14 h-14 bg-white mb-[-10px]">
                    <img class="w-full h-full object-cover" src="{{ Auth::user()->business->logo }}" alt="">
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
                        <p class="text-gray-500 text-xs text-middle mt-0.5">{{ $attendance_emp_report['range_date']
                            }}</p>
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
                            {{ $attendance_emp_report['employee']['first_name'] }}
                            {{ $attendance_emp_report['employee']['last_name'] }}
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
                            {{ $attendance_emp_report['employee']['department']['dept_name'] }}
                        </p>
                    </td>
                </tr>
            </table>
        </div>
        <table class='table border-collapse w-full border '>
            <thead class=''>
                <tr class='border border-2 '>
                    <th class='text-left border  '>
                        <p class="text-xs font-medium text-gray-500 text-center truncate">Tgl</p>
                    </th>
                    <th class='text-left border '>
                        <p class="text-xs font-medium text-gray-500 text-center truncate">Masuk</p>
                    </th>
                    <th class='text-left border '>
                        <p class="text-xs font-medium text-gray-500 text-center truncate">Lembur</p>
                    </th>
                    <th class='text-left border '>
                        <p class="text-xs font-medium text-gray-500 text-center truncate">Shift</p>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendance_emp_report['reports'] as $key => $item)
                <tr class='hover:bg-gray-50 border '>
                    <td class=' text-xs border text-gray-500 text-center'>
                        {{ date('d', strtotime($item['date'])) }}
                    </td>
                    <td class=' text-xs border text-gray-500 text-center'>
                        {{ $item['in'] ?? 'x' }}
                    </td>
                    <td class=' text-xs border text-gray-500 text-center'>
                        {{ $item['overtime'] ?? 'x' }}
                    </td>
                    <td class=' text-xs border text-gray-500 text-center'>
                        @if (!empty($item['shift']))
                        ({{ $item['shift']['shift_id'] }}) {{ $item['shift']['name'] }}
                        @else
                        -
                        @endif
                    </td>
                </tr>
                @endforeach
                <tr class='hover:bg-gray-50'>
                    <td class=' text-xs text-center'>
                    </td>
                    <td class=' text-xs text-center border text-gray-500'>
                        {{ $attendance_emp_report['total_in'] ?? '-' }}
                    </td>
                    <td class=' text-xs text-center border text-gray-500'>
                        {{ $attendance_emp_report['total_overtime'] ?? '-' }}
                    </td>
                    <td class=' text-xs text-center'> </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>