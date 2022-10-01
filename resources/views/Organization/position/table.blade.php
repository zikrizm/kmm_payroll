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
                            <x-ui.sort-table text="Position code" url="{{ route('position.index') }}"
                                field="position_code" order="{{ $order }}" />
                        </div>
                    </div>
                </th>
                <th class='px-6 py-3 text-left cursor-pointer'>
                    <x-ui.sort-table text="Position name" url="{{ route('position.index') }}" field="position_name"
                        order="{{ $order }}" />
                </th>
                <th class='px-6 py-3 text-left cursor-pointer'>
                    <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Parent position</p>
                </th>
                @canany(['position.update', 'position.delete'])
                <th class='px-6 py-3 text-left text-gray-500 text-xs font-medium'></th>
                @endcanany
            </tr>
        </thead>
        <tbody>
            @foreach ($positions['data'] as $item)
            <tr class='hover:bg-gray-50 border-b border-gray-200'>
                <td class='text-left'>
                    <div class="flex items-center">
                        <div class="pl-4 py-2 ">
                            {!! FormCustom::checkbox() !!}
                        </div>
                        <div class="flex gap-3 items-center px-6 py-3 ">
                            <p class="text-gray-500 text-sm">
                                {{ $item['position_code'] }}
                            </p>
                        </div>
                    </div>
                </td>
                <td class='px-6 py-4 text-gray-500 text-sm'>
                    {{ $item['position_name'] }}
                </td>
                <td class='px-6 py-4 text-gray-500 text-sm'>
                    {{ (!empty($item['parent_position'])) ? $item['parent_position']['position_name'] : '-' }}
                </td>
                @canany(['position.update', 'position.delete'])
                <td class='px-4 py-4'>
                    <div class='flex gap-1'>
                        @can('position.delete')
                        <button onclick="open_modal_confirm('{{ $item['id'] }}')"
                            class='p-2.5 cursor-pointer text-gray-500 delete-btn'>
                            <x-icon icon="trash-2" width=18 height=18 viewBox="20 20" />
                        </button>
                        @endcan
                        @can('position.update')
                        <button class='p-2.5 cursor-pointer text-gray-500 edit-btn'
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
        <p class='text-gray-700 text-sm'>
            Page <span> 1 </span> of <span>{{ ceil($positions['count'] / 10) }}</span>
        </p>
        <div class='flex gap-3'>
            @if (!empty($positions['previous']))
            <button data-pagination-url=""
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Previous</button>
            @elseif (!empty($positions['next']))
            <button data-pagination-url=""
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Next</button>
            @endif
        </div>
    </footer>
</main>