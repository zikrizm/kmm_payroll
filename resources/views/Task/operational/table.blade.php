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
                                <x-ui.sort-table text="Tanggal operasional" url="{{ route('operational.index') }}"
                                    field="date" order="{{ $order }}" />
                            </div>
                        </div>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Bagian</p>
                    </th>
                    {{-- <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Group</p>
                    </th> --}}
                    @canany(['operational.update', 'operational.delete'])
                    <th class='px-3 py-3 text-left text-gray-500 text-xs font-medium'></th>
                    @endcanany
                </tr>
            </thead>
            <tbody>
                @foreach ($operationals as $item)
                <tr class='hover:bg-gray-50 border-b border-gray-200 cursor-pointer'>
                    <td class='text-left'>
                        <div class="flex items-center">
                            <div class="pl-4 py-2">
                                {!! FormCustom::checkbox() !!}
                            </div>
                            <div class="flex gap-3 items-center px-6 py-3 hover:underline hover:text-gray-500 cursor-pointer text-gray-500 text-sm"
                                onclick="get_modal('{{ $item->id }}')">
                                <x-icon icon="calendar" width=18 height=18 viewBox="20 20" />
                                <p class="truncate ">
                                    {{ date('Y-m-d', strtotime($item->start_date)) }} -
                                    {{ date('Y-m-d', strtotime($item->end_date)) }}
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        <p class="truncate">({{ $item->dept_id }}) {{ $item->dept_name }}</p>
                    </td>
                    {{-- <td class='px-3 py text-gray-500 text-sm'>
                        <div class="flex items-center gap-2">
                            @foreach ($item->operational_has_depts as $dept)
                            @php
                            $is_active = $dept->status == 'active';
                            @endphp
                            <div
                                class="flex items-center gap-1 rounded-xl px-2.5 py-0.5 w-max {{ ($is_active) ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                <span
                                    class="w-1.5 h-1.5 rounded-full block {{ ($is_active) ? 'bg-green-700' : 'bg-red-700' }}"></span>
                                <p class="text-xs font-normal flex items-center gap-1 capitalize {{ ($is_active) ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $dept->dept_name }}
                                </p>
                            </div>
                            @endforeach
                        </div>
                    </td> --}}
                    @canany(['operational.update', 'operational.delete'])
                    <td class='px-3 py'>
                        <div class='flex gap-1'>
                            @can('operational.delete')
                            <button onclick="open_modal_confirm('{{ $item['id'] }}')"
                                class='px-2.5 cursor-pointer text-gray-500 delete-btn'>
                                <x-icon icon="trash-2" width=18 height=18 viewBox="20 20" />
                            </button>
                            @endcan
                            @can('operational.update')
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
        <p class="text-gray-700 text-sm">Page <span>{{ $operationals->currentPage() }}</span> of <span>
                {{ $operationals->lastPage() }}</span></p>
        <div class='flex gap-3'>
            @if (!$operationals->onFirstPage())
            <button data-pagination-url="{{ $operationals->previousPageUrl() }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Previous</button>
            @endif
            @if ($operationals->hasMorePages())
            <button data-pagination-url="{{ $operationals->nextPageUrl() }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Next</button>
            @endif
        </div>
    </footer>
</main>