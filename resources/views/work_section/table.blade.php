<div class="w-full">
    <table class="border-separate border-spacing-y-2 w-full">
        <thead class="">
            <tr class="text-left">
                <th class="py-3 px-6 text-xs font-medium text-gray-500 xs/max:px-4 xs/max:py">
                    <div class="flex items-center gap-2">
                        <p>Name</p>
                    </div>
                </th>
                <th class="py-3 px-6 text-xs font-medium text-gray-500 xs/max:px-4 xs/max:py">
                    <div class="flex items-center gap-2">
                        <p>Shift</p>
                    </div>
                </th>
                <th class="py-3 px-6 text-xs font-medium text-gray-500">
                    <div class="flex items-center gap-2">
                        <p>Overtime pay</p>
                    </div>
                </th>
                <th class="py-3 px-6 text-xs font-medium text-gray-500">
                    <div class="flex items-center gap-2">
                        <p>Status</p>
                    </div>
                </th>
                @canany(['shift.update', 'shift.delete'])
                <th class="py-3 px-6"></th>
                @endcanany
            </tr>
        </thead>
        <tbody class="text-sm font-normal text-gray-700">
            @foreach ($work_sections as $item)
            <tr class="cursor-pointer bg-white hover:bg-gray-50">
                <td class="px-6 py-3 text-left bg-transparent rounded-l-xl">
                    <p class="text-gray-500 text-sm font-normal truncate xs/max:w-12">{{ $item->name }}</p>
                </td>
                <td class="px-6 py-3 text-left bg-transparent">
                    <p class="text-gray-500 text-sm font-normal truncate xs/max:w-12">{{ $item->shift->name }}</p>
                </td>
                <td class="px-6 py-3 text-left bg-transparent">
                    <p class="text-gray-500 text-sm font-normal truncate xs/max:w-12">per {{ $item->payment_period }} menit (Rp) @convert($item->pay)
                    </p>
                </td>
                <td class="px-6 py-3 text-left bg-transparent">
                    {!! $item->statusBox !!}
                </td>
                @canany(['shift.update', 'shift.delete'])
                <td class="px-4 py-3 bg-transparent rounded-r-xl">
                    <div class="flex justify-end gap-1">
                        @can('shift.delete')
                        <button onclick="open_modal_confirm({{ $item->id }})"
                            class="px-2.5 cursor-pointer text-gray-500 rounded hover:bg-gray-50 delete-btn">
                            <x-icon icon="trash-2" width=16 height=16 viewBox="20 20" />
                        </button>
                        @endcan
                        @can('shift.update')
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
<x-ui.pagination-custom :pagination="$work_sections"/>