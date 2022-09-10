<div class="w-full overflow-x-auto">
    <table class="table border-collapse w-full max-w">
        <thead class="border-b border-gray-200 bg-gray-50">
            <tr class="text-left">
                <th class="py-3 px-6 text-xs font-medium text-gray-500 xs/max:px-4 xs/max:py">Name</th>
                <th class="py-3 px-6 text-xs font-medium text-gray-500">
                    <p class="truncate xs/max:w-12">City</p>
                </th>
                <th class="py-3 px-6 text-xs font-medium text-gray-500">zip coxe</th>
                <th class="py-3 px-6 text-xs font-medium text-gray-500">state</th>
                <th class="py-3 px-6 text-xs font-medium text-gray-500">country</th>
                <th class="py-3 px-6 text-xs font-medium text-gray-500">full address</th>
                <th class="py-3 px-6"></th>
            </tr>
        </thead>
        <tbody class="text-sm font-normal text-gray-700">
            @foreach ($locations as $item)
            <tr class="border-b border-grey/200 cursor-pointer hover:bg-grey/50">
                <td class="px-6 py-3 text-left">
                    <p class="text-gray-500 text-sm font-normal truncate xs/max:w-12">{{ $item->name }}</p>
                </td>
                <td class="px-6 py-3 text-left">
                    <p class="text-gray-500 text-sm font-normal truncate xs/max:w-12">{{ $item->city }}</p>
                </td>
                <td class="px-6 py-3 text-left">
                    <p class="text-gray-500 text-sm font-normal truncate xs/max:w-12">{{ $item->zip_code }}</p>
                </td>
                <td class="px-6 py-3 text-left">
                    <p class="text-gray-500 text-sm font-normal truncate xs/max:w-12">{{ $item->state }}</p>
                </td>
                <td class="px-6 py-3 text-left">
                    <p class="text-gray-500 text-sm font-normal truncate xs/max:w-12">{{ $item->country }}</p>
                </td>
                <td class="px-6 py-3 text-left">
                    <p class="text-gray-500 text-sm font-normal truncate xs/max:w-12">{{ $item->full_address }}</p>
                </td>
                @canany(['user.update', 'user.delete'])
                <td class="px-4 py-4">
                    <div class="flex gap-1">
                        @can('user.delete')
                        <button onclick="" class=" p-2.5 cursor-pointer text-gray-500 rounded hover:bg-gray-50
                            delete-btn">
                            <x-icon icon="trash-2" width=16 height=16 viewBox="20 20" />
                        </button>
                        @endcan
                        @can('user.update')
                        <button onclick="" class="p-2.5 cursor-pointer text-gray-500 rounded hover:bg-gray-50">
                            <x-icon icon="edit-2" width=16 height=16 viewBox="20 20" />
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
<footer class="flex justify-between items-center px-6 pt-3 pb-4">
    <p class="text-gray-700 text-sm">Page <span>1</span> of <span>7</span></p>
    <div class="flex gap-3"><button
            class="px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 text-gray-700">Next</button>
    </div>
</footer>