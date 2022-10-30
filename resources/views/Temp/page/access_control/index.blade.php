<section class="border rounded-xl shadow-md w-max xs/max:w-full">
    <header class="px-6 py-5 flex items-center gap-3">
        <button onclick="getModalCreate()"
            class="flex gap-2 shadow-xs rounded-lg py-2 px-3.5 text-white text-sm font-medium flex items-center bg-green-600">
            <x-icon icon="plus" width=16 height=16 viewBox="20 20" />
            New access control
        </button>
    </header>
    <div class="w-full overflow-x-auto">
        <table class="table border-collapse w-full max-w">
            <thead class="border-y border-gray-200 bg-gray-50">
                <tr class="text-left">
                    <th class="py-3 px-6 text-xs font-medium text-gray-500">Roles</th>
                    <th class="py-3 px-6 text-xs font-medium text-gray-500 ">
                        <p class="truncate xs/max:w-12 ">Roles field</p>
                    </th>
                    <th class="py-3 px-6"></th>
                </tr>
            </thead>
            <tbody class="text-sm font-normal text-gray-700">
                @foreach ($roles as $item)
                <tr class="border-b border-grey/200 cursor-pointer hover:bg-grey/50">
                    <td class="px-6 py-3 text-left">
                        <p class="text-gray-500 text-sm font-normal truncate xs/max:w-12">{{ $item->name }}</p>
                    </td>
                    <td class="px-6 py-3 text-left">
                        <p class="text-gray-500 text-sm font-normal truncate xs/max:w-12 w-32">
                            @if ($item->name != 'admin')
                            @foreach ($item->permissions as $permission)
                            {{ $permission->name }}
                            @endforeach
                            @endif
                        </p>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex gap-1">
                            <button class="p-2.5 cursor-pointer text-gray-500 rounded hover:bg-gray-50 delete-btn">
                                <x-icon icon="trash-2" width=16 height=16 viewBox="20 20" />
                            </button>
                            <button class="p-2.5 cursor-pointer text-gray-500 rounded hover:bg-gray-50">
                                <x-icon icon="edit-2" width=16 height=16 viewBox="20 20" />
                            </button>
                        </div>
                    </td>
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
</section>