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
                        <p>Start date</p>
                    </div>
                </th>
                <th class="py-3 px-6 text-xs font-medium text-gray-500">
                    <div class="flex items-center gap-2">
                        <p>End date</p>
                    </div>
                </th>
                <th class="py-3 px-6 text-xs font-medium text-gray-500">
                    <div class="flex items-center gap-2">
                        <p>Notes</p>
                    </div>
                </th>
                <th class="py-3 px-6 text-xs font-medium text-gray-500">
                    <div class="flex items-center gap-2">
                        <p>Status</p>
                    </div>
                </th>
                @canany(['holiday.update', 'holiday.delete'])
                <th class="py-3 px-6"></th>
                @endcanany
            </tr>
        </thead>
        <tbody class="text-sm font-normal text-gray-700">
            @foreach ($holidays as $item)
            <tr class="cursor-pointer bg-white hover:bg-gray-50">
                <td class="px-6 py-3 text-left bg-transparent rounded-l-xl">
                    <p class="text-gray-500 text-sm font-normal truncate xs/max:w-12 capitalize">{{ $item->name }}</p>
                </td>
                <td class="px-6 py-3 text-left bg-transparent">
                    <p class="text-gray-500 text-sm font-normal truncate xs/max:w-12 flex items-center gap-1.5">
                        <x-icon icon="calendar" width=16 height=16 viewBox="20 20" />
                        {{ $item->start_date }}
                    </p>
                </td>
                <td class="px-6 py-3 text-left bg-transparent">
                    <p class="text-gray-500 text-sm font-normal truncate xs/max:w-12 flex items-center gap-1.5">
                        <x-icon icon="calendar" width=16 height=16 viewBox="20 20" />
                        {{ $item->end_date }}
                    </p>
                </td>
                <td class="px-6 py-3 text-left bg-transparent">
                    <p class="text-gray-500 text-sm font-normal truncate xs/max:w-12 xl/max:w-20 flex items-center gap-1.5">
                        {{ $item->notes }}
                    </p>
                </td>
                <td class="px-6 py-3 text-left bg-transparent ">
                    {!! $item->statusBox !!}
                </td>
                @canany(['holiday.update', 'holiday.delete'])
                <td class="px-4 py-4 bg-transparent rounded-r-xl">
                    <div class="flex justify-end gap-1">
                        @can('holiday.delete')
                        <button onclick="open_modal_confirm({{ $item->id }})"
                            class="px-2.5 cursor-pointer text-gray-500 rounded hover:bg-gray-50 delete-btn">
                            <x-icon icon="trash-2" width=16 height=16 viewBox="20 20" />
                        </button>
                    @endcan
                        @can('holiday.update')
                        <button onclick="get_modal({{ $item->id }})"
                            class="px-2.5 cursor-pointer text-gray-500 rounded hover:bg-gray-50">
                            <x-icon icon="edit-2" width=14 height=14 viewBox="20 20" />
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
<x-ui.pagination-custom :pagination="$holidays"/>