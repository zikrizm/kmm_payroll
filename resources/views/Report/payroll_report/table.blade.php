<main class='border border-gray-200 rounded-lg shadow-sm overflow-hidden'>
    <div class="w-full overflow-auto overflow-y-hidden">
        <table class='table border-collapse w-full'>
            <thead class='border-b border-gray-200 bg-gray-50'>
                <tr class=''>
                    <th class='px-3  py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">No.</p>
                    </th>
                    <th class='px-3  py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Nama pegawai</p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Tanggal hitung</p>
                    </th>
                    {{-- @foreach ($th_dates as $item)
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer text-center">
                            {{ date('d', strtotime($item['date'])) }}/{{ $item['slug'] }}
                        </p>
                    </th>
                    @endforeach --}}
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer text-center">HK</p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer text-center">JL</p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Gaji</p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Kasbon</p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Lembur</p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Rbhn+U.Libur</p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Jabatan</p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Total</p>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendance_reports as $key => $item)
                <tr class='hover:bg-gray-50 border-b border-gray-200'>
                    <td class='px-3 py-2 text-gray-500 text-sm'>
                        {{ $key +1 }}
                    </td>
                    <td class='px-3 py-2 text-gray-500 text-sm'>
                        <p class="truncate">{{ $item['employee']['first_name'] }} {{ $item['employee']['last_name'] }}
                        </p>
                    </td>
                    <td class='px-3 py-2 text-gray-500 text-sm'>
                        <p class="truncate">2022-10-10</p>
                    </td>
                    {{-- @foreach ($item['reports'] as $item_report)
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer text-center">
                            {{ ($item_report['overtime'] ?? 0) + ($item_report['in'] ?? 0) }}
                        </p>
                    </th>
                    @endforeach --}}
                    <td class='px-3 py-2 text-gray-500 text-sm'>
                        <p class="truncate text-center">{{ $item['in_count'] }}</p>
                    </td>
                    <td class='px-3 py-2 text-gray-500 text-sm'>
                        <p class="truncate text-center">{{ $item['overtime_count'] ?? '-' }}</p>
                    </td>
                    <td class='px-3 py-2 text-gray-500 text-sm'>
                        <p class="truncate">@convertnorp($item['daily_salary'])</p>
                    </td>
                    <td class='px-3 py-2 text-gray-500 text-sm'>
                        <p class="truncate">@convertnorp($item['dept'])</p>
                    </td>
                    <td class='px-3 py-2 text-gray-500 text-sm'>
                        <p class="truncate">@convertnorp($item['overtime_payment'])</p>
                    </td>
                    <td class='px-3 py-2 text-gray-500 text-sm'>
                        <p class="truncate"></p>
                    </td>
                    <td class='px-3 py-2 text-gray-500 text-sm'>
                        <p class="truncate">@convertnorp($item['position_extra_pay'])</p>
                    </td>
                    <td class='px-3 py-2 text-gray-500 text-sm'>
                        <p class="truncate">@convertnorp($item['total'])</p>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{-- <footer class='flex justify-between items-center px-6 pt-3 pb-4'>
            <p class="text-gray-700 text-sm">Page <span>{{ $holidays->currentPage() }}</span> of <span>
                    {{ $holidays->lastPage() }}</span></p>
            <div class='flex gap-3'>
                @if (!$holidays->onFirstPage())
                <button data-pagination-url="{{ $holidays->previousPageUrl() }}"
                    class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Previous</button>
                @endif
                @if ($holidays->hasMorePages() )
                <button data-pagination-url="{{ $holidays->nextPageUrl() }}"
                    class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Next</button>
                @endif
            </div>
        </footer> --}}
    </div>
</main>