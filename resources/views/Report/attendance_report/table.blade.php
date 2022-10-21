<main class='border border-gray-200 rounded-lg shadow-sm w-max overflow-hidden'>
    <div class="w-full overflow-auto overflow-y-hidden">
        <table class='table border-collapse w-full'>
            <thead class='border-b border-gray-200 bg-gray-50'>
                <tr class=''>
                    <th class='text-left'>
                        <div class='flex items-center'>
                            <div class='pl-4 py-2 flex items-center'>
                                {!! FormCustom::checkbox() !!}
                            </div>
                            <div class='px-6 py-3 cursor-pointer flex-1'>
                                <x-ui.sort-table text="Tanggal absensi" url="{{ route('attendance-report.index') }}"
                                    field="date" order="{{ $order }}" />
                            </div>
                        </div>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <x-ui.sort-table text="Nama karyawan" url="{{ route('attendance-report.index') }}"
                            field="first_name" order="{{ $order }}" />
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Bagian</p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer text-center">Hari kerja</p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer text-center">Masuk</p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer text-center">Keluar</p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Shift</p>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendance_reports['data'] as $item)
                <tr class='hover:bg-gray-50 border-b border-gray-200 cursor-pointer'>
                    <td class='text-left'>
                        <div class="flex items-center">
                            <div class="pl-4 py-2">
                                {!! FormCustom::checkbox() !!}
                            </div>
                            <div class="flex gap-3 items-center px-6 py-3 cursor-pointer text-gray-500 text-sm">
                                <x-icon icon="calendar" width=18 height=18 viewBox="20 20" />
                                <p class="truncate ">
                                    {{ date('Y-m-d', strtotime($item['att_date'])) }}
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        <div class="flex items-center gap-2">
                            {{ $item['first_name'] ?? '-' }} {{ $item['last_name'] ?? '' }}
                        </div>
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        {{ $item['department'] ?? '-' }}
                    </td>
                    <td class='px-3 py text-gray-500 text-sm text-center'>
                        <p class="capitalize">{{ $item['shift']['weekday'] ?? '' }}</p>
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        <div class="flex items-center gap-2">
                            <x-icon icon="clock" width=18 height=18 viewBox="20 20" />
                            <p class="truncate">
                                {{ date('H:i', strtotime($item['first_punch'])); }}
                            </p>
                        </div>
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        <div class="flex items-center gap-2">
                            <x-icon icon="clock" width=18 height=18 viewBox="20 20" />
                            <p class="truncate">
                                {{ date('H:i', strtotime($item['last_punch'])); }}
                            </p>
                        </div>
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        @if (!empty($item['shift']) && !empty($item['shift']['id']))
                        ({{ $item['shift']['id'] ?? '' }})
                        @else
                        -
                        @endif {{ $item['shift']['name'] ?? '' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <footer class='flex justify-between items-center px-6 pt-3 pb-4'>
            <p class='text-gray-700 text-sm'> Page <span> {{ $attendance_reports['currentPage']}} </span>
                of <span> {{ $attendance_reports['lastPage']}}</span>
            </p>
            <div class='flex gap-3'>
                @if (!empty($attendance_reports['previous']))
                <button data-pagination-page="{{ $attendance_reports['previous'] }}"
                    class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Previous</button>
                @endif

                @if (!empty($attendance_reports['next']))
                <button data-pagination-page="{{ $attendance_reports['next'] }}"
                    class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Next</button>
                @endif
            </div>
        </footer>
    </div>
</main>