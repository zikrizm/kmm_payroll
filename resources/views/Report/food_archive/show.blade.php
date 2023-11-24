<section
    class="container-modal-calculate flex flex-col gap-8 py-4 w-[400px] bg-white border max-h-[95vh] overflow-y-auto overflow-x-hidden relative rounded-lg duration-300">
    <header class="flex flex-col gap-4 px-4 relative w-full">
        <button
            class="close-calculate absolute top-[-5px] right-3 xs/max:top-[-6px] modal-close hover:border-gray-500  border border-transparent text-gray-500 rounded p-0.5">
            <x-icon icon="x" width=16 height=16 viewBox="20 20" />
        </button>
        <div class="flex flex-col gap-1 w-full">
            <div class="flex items-start gap-2">
                <div
                    class="rounded-full bg-violet-100 p-1.5 border-[4px] border-violet-50 box-border mr-2 text-violet-800">
                    <x-icon icon="database" width=18 height=18 viewBox="20 20" />
                </div>
                <div>
                    <p class="text-xl font-semibold text-gray-900">Detail tagihan nasi
                    </p>
                    <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                        Lihat karyawan bagian = {{ $food_archive['food_archive_th']['dept_name'] }}, yang mendapat nasi.
                    </p>
                </div>
            </div>
        </div>
        <hr>
    </header>
    <div>
        <section class="bg-gray-100 py-1.5 px-4 flex items-center border-b">
            <div class="flex-[2]">
                <p class="text-sm text-gray-700 font-medium">Tanggal</p>
            </div>
            <div class="flex items-center gap-2.5 flex-1">
                <p class="text-sm text-gray-700 font-medium ">
                    Total
                </p>
            </div>
        </section>
        <section>
            @foreach ($food_archive['total_food_perhari'] as $item)
                <div class="w-full flex flex-col gap-2 px-4 py-1.5 date-box">
                    <div class="w-full flex items-center justify-between ">
                        <div class="flex-[2]">
                            <p class="text-sm text-gray-500">
                                {{ Carbon\Carbon::parse($item['date'])->format('d F Y') }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2.5 flex-1">
                            <div class="flex-1">
                                <p class="text-sm text-gray-500">
                                    {{ $item['total'] ?? 0 }}
                                </p>
                            </div>
                            <button onclick="colapse(this)"
                                class="text-xs text-gray-500 border px-2 py-1 rounded flex items-center gap-1">
                                <x-icon icon="chevron-down" width=16 height=16 viewBox="20 20" />
                            </button>
                        </div>
                    </div>
                    <div class="emps-box hidden">
                        @foreach ($item['employees'] as $emp)
                            <div class="w-full flex items-center justify-between ">
                                <div class="flex-[2]">
                                    <p class="text-sm text-gray-500">
                                    <p class="text-sm text-gray-500">{{ $emp['first_name'] ?? '-' }}
                                        {{ $emp['last_name'] ?? '' }}</p>
                                    </p>
                                </div>
                                <div class="flex items-center gap-2.5 flex-1">
                                    <p class="text-sm text-gray-500">
                                        {{ $emp['total'] ?? 0 }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </section>
    </div>
    {{-- <div class="flex flex-col gap-4">
        <section class="px-4">
            <p class="text-base text-gray-700 font-medium">Range tanggal nasi</p>
            <p class="text-sm text-gray-500">Range tanggal nasi untuk bagian <span class="font-medium text-gray-700">{{
                    $food_archive->food_archive_th->dept_name }}</span>.</p>
            <hr class="mt-2">
        </section>
        <section>
            <div class="w-full flex items-center justify-between px-4 py-1.5 gap-4 bg-gray-100">
                <p class="text-sm text-gray-500">Tanggal awal</p>
                <p class="text-sm text-gray-500">
                    {{ Carbon\Carbon::parse($food_archive->food_archive_th->start_date)->format('d-m-Y'); }}
                </p>
            </div>
            <div class="w-full flex items-center justify-between px-4 py-1.5 gap-4">
                <p class="text-sm text-gray-500">Tanggal akhir</p>
                <p class="text-sm text-gray-500">
                    {{ Carbon\Carbon::parse($food_archive->food_archive_th->end_date)->format('d-m-Y'); }}
                </p>
            </div>
        </section>
        <section class="px-4">
            <p class="text-base text-gray-700 font-medium">Karyawan</p>
            <p class="text-sm text-gray-500">Jumlah nasi yang di dapat karyawan.</p>
            <hr class="mt-2">
        </section>
        <div>
            <section class="bg-gray-100 py-1.5 px-4 flex items-center border-b">
                <div class="flex-[2]">
                    <p class="text-sm text-gray-700 font-medium">Nama</p>
                </div>
                <div class="flex items-center gap-2.5 flex-1">
                    <p class="text-sm text-gray-700 font-medium ">
                        Tgl & Total
                    </p>
                </div>
            </section>
            <section>
                @foreach ($food_archive->food_archive_td_emps as $item)
                @foreach ($item->food_archive_td_emp_attendances as $food_archive_td_emp_attendance)
                @if ($food_archive_td_emp_attendance->total > 0)
                <div class="w-full flex items-center justify-between px-4 py-1.5">
                    <div class="flex-[2]">
                        <p class="text-sm text-gray-500">{{ $item->first_name ?? '-' }} {{ $iten->last_name ?? '' }}</p>
                    </div>
                    <div class="flex items-center gap-2.5 flex-1">
                        <p class="text-sm text-gray-500">
                            {{ Carbon\Carbon::parse($food_archive_td_emp_attendance->food_date)->format('Y-m-d'); }}
                        </p>
                        <button class="text-xs text-gray-500 border px-2 py-1 rounded flex items-center gap-1">
                            {{ $food_archive_td_emp_attendance->total ?? 0 }}
                        </button>
                    </div>
                </div>
                @endif
                @endforeach
                @endforeach
            </section>
        </div>
    </div> --}}
</section>
