<div class="w-full overflow-auto overflow-y-hidden flex-1">
    @if (empty($date))
        <p class="text-sm text-gray-500">Pilih tanggal absensi.</p>
    @elseif ($departments->isEmpty())
        <p class="text-sm text-gray-500">Tidak ada karyawan yang telat masuk atau belum absen masuk pada tanggal {{ $date }}.</p>
    @else
        <p class="text-sm font-medium text-gray-700 mb-2">Tanggal: {{ $date }}</p>
        @foreach ($departments as $group)
            <p class="font-semibold text-gray-700 mt-4 mb-2">{{ $group['department']['dept_name'] ?? '-' }}</p>
            <div class="grid gap-5 grid-cols-3 xl/max:grid-cols-2 lg/max:grid-cols-1 w-max items-start">
                @foreach ($group['rows'] as $row)
                    @php
                        $isBelumMasuk = $row['status'] === 'belum_masuk';
                        $badgeClass = $isBelumMasuk
                            ? 'bg-gray-200 text-gray-700'
                            : ($row['within_tolerance'] ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-700');
                        $badgeLabel = $isBelumMasuk ? 'Belum masuk' : 'Telat ' . $row['late_minutes'] . ' menit';
                    @endphp
                    <div class="flex flex-col gap-2.5 border rounded p-4 w-[300px]" style="min-width: 300px;">
                        <header class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white rounded-full overflow-hidden shrink-0">
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

                        <div class="flex justify-between text-xs text-gray-500">
                            <span>Jadwal masuk</span>
                            <span>{{ $row['check_in'] }}</span>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500">
                            <span>Absen masuk</span>
                            <span>{{ $row['first_punch'] ?? '-' }}</span>
                        </div>

                        <div>
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-medium {{ $badgeClass }}">
                                {{ $badgeLabel }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-600">{{ $row['info'] }}</p>
                    </div>
                @endforeach
            </div>
        @endforeach
    @endif
</div>
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
