<main class='border border-gray-200 rounded-lg shadow-sm w-max overflow-hidden'>
    <table class='table border-collapse w-full'>
        <thead class='border-b border-gray-200 bg-gray-50'>
            <tr class=''>
                <th class='text-left'>
                    <div class='flex items-center'>
                        <div class='pl-4 py-2 flex items-center'>
                            {!! FormCustom::checkbox() !!}
                        </div>
                        <div class='px-6 py-3 cursor-pointer flex-1'>
                            <x-ui.sort-table text="Name" url="{{ route('timetable.index') }}" field="name"
                                order="{{ $order }}" />
                        </div>
                    </div>
                </th>
                <th class='px-3 py-3 text-left cursor-pointer'>
                    <x-ui.sort-table text="Masuk" url="{{ route('timetable.index') }}" field="in_time"
                        order="{{ $order }}" />
                </th>
                <th class='px-3 py-3 text-left cursor-pointer'>
                    <x-ui.sort-table text="Keluar" url="{{ route('timetable.index') }}" field="out_time"
                        order="{{ $order }}" />
                </th>
                <th class='px-3 py-3 text-left cursor-pointer'>
                    <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Waktu Kerja</p>
                </th>
                <th class='px-3 py-3 text-left cursor-pointer'>
                    <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Tipe Hari</p>
                </th>
                <th class='px-3 py-3 text-left cursor-pointer'>
                    <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Beda Hari</p>
                </th>
                <th class='px-3 py-3 text-left cursor-pointer'>
                    <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Istirahat</p>
                </th>
                @canany(['timetable.update', 'timetable.delete'])
                <th class='px-3 py-3 text-left text-gray-500 text-xs font-medium'></th>
                @endcanany
            </tr>
        </thead>
        <tbody>
            @foreach ($timetables as $item)
            <tr class='hover:bg-gray-50 border-b border-gray-200'>
                <td class='text-left'>
                    <div class="flex items-center">
                        <div class="pl-4 py ">
                            {!! FormCustom::checkbox() !!}
                        </div>
                        <div class="flex gap-3 items-center px-6 py-3 underline decoration-violet-600 cursor-pointer"
                            onclick="get_modal('{{ $item['id'] }}')">
                            <p class="text-violet-600 text-sm">
                                {{ $item->name }}
                            </p>
                        </div>
                    </div>
                </td>
                <td class='px-3 py text-gray-500 text-sm'>
                    <div class="flex items-center gap-2">
                        <x-icon icon="clock" width=18 height=18 viewBox="20 20" />
                        <p class="truncate">
                            {{ date('H:i', strtotime($item->in_time)); }}
                        </p>
                    </div>
                </td>
                <td class='px-3 py text-gray-500 text-sm'>
                    <div class="flex items-center gap-2">
                        <x-icon icon="clock" width=18 height=18 viewBox="20 20" />
                        <p class="truncate">
                            {{ date('H:i', strtotime($item->out_time)); }}
                        </p>
                    </div>
                </td>
                <td class='px-3 py text-gray-500 text-sm'>
                    {{ $item->work_time }}
                </td>
                <td class='px-3 py text-gray-500 text-sm'>
                    @switch($item->work_type)
                    @case(0)
                    Hari kerja
                    @break
                    @case(1)
                    Minggu
                    @break
                    @case(2)
                    Libur nasional
                    @break
                    @default

                    @endswitch
                </td>
                <td class='px-3 py text-gray-500 text-sm'>
                    {{ $item->cross_day ?? '-' }}
                </td>
                <td class='px-3 py text-gray-500 text-sm'>
                    @forelse ($item->timetable_has_break_time as $key => $itemhas)
                    {{ $itemhas->break_time->name }}@if(( $item->timetable_has_break_time->count()-1) != $key),@endif
                    @empty
                    -
                    @endforelse
                </td>
                @canany(['timetable.update', 'timetable.delete'])
                <td class='px-3 py'>
                    <div class='flex gap-1'>
                        @can('timetable.delete')
                        <button onclick="open_modal_confirm('{{ $item->id }}')"
                            class='p-2.5 cursor-pointer text-gray-500 delete-btn'>
                            <x-icon icon="trash-2" width=18 height=18 viewBox="20 20" />
                        </button>
                        @endcan
                        @can('timetable.update',)
                        <button class='p-2.5 cursor-pointer text-gray-500 edit-btn'
                            onclick="get_modal('{{ $item->id }}')">
                            <x-icon icon="edit" width=18 height=18 viewBox="20 20" />
                        </button>
                        @endcan
                    </div>
                </td>
                @endcanany
            </tr>
            @endforeach
        </tbody>
    </table>
    <footer class='flex justify-between items-center px-6 pt-3 pb-4'>
        <p class="text-gray-700 text-sm">Page <span>{{ $timetables->currentPage() }}</span> of <span>
                {{ $timetables->lastPage() }}</span></p>
        <div class='flex gap-3'>
            @if (!$timetables->onFirstPage())
            <button data-pagination-url="{{ $timetables->previousPageUrl() }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Previous</button>
            @endif
            @if ($timetables->hasMorePages() )
            <button data-pagination-url="{{ $timetables->nextPageUrl() }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Next</button>
            @endif
        </div>
    </footer>
</main>