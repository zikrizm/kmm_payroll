<div class="w-full">
    <table class="border-separate border-spacing-y-2 w-full">
        <thead class="">
            <tr class="text-left">
                <th class="py-3 px-6 text-xs font-medium text-gray-500 xs/max:px-4 xs/max:py">
                    <div class="flex items-center gap-2">
                        <p>Name</p>
                    </div>
                </th>
                <th class="py-3 px-6 text-xs font-medium text-gray-500">
                    <div class="flex items-center gap-2">
                        <p>Roles field</p>
                    </div>
                </th>
                @canany(['access-control.update', 'access-control.delete'])
                <th class="py-3 px-6"></th>
                @endcanany
            </tr>
        </thead>
        <tbody class="text-sm font-normal text-gray-700">
            @foreach ($roles as $item)
            <tr class="cursor-pointer hover:bg-gray-50">
                <td class="px-6 py-3 text-left bg-white rounded-l-xl">
                    <p class="text-gray-500 text-sm font-normal truncate xs/max:w-12">{{ $item->name }}</p>
                </td>
                <td class="px-6 py-3 text-left bg-white overflow-hidden" width="80%">
                    <div class="flex items-end gap-2 overflow-hidden">
                        @foreach ($item->permissions->slice(0, 5) as $permission)
                        <div class="text-gary-700 border rounded-lg px-2 py-1 truncate">
                            {{ $permission->name }}
                        </div>
                        @endforeach
                        @if (count($item->permissions) > 5)
                            ...
                        @endif
                    </div>
                </td>
                @canany(['access-control.update', 'access-control.delete'])
                <td class="px-4 py-3 bg-white rounded-r-xl">
                    @if (!$item->is_default)
                    <div class="flex justify-end gap-1">
                        @can('access-control.delete')
                        <button onclick="open_modal_confirm({{ $item->id }})"
                            class="p-2.5 cursor-pointer text-gray-500 rounded hover:bg-gray-50 delete-btn">
                            <x-icon icon="trash-2" width=16 height=16 viewBox="20 20" />
                        </button>
                        @endcan
                        @can('access-control.update')
                        <button onclick="get_modal({{ $item->id }})"
                            class="p-2.5 cursor-pointer text-gray-500 rounded hover:bg-gray-50">
                            <x-icon icon="edit-2" width=16 height=16 viewBox="20 20" />
                        </button>
                        @endcan

                    </div>
                    @endif
                </td>
                @endcanany
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<x-ui.pagination-custom :pagination="$roles"/>