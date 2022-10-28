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
                                <x-ui.sort-table text="Waktu" url="{{ route('activity-log.index') }}" field="date"
                                    order="{{ $order }}" />
                            </div>
                        </div>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <x-ui.sort-table text="Username" url="{{ route('activity-log.index') }}" field="user.username"
                            order="{{ $order }}" />
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Halaman</p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Action</p>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($activity_logs as $item)
                <tr class='hover:bg-gray-50 border-b border-gray-200 cursor-pointer'>
                    <td class='text-left'>
                        <div class="flex items-center">
                            <div class="pl-4 py-2">
                                {!! FormCustom::checkbox() !!}
                            </div>
                            <div class="flex gap-3 items-center px-6 py-3 text-gray-500 text-sm">
                                <p class="truncate ">
                                    {{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item->date, 'UTC')->setTimezone('Asia/Jakarta')->format('F d, Y H:i:s T'); }}
                                </p>
                              
                            </div>
                        </div>
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        <div class="flex items-center gap-2">
                            {{ $item->user->username }}
                        </div>
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        {{ $item->action ?? '' }}
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        {{ $item->description ?? '' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <footer class='flex justify-between items-center px-6 pt-3 pb-4'>
            <p class="text-gray-700 text-sm">Page <span>{{ $activity_logs->currentPage() }}</span> of <span>
                    {{ $activity_logs->lastPage() }}</span></p>
            <div class='flex gap-3'>
                @if (!$activity_logs->onFirstPage())
                <button data-pagination-url="{{ $activity_logs->previousPageUrl() }}"
                    class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Previous</button>
                @endif
                @if ($activity_logs->hasMorePages())
                <button data-pagination-url="{{ $activity_logs->nextPageUrl() }}"
                    class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Next</button>
                @endif
            </div>
        </footer>
    </div>
</main>