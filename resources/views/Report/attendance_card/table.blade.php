@if (($result['departments'] ?? collect())->isNotEmpty())
    <div class="w-full overflow-auto overflow-y-hidden flex-1">
        <div class="grid gap-5 grid-cols-3 xl/max:grid-cols-2 lg/max:grid-cols-1 w-max items-start">
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
                                        @php
                                            $punchDialogId = 'punch-dialog-' . preg_replace(
                                                '/[^A-Za-z0-9_-]/',
                                                '',
                                                ($department['department']['dept_code'] ?? 'd') . '-' . ($employee['employee']['emp_code'] ?? $loop->index)
                                            );
                                            $employeeName = trim(
                                                ($employee['employee']['first_name'] ?? '') . ' ' . ($employee['employee']['last_name'] ?? '')
                                            );
                                            $departmentName = $department['department']['dept_name'] ?? '-';
                                            $rangeDateLabel =
                                                date('d-m-Y', strtotime($result['start_date_work_day'])) .
                                                ' – ' .
                                                date('d-m-Y', strtotime($result['end_date_work_day']));
                                        @endphp
                                        <div class="flex items-center gap-1.5">
                                            <p class="text-gray-500 text-xs text-middle mt-0.5">
                                                {{ date('d-m-Y', strtotime($result['start_date_work_day'])) }} -
                                                {{ date('d-m-Y', strtotime($result['end_date_work_day'])) }}
                                            </p>
                                            <button type="button"
                                                class="btn-open-punch-dialog inline-flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded p-0.5"
                                                data-punch-dialog="{{ $punchDialogId }}"
                                                title="Daftar punch">
                                                <x-icon icon="clock" width=12 height=12 viewBox="20 20" />
                                            </button>
                                            <template id="{{ $punchDialogId }}">
                                                <div
                                                    class="bg-white w-[400px] max-w-[95vw] max-h-[90vh] overflow-y-auto overflow-x-hidden relative rounded-lg shadow-lg">
                                                    <button type="button"
                                                        class="absolute top-3 right-3 modal-close hover:bg-gray-100 text-gray-500 rounded p-2 z-10">
                                                        <x-icon icon="x" width=16 height=16 viewBox="20 20" />
                                                    </button>
                                                    <div class="p-5 flex flex-col gap-4">
                                                        <div class="flex items-start gap-3 pr-8">
                                                            <div
                                                                class="w-14 h-14 rounded-full overflow-hidden flex-shrink-0 border border-gray-200 bg-white">
                                                                <x-employee-photo
                                                                    :photo="$employee['employee']['photo'] ?? null" />
                                                            </div>
                                                            <div class="flex flex-col gap-1 min-w-0 text-left">
                                                                <p class="text-base font-semibold text-gray-900 leading-tight">
                                                                    {{ $employeeName !== '' ? $employeeName : '-' }}
                                                                </p>
                                                                <div class="flex flex-col gap-0.5 text-xs text-gray-500">
                                                                    <p><span class="text-gray-400">Bagian:</span>
                                                                        {{ $departmentName }}</p>
                                                                    <p><span class="text-gray-400">Tanggal:</span>
                                                                        {{ $rangeDateLabel }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr class="border-gray-200">
                                                        <div class="flex flex-col gap-2 text-left">
                                                            <p class="text-sm font-medium text-gray-800">Daftar Punch</p>
                                                            <table class="w-full border-collapse">
                                                                <thead>
                                                                    <tr class="border-b border-gray-200">
                                                                        <th
                                                                            class="text-left text-xs font-medium text-gray-500 py-2 w-28">
                                                                            Tanggal</th>
                                                                        <th
                                                                            class="text-left text-xs font-medium text-gray-500 py-2">
                                                                            Punch</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @forelse (($employee['punches_by_date'] ?? collect()) as $punch_date => $punch_times)
                                                                        @php
                                                                            $dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                                                            $punchTimestamp = strtotime($punch_date);
                                                                            $dayName = $dayNames[(int) date('w', $punchTimestamp)];
                                                                            $dateLabel = date('d/m/Y', $punchTimestamp);
                                                                        @endphp
                                                                        <tr class="border-b border-gray-100 align-top">
                                                                            <td
                                                                                class="text-[10px] text-gray-700 py-2.5 pr-3 whitespace-nowrap">
                                                                                <div class="flex flex-col leading-tight">
                                                                                    <span>{{ $dateLabel }}</span>
                                                                                    <span class="text-[10px] text-gray-400">{{ $dayName }}</span>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-xs text-gray-600 py-2.5">
                                                                                @if (!empty($punch_times))
                                                                                    <div class="flex flex-wrap gap-1.5">
                                                                                        @foreach ($punch_times as $punch_time)
                                                                                            <span
                                                                                                class="inline-flex items-center rounded border border-gray-200 bg-gray-50 px-1.5 py-0.5 text-[11px] text-gray-700">
                                                                                                {{ $punch_time }}
                                                                                            </span>
                                                                                        @endforeach
                                                                                    </div>
                                                                                @else
                                                                                    <span class="text-gray-400">—</span>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="2"
                                                                                class="text-xs text-gray-400 py-3">Tidak
                                                                                ada punch</td>
                                                                        </tr>
                                                                    @endforelse
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
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
                                                    @if (!empty($attendance['total_hk_holiday']))
                                                        <p class="text-[10px] text-red-500 truncate">
                                                            ({{ $attendance['total_hk_holiday'] ?? '-' }})
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
@else
<div class="h-full w-full flex justify-center items-center">
    <div class="flex flex-col items-start justify-start gap-4">
        <span>
            <x-icon icon="alert-triangle" width=50 height=50 viewBox="20 20" />
        </span>
        <div class="text-left">
            <p class="text-2xl font-semibold">Laporan tidak ada</p>
            <p>Silahkan pilih bagian terlebih dahulu</p>
        </div>
    </div>
</div>
@endif
