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
                            <x-ui.sort-table text="Name" url="{{ route('break-time.index') }}" field="name"
                                order="{{ $order }}" />
                        </div>
                    </div>
                </th>
                <th class='px-3  py-3 text-left cursor-pointer'>
                    <x-ui.sort-table text="Start time" url="{{ route('break-time.index') }}" field="start_time"
                        order="{{ $order }}" />
                </th>
                <th class='px-3  py-3 text-left cursor-pointer'>
                    <x-ui.sort-table text="End time" url="{{ route('break-time.index') }}" field="end_time"
                        order="{{ $order }}" />
                </th>
                <th class='px-3  py-3 text-left cursor-pointer'>
                    <x-ui.sort-table text="Duration" url="{{ route('break-time.index') }}" field="duration"
                        order="{{ $order }}" />
                </th>
                @canany(['break-time.update', 'break-time.delete'])
                <th class='px-3  py-3 text-left text-gray-500 text-xs font-medium'></th>
                @endcanany
            </tr>
        </thead>
        <tbody>
            @foreach ($break_times as $item)
            <tr class='hover:bg-gray-50 border-b border-gray-200'>
                <td class='text-left'>
                    <div class="flex items-center">
                        <div class="pl-4 py ">
                            {!! FormCustom::checkbox() !!}
                        </div>
                        <div class="flex gap-3 items-center px-6 py-3 hover:underline hover:text-gray-500 cursor-pointer"
                        onclick="get_modal('{{ $item['id'] }}')">
                            <p class="text-violet-600 font-medium text-sm underline decoration-violet-600 cursor-pointer">
                                {{ $item->name }}
                            </p>
                        </div>
                    </div>
                </td>
                <td class='px-3 py text-gray-500 text-sm'>
                    <div class="flex items-center gap-2">
                        <x-icon icon="clock" width=18 height=18 viewBox="20 20" />
                        <p class="truncate">
                            {{ date('H:i', strtotime($item->start_time)); }}
                        </p>
                    </div>
                </td>
                <td class='px-3 py text-gray-500 text-sm'>
                    <div class="flex items-center gap-2">
                        <x-icon icon="clock" width=18 height=18 viewBox="20 20" />
                        <p class="truncate">
                            {{ date('H:i', strtotime($item->end_time)); }}
                        </p>
                    </div>
                </td>
                <td class='px-3 py text-gray-500 text-sm'>
                    {{ $item->duration }}
                </td>
                @canany(['break-time.update', 'break-time.delete'])
                <td class='px-3 py'>
                    <div class='flex gap-1'>
                        @can('break-time.delete')
                        <button onclick="open_modal_confirm('{{ $item->id }}')"
                            class='p-2.5 cursor-pointer text-gray-500 delete-btn'>
                            <x-icon icon="trash-2" width=18 height=18 viewBox="20 20" />
                        </button>
                        @endcan
                        @can('break-time.update',)
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
        <p class="text-gray-700 text-sm">Page <span>{{ $break_times->currentPage() }}</span> of <span>
                {{ $break_times->lastPage() }}</span></p>
        <div class='flex gap-3'>
            @if (!$break_times->onFirstPage())
            <button data-pagination-url="{{ $break_times->previousPageUrl() }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Previous</button>
            @endif
            @if ($break_times->hasMorePages() )
            <button data-pagination-url="{{ $break_times->nextPageUrl() }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Next</button>
            @endif
        </div>
    </footer>
</main>