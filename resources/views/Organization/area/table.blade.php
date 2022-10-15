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
                            <x-ui.sort-table text="Area code" url="{{ route('area.index') }}" field="area_code"
                                order="{{ $order }}" />
                        </div>
                    </div>
                </th>
                <th class='px-3 py-3 text-left cursor-pointer'>
                    <x-ui.sort-table text="Area name" url="{{ route('area.index') }}" field="area_name"
                        order="{{ $order }}" />
                </th>
                <th class='px-3 py-3 text-left cursor-pointer'>
                    <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Parent area</p>
                </th>
                @canany(['area.update', 'area.delete'])
                <th class='px-3 py-3 text-left text-gray-500 text-xs font-medium'></th>
                @endcanany
            </tr>
        </thead>
        <tbody>
            @foreach ($areas['data'] as $item)
            <tr class='hover:bg-gray-50 border-b border-gray-200'>
                <td class='text-left'>
                    <div class="flex items-center">
                        <div class="pl-4 py-2">
                            {!! FormCustom::checkbox() !!}
                        </div>
                        <div class="flex gap-3 items-center px-6 py-3 hover:underline hover:text-gray-500 cursor-pointer"
                            onclick="get_modal('{{ $item['id'] }}')">
                            <p class="text-gray-500 text-sm">
                                {{ $item['area_code'] }}
                            </p>
                        </div>
                    </div>
                </td>
                <td class='px-3 py text-gray-500 text-sm'>
                    {{ $item['area_name'] }}
                </td>
                <td class='px-3 py text-gray-500 text-sm'>
                    {{ (!empty($item['parent_area'])) ? $item['parent_area']['area_name'] : '-' }}
                </td>
                @canany(['area.update', 'area.delete'])
                <td class='px-3 py'>
                    <div class='flex gap-1'>
                        @can('area.delete')
                        <button onclick="open_modal_confirm('{{ $item['id'] }}')"
                            class='px-2.5 cursor-pointer text-gray-500 delete-btn'>
                            <x-icon icon="trash-2" width=18 height=18 viewBox="20 20" />
                        </button>
                        @endcan
                        @can('area.update')
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
    <footer class='flex justify-between items-center px-6 pt-3 pb-4'>
        @php $page = 1; @endphp
        <p class='text-gray-700 text-sm'>
            Page <span> {{ $page }} </span> of <span>{{ ceil(($areas['count'] ?? 0) / 10) }}</span>
        </p>
        <div class='flex gap-3'>
            @if (!empty($areas['previous']))
            <button data-pagination-url="{{ $areas['previous'] }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Previous</button>
            @elseif (!empty($areas['next']))
            <button data-pagination-url="{{ $areas['next'] }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Next</button>
            @endif
        </div>
    </footer>
</main>