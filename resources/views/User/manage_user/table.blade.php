<main class='border border-gray-200 rounded-lg shadow-sm w-max overflow-hidden'>
    <table class='table border-collapse w-full'>
        <thead class='border-b border-gray-200 bg-gray-50'>
            <tr class=''>
                <th class='text-left'>
                    <div class='flex items-center'>
                        {{-- <div class='pl-4 py-2 flex items-center'>
                            {!! FormCustom::checkbox() !!}
                        </div> --}}
                        <div class='px-6 py-3 cursor-pointer flex-1'>
                            <x-ui.sort-table text="Nama & Email" url="{{ route('user.index') }}" field="email"
                                order="{{ $order }}" />
                        </div>
                    </div>
                </th>
                <th class='px-3 py-3 text-left cursor-pointer'>
                    <x-ui.sort-table text="Username" url="{{ route('user.index') }}" field="username"
                        order="{{ $order }}" />
                </th>
                <th class='px-3 py-3 text-left cursor-pointer'>
                    <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Wewenang</p>
                </th>
                <th class='px-3 py-3 text-left cursor-pointer'>
                    <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Status</p>
                </th>
                @canany(['user.update', 'user.delete'])
                    <th class='px-3 py-3 text-left text-gray-500 text-xs font-medium'></th>
                @endcanany
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $item)
                <tr class='hover:bg-gray-50 border-b border-gray-200'>
                    <td class='text-left'>
                        <div class="flex items-center">
                            {{-- <div class="pl-4 py">
                                {!! FormCustom::checkbox() !!}
                            </div> --}}
                            <div class="flex gap-3 items-center px-6 py-1">
                                <div class="w-8 h-8 min-w-[32px] min-h-[32px] rounded-full overflow-hidden">
                                    @if (!empty($item->photo))
                                        <img src="{{ $item->photo }}" alt=""
                                            class="w-full h-full object-cover">
                                    @else
                                        <img src="@zkPhoto(files / nophoto . gif)" alt="" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div>
                                    <p onclick="get_modal('{{ $item->id }}')"
                                        class="text-violet-700 text-sm font-medium truncate sm/max:w-12 underline decoration-violet-600 cursor-pointer">
                                        {{ $item->name }}
                                    </p>
                                    <p class="text-gray-500 text-sm font-normal truncate sm/max:w-12">
                                        {{ $item->email }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        {{ $item->username }}
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        @foreach ($item->roles as $role)
                            {{ $role->name }}
                        @endforeach
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        {!! $item->statusBox !!}
                    </td>
                    @canany(['user.update', 'user.delete'])
                        <td class='px-3 py'>
                            <div class='flex gap-1'>
                                @can('user.delete')
                                    <button type="button" onclick="open_modal_confirm('{{ $item->id }}')"
                                        class='px-2.5 cursor-pointer text-gray-500 delete-btn'>
                                        <x-icon icon="trash-2" width=18 height=18 viewBox="20 20" />
                                    </button>
                                @endcan
                                @can('user.update')
                                    <button class='px-2.5 cursor-pointer text-gray-500 edit-btn'
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
        <p class="text-gray-700 text-sm">Page <span>{{ $users->currentPage() }}</span> of <span>
                {{ $users->lastPage() }}</span></p>
        <div class='flex gap-3'>
            @if (!$users->onFirstPage())
                <button data-pagination-url="{{ $users->previousPageUrl() }}"
                    class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Previous</button>
            @endif
            @if ($users->hasMorePages())
                <button data-pagination-url="{{ $users->nextPageUrl() }}"
                    class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Next</button>
            @endif
        </div>
    </footer>
</main>
