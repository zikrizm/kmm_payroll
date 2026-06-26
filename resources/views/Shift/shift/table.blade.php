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
                            <x-ui.sort-table text="Nama shift" url="{{ route('shift.index') }}" field="name"
                                order="{{ $order }}" />
                        </div>
                    </div>
                </th>
                <th class='px-3 py-3 text-left cursor-pointer'>
                    <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Department</p>
                </th>
                <th class='px-3 py-3 text-left cursor-pointer'>
                    <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Status</p>
                </th>
                @canany(['shift.update', 'shift.delete'])
                <th class='px-3 py-3 text-left text-gray-500 text-xs font-medium'></th>
                @endcanany
            </tr>
        </thead>
        <tbody>
            @foreach ($shifts as $item)
            <tr class='hover:bg-gray-50 border-b border-gray-200'>
                <td class='text-left'>
                    <div class="flex items-center">
                        <div class="pl-4 py ">
                            {!! FormCustom::checkbox() !!}
                        </div>
                        <div class="flex gap-3 items-center px-6 py-3 cursor-pointer"
                            onclick="get_modal('{{ $item->id }}')">
                            <p class="text-violet-600 font-medium text-sm underline decoration-violet-600 cursor-pointer">
                                {{ $item->name }}
                            </p>
                        </div>
                    </div>
                </td>
                <td class='px-3 py text-gray-500 text-sm'>
                    {{ $item->department['dept_name'] ?? '' }}
                </td>
                <td class='px-3 py text-gray-500 text-sm'>
                    @if (($item->status ?? 'active') == 'active')
                    @can('shift.update')
                    <button type="button"
                        class="px-2 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full cursor-pointer hover:bg-green-200"
                        data-shift-id="{{ $item->id }}"
                        data-shift-name="{{ $item->name }}"
                        data-dept-name="{{ $item->department['dept_name'] ?? '' }}"
                        data-status="active"
                        onclick="open_status_confirm(this)">
                        Active
                    </button>
                    @else
                    <span class="px-2 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full">Active</span>
                    @endcan
                    @else
                    @can('shift.update')
                    <button type="button"
                        class="px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-full cursor-pointer hover:bg-red-200"
                        data-shift-id="{{ $item->id }}"
                        data-shift-name="{{ $item->name }}"
                        data-dept-name="{{ $item->department['dept_name'] ?? '' }}"
                        data-status="inactive"
                        onclick="open_status_confirm(this)">
                        Inactive
                    </button>
                    @else
                    <span class="px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-full">Inactive</span>
                    @endcan
                    @endif
                </td>
                @canany(['shift.update', 'shift.delete'])
                <td class='px-3 py'>
                    <div class='flex gap-1'>
                        @can('shift.delete')
                        <button onclick="open_modal_confirm('{{ $item->id }}')"
                            class='p-2.5 cursor-pointer text-gray-500 delete-btn'>
                            <x-icon icon="trash-2" width=18 height=18 viewBox="20 20" />
                        </button>
                        @endcan
                        @can('shift.update',)
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
        <p class="text-gray-700 text-sm">Page <span>{{ $shifts->currentPage() }}</span> of <span>
                {{ $shifts->lastPage() }}</span></p>
        <div class='flex gap-3'>
            @if (!$shifts->onFirstPage())
            <button data-pagination-url="{{ $shifts->previousPageUrl() }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Previous</button>
            @endif
            @if ($shifts->hasMorePages() )
            <button data-pagination-url="{{ $shifts->nextPageUrl() }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Next</button>
            @endif
        </div>
    </footer>
</main>