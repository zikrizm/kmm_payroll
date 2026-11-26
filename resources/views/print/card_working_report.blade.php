@extends('layouts.app')
@section('title', 'Print | Card Working Report')
@section('content')
    <div class="flex flex-col items-center gap-8 w-full h-screen overflow-auto py-8">
        <div class="w-full flex justify-center items-center">
            <button type="button" onclick="print()"
                class="text-white shadow bg-violet-600 hover:bg-violet-700 focus:ring-2 focus:ring-violet-700 font-medium rounded-lg xs/max:rounded-md text-sm xs/max:text-xs inline-flex items-center xs/max:px-4 px-6 xs/max:py-1.5 py-2 text-center">Cetak</button>
        </div>
        <div id="print-card-working-report" class="w-max">
            @if (empty($date))
                <p class="text-sm text-gray-500">Tanggal tidak dipilih.</p>
            @elseif ($departments->isEmpty())
                <p class="text-sm text-gray-500">Tidak ada karyawan yang telat masuk atau belum absen masuk pada tanggal {{ $date }}.</p>
            @else
                <p class="text-sm font-medium text-gray-700 mb-2">Tanggal: {{ $date }}</p>
                @foreach ($departments as $group)
                    <p class="font-semibold text-gray-700 mt-4 mb-2">{{ $group['department']['dept_name'] ?? '-' }}</p>
                    <div class="grid gap-5 grid-cols-2 items-start">
                        @foreach ($group['rows'] as $row)
                            @php
                                $isBelumMasuk = $row['status'] === 'belum_masuk';
                                $statusClass = $isBelumMasuk
                                    ? 'text-gray-500'
                                    : ($row['within_tolerance'] ? 'text-yellow-700' : 'text-red-600');
                                $statusLabel = $isBelumMasuk ? 'Belum masuk' : 'Telat ' . $row['late_minutes'] . ' menit';
                            @endphp
                            <div class="flex flex-col gap-2 border rounded p-4 w-[300px]" style="min-width: 300px;">
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

                                <div class="w-full h-10 p-1 border">
                                    <p class="text-gray-500 text-xs">Keterangan:</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    <script>
        function print(params) {
            var originalContents, popupWin, printContents;
            return printContents = document.getElementById(
                    "print-card-working-report").innerHTML,
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
