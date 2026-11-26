<div class="w-full overflow-auto overflow-y-hidden flex-1">
    @if (empty($date))
        <p class="text-sm text-gray-500">Pilih tanggal absensi.</p>
    @elseif ($departments->isEmpty())
        <p class="text-sm text-gray-500">Tidak ada karyawan yang telat masuk atau belum absen masuk pada tanggal {{ $date }}.</p>
    @else
        <p class="text-sm font-medium text-gray-700 mb-2">Tanggal: {{ $date }}</p>
        @foreach ($departments as $group)
            <p class="font-semibold text-gray-700 mt-4 mb-2">{{ $group['department']['dept_name'] ?? '-' }}</p>
            <div class="grid gap-4 grid-cols-3 xl/max:grid-cols-2 lg/max:grid-cols-1 w-max items-start">
                @foreach ($group['rows'] as $row)
                    @php
                        $isBelumMasuk = $row['status'] === 'belum_masuk';
                        $statusClass = $isBelumMasuk
                            ? 'text-gray-500'
                            : ($row['within_tolerance'] ? 'text-yellow-700' : 'text-red-600');
                        $statusLabel = $isBelumMasuk ? 'Belum masuk' : 'Telat ' . $row['late_minutes'] . ' menit';
                    @endphp
                    <div class="flex flex-col gap-2 border rounded p-4 w-[280px]" style="min-width: 280px;">
                        <header class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white rounded-full overflow-hidden shrink-0">
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
                                <p class="font-bold text-gray-700 truncate">
                                    {{ $row['employee']['first_name'] }} {{ $row['employee']['last_name'] }}
                                </p>
                                <p class="text-xs text-gray-500 truncate">{{ $row['timetable_name'] ?? '-' }}</p>
                            </div>
                        </header>

                        <div class="flex justify-between items-baseline text-xs text-gray-500">
                            <span>Jadwal masuk</span>
                            <span>
                                {{ $row['check_in'] }}
                                <span class="text-gray-400 opacity-60">+{{ $row['tolerance_minutes'] ?? 0 }}</span>
                            </span>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500">
                            <span>Absen masuk</span>
                            <span>{{ $row['first_punch'] ?? '-' }}</span>
                        </div>
                        <p class="text-sm font-semibold {{ $statusClass }}">{{ $statusLabel }}</p>
                    </div>
                @endforeach
            </div>
        @endforeach
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
