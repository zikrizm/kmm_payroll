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
                                <x-ui.sort-table text="Tanggal" url="{{ route('request-task.index') }}"
                                    field="start_date" order="{{ $order }}" />
                            </div>
                        </div>
                    </th>
                    {{-- <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Jabatan</p>
                    </th> --}}
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Karyawan</p>
                    </th>
                    @canany(['request-task.update', 'request-task.delete'])
                    <th class='px-3 py-3 text-left text-gray-500 text-xs font-medium'></th>
                    @endcanany
                </tr>
            </thead>
            <tbody>
                @foreach ($request_tasks as $item)
                <tr class='hover:bg-gray-50 border-b border-gray-200 cursor-pointer'>
                    <td class='text-left'>
                        <div class="flex items-center">
                            <div class="pl-4 py-2">
                                {!! FormCustom::checkbox() !!}
                            </div>
                            <div class="flex gap-3 items-center px-6 py-3 hover:underline hover:text-gray-500 cursor-pointer text-gray-500 text-sm"
                                onclick="get_modal('{{ $item['id'] }}')">
                                <x-icon icon="calendar" width=18 height=18 viewBox="20 20" />
                                <p class="truncate text-sm">
                                    {{ date('Y-m-d', strtotime($item->start_date)) }} -
                                    {{ date('Y-m-d', strtotime($item->end_date)) }}
                                </p>
                            </div>
                        </div>
                    </td>
                    {{-- <td class='px-3 py text-gray-500 text-sm'>
                        {{ implode(', ', array_column($item->call_employee_helps->toArray(), 'emp_first_name')) }}
                    </td> --}}
                    <td class='px-3 py text-gray-500 text-sm'>
                        {{ implode(', ', array_column($item->request_task_has_emps->toArray(), 'emp_first_name')) }}
                    </td>
                    @canany(['request-task.update', 'request-task.delete'])
                    <td class='px-3 py'>
                        <div class='flex gap-1'>
                            @can('request-task.delete')
                            <button onclick="open_modal_confirm('{{ $item['id'] }}')"
                                class='px-2.5 cursor-pointer text-gray-500 delete-btn'>
                                <x-icon icon="trash-2" width=18 height=18 viewBox="20 20" />
                            </button>
                            @endcan
                            @can('request-task.update')
                            <button class='px-2.5 cursor-pointer text-gray-500 edit-btn'
                                onclick="get_modal('{{ $item['id'] }}')">
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
    </div>
    <footer class='flex justify-between items-center px-6 pt-3 pb-4'>
        <p class="text-gray-700 text-sm">Page <span>{{ $request_tasks->currentPage() }}</span> of <span>
                {{ $request_tasks->lastPage() }}</span></p>
        <div class='flex gap-3'>
            @if (!$request_tasks->onFirstPage())
            <button data-pagination-url="{{ $request_tasks->previousPageUrl() }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Previous</button>
            @endif
            @if ($request_tasks->hasMorePages())
            <button data-pagination-url="{{ $request_tasks->nextPageUrl() }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Next</button>
            @endif
        </div>
    </footer>
</main>