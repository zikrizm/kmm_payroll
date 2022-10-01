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
                            <x-ui.sort-table text="Role name" url="{{ route('role.index') }}" field="name"
                                order="{{ $order }}" />
                        </div>
                    </div>
                </th>
                @canany(['user.update', 'user.delete'])
                <th class='px-6 py-3 text-left text-gray-500 text-xs font-medium'></th>
                @endcanany
            </tr>
        </thead>
        <tbody>
            @foreach ($roles as $item)
            <tr class='hover:bg-gray-50 border-b border-gray-200'>
                <td class='text-left w-72'>
                    <div class="flex items-center">
                        <div class="pl-4 py">
                            {!! FormCustom::checkbox() !!}
                        </div>
                        <div class="flex gap-3 items-center px-6 py-3">
                            <p class="text-gray-500 text-sm font-medium truncate">
                                {{ $item->name }}
                            </p>
                        </div>
                </td>
                <td class='px-3 py'>
                    <div class='flex gap-1'>
                        <button onclick="open_modal_confirm('{{ $item->id }}')"
                            class='p-2.5 cursor-pointer text-gray-500 delete-btn'>
                            <x-icon icon="trash-2" width=18 height=18 viewBox="20 20" />
                        </button>
                        <button class='p-2.5 cursor-pointer text-gray-500 edit-btn'
                            onclick="get_modal('{{ $item->id }}')">
                            <x-icon icon="edit" width=18 height=18 viewBox="20 20" />
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <footer class='flex justify-between items-center px-6 pt-3 pb-4'>
        <p class="text-gray-700 text-sm">Page <span>{{ $roles->currentPage() }}</span> of <span>
                {{ $roles->lastPage() }}</span></p>
        <div class='flex gap-3'>
            @if (!$roles->onFirstPage())
            <button data-pagination-url="{{ $roles->previousPageUrl() }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Previous</button>
            @endif
            @if ($roles->hasMorePages() )
            <button data-pagination-url="{{ $roles->nextPageUrl() }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Next</button>
            @endif
        </div>
    </footer>
</main>