<div>
    <div class="flex items-start justify-center py-2 px-2 gap-2 w-full border">
        <div>
            <p class="text-xs font-semibold">
                Keterangan:
            </p>
        </div>
        <div class="grid grid-cols-3 gap-x-4 gap-y-2">
            <div class="flex items-center gap-2">
                <x-icon icon="sehat" width=16 height=16 viewBox="20 20" />
                <p class="text-xs">: Sehat</p>
            </div>
            <div class="flex items-center gap-2">
                <x-icon icon="tangan" width=16 height=16 viewBox="20 20" />
                <p class="text-xs">: Kuku Bersih dan Pendek</p>
            </div>
            <div class="flex items-center gap-2">
                <x-icon icon="seragam" width=16 height=16 viewBox="20 20" />
                <p class="text-xs">: Seragam Lengkap</p>
            </div>
            <div class="flex items-center gap-2">
                <x-icon icon="cincin" width=16 height=16 viewBox="20 20" />
                <p class="text-xs">: Tidak pakai Perhiasan</p>
            </div>
            <div class="flex items-center gap-2">
                <x-icon icon="smile" width=16 height=16 viewBox="20 20" />
                <p class="text-xs">: Pakaian Bersih</p>
            </div>
            <div class="flex items-center gap-2">
                <x-icon icon="smile" width=16 height=16 viewBox="20 20" />
                <p class="text-xs">: Pakaian Bersih</p>
            </div>
            <div class="flex items-center gap-2">
                <x-icon icon="cuci-tangan" width=16 height=16 viewBox="20 20" />
                <p class="text-xs">: Cuci Tangan</p>
            </div>
            <div class="flex items-center gap-2">
                <x-icon icon="hati" width=16 height=16 viewBox="20 20" />
                <p class="text-xs">: Tidak ada Luka</p>
            </div>
        </div>
    </div>
    <div class="h-2"></div>
    <div class="flex flex-col items-start pl-10 pr-20 gap-1">
        <div class="border pl-2 pr-6 py-1">
            <p class="text-xs">
                Bagian: {{ $department_bios['dept_name'] }}
            </p>
        </div>
        <div class="border pl-2 pr-6 py-1">
            <p class="text-xs">
                Tahun: {{ $year }}
            </p>
        </div>

    </div>
    <div class="h-2"></div>
    <main class='border border-gray-200 rounded-lg shadow-sm overflow-hidden'>
        <div class="w-full overflow-auto overflow-y-hidden">
            <table class='table border-collapse w-full'>
                <thead class='border border-gray-200'>
                    <tr class='border-t'>
                        <th class='border border-t-0 border-l-0 px-3 py-1 text-left' rowspan="3" width="1%">
                            <p class="text-xs font-medium truncate">No</p>
                        </th>
                        <th class='border border-t-0 border-l-0 px-3 py-1 text-left' rowspan="3">
                            <p class="text-xs font-medium truncate">Nama</p>
                        </th>
                        @foreach ($range_dates as $item)
                            <th class='border border-t-0 px-3 py-1 text-center' colspan="7">
                                <p class="text-xs font-medium truncate">Hari / Tanggal</p>
                            </th>
                            <th class='border border-t-0 border-l-0 px-3 py-1 text-left' rowspan="3">
                                <div class="flex justify-center items-center text-center">
                                    <p class="text-xs font-medium truncate" style="writing-mode: sideways-rl;">
                                        Keterangan
                                    </p>
                                </div>
                            </th>
                            <th class='border border-t-0 border-l-0 px-3 py-1 text-center' rowspan="3">
                                <p class="text-xs font-medium truncate">Paraf</p>
                            </th>
                        @endforeach
                    </tr>
                    <tr class='border-b-2'>
                        @foreach ($range_dates as $item)
                            <th class='border px-3 py-1 text-center' colspan="7">
                                <p class="text-xs font-medium truncate">{{ $item['date'] }}
                                </p>
                            </th>
                        @endforeach
                    </tr>
                    <tr class='border-b-2'>
                        @foreach ($range_dates as $item)
                            <th class='border px-3 py-1 text-center' colspan="1">
                                <div class="flex justify-center">
                                    <x-icon icon="sehat" width=16 height=16 viewBox="20 20" />
                                </div>
                            </th>
                            <th class='border px-3 py-1 text-center' colspan="1">
                                <div class="flex justify-center">
                                    <x-icon icon="cincin" width=16 height=16 viewBox="20 20" />
                                </div>
                            </th>
                            <th class='border px-3 py-1 text-center' colspan="1">
                                <div class="flex justify-center">
                                    <x-icon icon="hati" width=16 height=16 viewBox="20 20" />
                                </div>
                            </th>
                            <th class='border px-3 py-1 text-center' colspan="1">
                                <div class="flex justify-center">
                                    <x-icon icon="tangan" width=16 height=16 viewBox="20 20" />
                                </div>
                            </th>
                            <th class='border px-3 py-1 text-center' colspan="1">
                                <div class="flex justify-center">
                                    <x-icon icon="smile" width=16 height=16 viewBox="20 20" />
                                </div>
                            </th>
                            <th class='border px-3 py-1 text-center' colspan="1">
                                <div class="flex justify-center">
                                    <x-icon icon="seragam" width=16 height=16 viewBox="20 20" />
                                </div>
                            </th>
                            <th class='border px-3 py-1 text-center' colspan="1">
                                <div class="flex justify-center">
                                    <x-icon icon="cuci-tangan" width=16 height=16 viewBox="20 20" />
                                </div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employees as $key => $item)
                        <tr class='hover:bg-gray-50 border-gray-200'>
                            <td class='border border-l-0 px-3 py-2 text-gray-500 text-xs text-right'>
                                {{ $key + 1 }}
                            </td>
                            <td class='border px-3 py-2 text-gray-500 text-xs'>
                                <p class="truncate">
                                    {{ $item['first_name'] ?? '-' }}
                                    {{ $item['last_name'] ?? '' }}
                                </p>
                            </td>
                            @foreach ($range_dates as $item)
                                @for ($i = 0; $i < 7; $i++)
                                    <td class='border border-l-0 px-3 py-2 text-gray-500 text-xs text-right'>
                                        @if ($check_all)
                                            <div class="flex justify-center">
                                                <x-icon icon="check" width=16 height=16 viewBox="20 20" />
                                            </div>
                                        @endif
                                    </td>
                                @endfor
                                <td class='border px-3 py-2 text-gray-500 text-xs'></td>
                                <td class='border px-3 py-2 text-gray-500 text-xs'></td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </main>

</div>
