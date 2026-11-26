<div class="w-full overflow-auto flex-1">
    @if (!empty($failed_departments))
        <p class="text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded px-3 py-2 mb-3">
            Data absensi gagal diambil untuk bagian: {{ implode(', ', $failed_departments) }}. Daftar ini belum lengkap. Coba muat ulang.
        </p>
    @endif
    @if (empty($date))
        <p class="text-sm text-gray-500">Pilih tanggal absensi.</p>
    @elseif ($rows->isEmpty())
        <p class="text-sm text-gray-500">Tidak ada karyawan yang melewati batas disiplin pada tanggal {{ $date }}.</p>
    @else
        <p class="text-sm font-medium text-gray-700 mb-3">Tanggal: {{ $date }}</p>
        <table class="w-full border-collapse">
            <thead>
                <tr class="border-b text-left text-xs font-medium text-gray-500">
                    <th class="py-2 pr-3 font-medium">Nama</th>
                    <th class="py-2 pr-3 font-medium">Bagian</th>
                    <th class="py-2 pr-3 font-medium">Jadwal masuk</th>
                    <th class="py-2 pr-3 font-medium">Absen masuk</th>
                    <th class="py-2 font-medium">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    @php
                        $statusLabel = 'Telat ' . $row['late_minutes'] . ' menit';
                        $punchDialogId = 'punch-dialog-' . preg_replace(
                            '/[^A-Za-z0-9_-]/',
                            '',
                            ($row['department']['dept_code'] ?? 'd') . '-' . ($row['employee']['emp_code'] ?? $loop->index)
                        );
                        $employeeName = trim(($row['employee']['first_name'] ?? '') . ' ' . ($row['employee']['last_name'] ?? ''));
                        $dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                        $punchTimestamp = strtotime($row['date']);
                        $punchDayName = $dayNames[(int) date('w', $punchTimestamp)];
                        $punchDateLabel = date('d/m/Y', $punchTimestamp);
                    @endphp
                    <tr class="border-b">
                        <td class="py-2.5 pr-3">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-full overflow-hidden shrink-0 bg-gray-100">
                                    @if (!empty($row['employee']['photo']))
                                        <img class="image w-full h-full object-cover"
                                            src="{{ config('constants.api_zkteco') }}{{ $row['employee']['photo'] }}"
                                            alt="">
                                    @else
                                        <img class="w-full h-full object-cover"
                                            src="{{ config('constants.api_zkteco') }}/files/nophoto.gif" alt=""
                                            onerror="this.parentElement.style.width='0';">
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <p class="text-sm font-medium text-gray-800 truncate">
                                            {{ $row['employee']['first_name'] }} {{ $row['employee']['last_name'] }}
                                        </p>
                                        <button type="button"
                                            class="btn-open-punch-dialog inline-flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded p-0.5 shrink-0"
                                            data-punch-dialog="{{ $punchDialogId }}"
                                            title="Daftar punch">
                                            <x-icon icon="clock" width=12 height=12 viewBox="20 20" />
                                        </button>
                                        <template id="{{ $punchDialogId }}">
                                            <div
                                                class="bg-white w-[400px] max-w-[95vw] max-h-[90vh] overflow-y-auto overflow-x-hidden relative rounded-lg shadow-lg box-border">
                                                <button type="button"
                                                    class="absolute top-3 right-3 modal-close hover:bg-gray-100 text-gray-500 rounded p-2 z-10">
                                                    <x-icon icon="x" width=16 height=16 viewBox="20 20" />
                                                </button>
                                                <div class="p-5 flex flex-col gap-2.5 w-full min-w-0 box-border">
                                                    <div class="flex items-start gap-3 pr-8">
                                                        <div
                                                            class="w-14 h-14 rounded-full overflow-hidden flex-shrink-0 border border-gray-200 bg-white">
                                                            <x-employee-photo :photo="$row['employee']['photo'] ?? null" />
                                                        </div>
                                                        <div class="flex flex-col gap-1 min-w-0 text-left">
                                                            <p class="text-base font-semibold text-gray-900 leading-tight">
                                                                {{ $employeeName !== '' ? $employeeName : '-' }}
                                                            </p>
                                                            <div class="flex flex-col gap-0.5 text-xs text-gray-500">
                                                                <p><span class="text-gray-400">Bagian:</span>
                                                                    {{ $row['department']['dept_name'] ?? '-' }}</p>
                                                                <p><span class="text-gray-400">Tanggal:</span>
                                                                    {{ date('d-m-Y', $punchTimestamp) }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr class="border-gray-200">
                                                    <div class="flex flex-col text-left w-full min-w-0">
                                                        <div class="flex items-center gap-4 border-b border-gray-200 pb-2">
                                                            <p class="text-xs font-medium text-gray-500 w-[88px] shrink-0">Tanggal</p>
                                                            <p class="text-xs font-medium text-gray-500 min-w-0 flex-1">Punch</p>
                                                        </div>
                                                        <div class="flex items-start gap-4 border-b border-gray-100 py-2.5 w-full min-w-0">
                                                            <div class="w-[88px] shrink-0 text-[10px] text-gray-700">
                                                                <div class="flex flex-col leading-tight">
                                                                    <span>{{ $punchDateLabel }}</span>
                                                                    <span class="text-[10px] text-gray-400">{{ $punchDayName }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="min-w-0 flex-1 overflow-hidden">
                                                                @if (!empty($row['punches']))
                                                                    <div class="flex flex-wrap gap-1.5" style="flex-wrap: wrap;">
                                                                        @foreach ($row['punches'] as $punch_time)
                                                                            <span
                                                                                class="inline-flex items-center rounded border border-gray-200 bg-gray-50 px-1.5 py-0.5 text-[10px] text-gray-700"
                                                                                style="flex: 0 0 auto;">
                                                                                {{ $punch_time }}
                                                                            </span>
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    <span class="text-[10px] text-gray-400">—</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    <p class="text-xs text-gray-400 truncate">{{ $row['timetable_name'] ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-2.5 pr-3 text-sm text-gray-600">{{ $row['department']['dept_name'] ?? '-' }}</td>
                        <td class="py-2.5 pr-3 text-sm text-gray-700">
                            {{ $row['check_in'] }}
                            <span class="text-gray-400 opacity-60">+{{ $row['tolerance_minutes'] ?? 0 }}</span>
                        </td>
                        <td class="py-2.5 pr-3 text-sm text-gray-700">{{ $row['first_punch'] ?? '-' }}</td>
                        <td class="py-2.5 text-sm font-medium text-red-600">{{ $statusLabel }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
<script>
    var images = document.querySelectorAll('.image');
    images.forEach(function(img) {
        img.onerror = function() {
            img.src = img.src.replace('auth_files/photo', 'auth_files/biophoto');
        };
    });
</script>
