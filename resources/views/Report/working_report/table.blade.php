<div class="w-full overflow-auto flex-1">
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
                                    <p class="text-sm font-medium text-gray-800 truncate">
                                        {{ $row['employee']['first_name'] }} {{ $row['employee']['last_name'] }}
                                    </p>
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
