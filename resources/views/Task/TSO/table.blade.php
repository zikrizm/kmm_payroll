<main class='border border-gray-200 rounded-lg shadow-sm w-full overflow-hidden'>
    <div class="w-full overflow-auto overflow-y-hidden">
        <table class='table border-collapse w-full table-tso'>
            <thead class='border-b border-gray-200 bg-gray-50'>
                <tr class=''>
                    <th class='text-left'>
                        <div class='flex items-center'>
                            <div class='pl-4 py-2 flex items-center'>
                                <div class="flex items-center justify-center relative">
                                    <input type='checkbox' onchange="selectAllAttendance(this)"
                                        class="min-h-[16px] min-w-[16px] w-4 h-4 opacity-0 z-10 peer cursor-pointer" />
                                    <span
                                        class="absolute min-h-[16px] min-w-[16px] w-4 h-4 border border-gray-300 rounded cursor-pointer
                                        flex items-center justify-center peer-checked:border-violet-600 invisible peer-checked:visible">
                                        <x-icon icon="check" width=12 height=12 viewBox="20 20" />
                                    </span>
                                    <span class="absolute min-h-[16px] min-w-[16px] w-4 h-4 border border-gray-300 rounded
                                         visible peer-checked:invisible cursor-pointer">
                                    </span>
                                </div>
                            </div>
                            <div class='pl-6 pr-3 py-3 cursor-pointer flex-1'>
                                <p class="text-xs font-medium text-gray-500 truncate">Tanggal absensi dan Waktu absensi
                                </p>
                            </div>
                        </div>
                    </th>
                    <th class='px-3 py-3 text-left'>
                        <p class="text-xs font-medium text-gray-500 truncate">Karyawan</p>
                    </th>
                    <th class='px-3 py-3 text-left'>
                        <p class="text-xs font-medium text-gray-500 truncate">Bagian</p>
                    </th>
                    <th class='px-3 py-3 text-left'>
                        <p class="text-xs font-medium text-gray-500 truncate">Shift</p>
                    </th>
                    <th class='px-3 py-3 text-left'>
                        <p class="text-xs font-medium text-gray-500 truncate">Keterangan</p>
                    </th>
                    <th class='px-3 py-3 text-center'>
                        <p class="text-xs font-medium text-gray-500 truncate">Status</p>
                    </th>
                    {{-- @canany(['attendance-tso.approved']) --}}
                    <th class='px-3 py-3 text-left text-gray-500 text-xs font-medium'></th>
                    {{-- @endcanany --}}
                </tr>
            </thead>
            <tbody id="tbody-tso" class=""></tbody>
        </table>
    </div>
</main>