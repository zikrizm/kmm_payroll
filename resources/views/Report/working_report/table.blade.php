<div class="w-full overflow-auto overflow-y-hidden flex-1">
    <div class="grid gap-5 grid-cols-3 xl/max:grid-cols-2 lg/max:grid-cols-1 w-max items-start">
        @foreach ($result['department_reports'] as $department)
            @foreach ($department['employee_attendances'] as $employee_attendance)
                <div class="flex flex-col gap-2.5 border rounded p-4 w-[375px]" style="min-width: 375px;">
                    <header class="relative">
                        <div>
                            <p class="font-bold text-gray-700 text-xl">Absensi Tidak Disiplin</p>
                            <p class="font-medium text-gray-500 text-xs ">{{ Auth::user()->business->name }}</p>
                        </div>
                        <div class="flex items-end absolute top-[5px] w-full">
                            <hr class="flex-1 border-[1.5px] bg-black rounded ">
                            <div class="w-14 h-14 bg-white mb-[-10px] rounded-full overflow-hidden">
                                @if (!empty($employee_attendance['employee']['photo']))
                                <img class="image w-full h-full object-cover"
                                    src="{{config('constants.api_zkteco')}}{{ $employee_attendance['employee']['photo'] }}" alt="">
                                @else
                                <img class="w-full h-full object-cover"
                                    src="{{config('constants.api_zkteco')}}/files/nophoto.gif" alt=""
                                    onerror="this.parentElement.style.width='0';">
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
                                        {{ date('d-m-Y', strtotime($result['working_date']['start_date'])) }} -
                                        {{ date('d-m-Y', strtotime($result['working_date']['end_date'])) }}
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
                                        {{ $employee_attendance['employee']['first_name'] }}
                                        {{ $employee_attendance['employee']['last_name'] }}
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
                    <div class="flex flex-col gap-1">
                        <table class='table border-collapse w-full border '>
                            <thead class=''>
                                <tr class='border border-2 '>
                                    <th class='text-left border '>
                                        <p class="text-xs font-medium text-gray-500 text-center truncate">Tgl</p>
                                    </th>
                                    <th class='text-left border '>
                                        <p class="text-xs font-medium text-gray-500 text-center truncate">Hari</p>
                                    </th>
                                    <th class='text-left border '>
                                        <p class="text-xs font-medium text-gray-500 text-center truncate">Masuk</p>
                                    </th>
                                    <th class='text-left border '>
                                        <p class="text-xs font-medium text-gray-500 text-center truncate">Keluar</p>
                                    </th>
                                    <th class='text-left border '>
                                        <p class="text-xs font-medium text-gray-500 text-center truncate">Ket</p>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $attendances_working = $employee_attendance['attendances']->filter(function ($item) {
                                        return count($item['working_status_list']) > 0;
                                    });
                                @endphp
                                @foreach ($attendances_working as $key => $attendance)
                                    @php
                                        $invalidWorking = $attendance['working_status_list']->filter(function ($item) {
                                            return !$item['status'];
                                        });
                                    @endphp
                                    @foreach($invalidWorking as $key => $item)
                                        <tr class='hover:bg-gray-50 border'>
                                            @if ($key == 0)
                                            <td rowspan="{{ count($invalidWorking) }}"
                                                class="text-xs border text-center w-8 {{ $attendance['is_holiday'] ? 'text-red-500' : 'text-gray-500' }}">
                                                {{ date('d', strtotime($attendance['date'])) }}
                                            </td>
                                            <td rowspan="{{ count($invalidWorking) }}"
                                                class="text-xs border text-center w-8 {{ $attendance['is_holiday'] ? 'text-red-500' : 'text-gray-500' }}">
                                                {{ $attendance['key'] }}
                                            </td>
                                            @endif
                                            <td class='text-xs border text-gray-500 text-center w-12'>
                                                @if (!empty($item['start_punch']))
                                                    {{ date('H:i', strtotime($item['start_punch'])) }}
                                                @else
                                                -
                                                @endif
                                            </td>
                                            <td class='text-xs border text-gray-500 text-center w-14 '>
                                                @if (!empty($item['end_punch']))
                                                    <div class="relative">
                                                        {{ date('H:i', strtotime($item['end_punch'])) }}
                                                    </div>
                                                @else
                                                -
                                                @endif
                                            </td>
                                            <td class='text-xs border text-gray-500 text-center'>
                                                <p>{{ $item['info'] ?? '-' }}</p>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                        <div class="w-full h-10 p-1 border">
                            <p class="text-gray-500 text-xs">Keterangan:</p>
                        </div>
                    </div>
                </div>
            @endforeach
        @endforeach
    </div>
</div><script>
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