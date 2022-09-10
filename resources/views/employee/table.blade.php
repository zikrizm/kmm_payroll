<div class="w-full">
    <table class="border-separate border-spacing-y-2 w-full">
        <thead class="">
            <tr class="text-left">
                <th class="py-3 px-6 text-xs font-medium text-gray-500 xs/max:px-4 xs/max:py">
                    <div class="flex items-center gap-2">
                        <p>Absen id</p>
                        <x-icon icon="arrow-down" width=12 height=12 viewBox="20 20" />
                    </div>
                </th>
                <th class="py-3 px-6 text-xs font-medium text-gray-500 xs/max:px-4 xs/max:py">
                    <div class="flex items-center gap-2">
                        <p>Name</p>
                        <x-icon icon="arrow-down" width=12 height=12 viewBox="20 20" />
                    </div>
                </th>
                <th class="py-3 px-6 text-xs font-medium text-gray-500 xs/max:px-4 xs/max:py">
                    <div class="flex items-center gap-2">
                        <p>Username</p>
                        <x-icon icon="arrow-down" width=12 height=12 viewBox="20 20" />
                    </div>
                </th>
                <th class="py-3 px-6 text-xs font-medium text-gray-500 xs/max:px-4 xs/max:py">
                    <div class="flex items-center gap-2">
                        <p>Basic salary</p>
                        <x-icon icon="arrow-down" width=12 height=12 viewBox="20 20" />
                    </div>
                </th>
                <th class="py-3 px-6 text-xs font-medium text-gray-500 xs/max:px-4 xs/max:py">
                    <div class="flex items-center gap-2">
                        <p>Pay component</p>
                        <x-icon icon="arrow-down" width=12 height=12 viewBox="20 20" />
                    </div>
                </th>
                <th class="py-3 px-6 text-xs font-medium text-gray-500">
                    <div class="flex items-center gap-2">
                        <p>Status</p>
                        <x-icon icon="arrow-down" width=12 height=12 viewBox="20 20" />
                    </div>
                </th>
                @canany(['employee.update', 'employee.delete'])
                <th class="py-3 px-6"></th>
                @endcanany
            </tr>
        </thead>
        <tbody class="text-sm font-normal text-gray-700">
            @foreach ($employees as $item)
            <tr class="cursor-pointer bg-white hover:bg-gray-50">
                <td class="px-6 py-3 text-left bg-transparent">
                    <p class="text-violet-600 text-sm font-normal truncate xs/max:w-12">#{{ $item->absen_id }}</p>
                </td>
                <td class="px-6 py-4 text-left bg-transparent rounded-l-xl">
                    <div class="flex gap-3 items-center">
                        <img src="{{ asset('storage/profiles/'.$item->photo.'')}}" alt=""
                            class="w-10 object-contain h-10 min-w-[40px] min-h-[40px]">
                        <div>
                            <p class="text-gray-900 text-sm font-medium truncate sm/max:w-12">
                                {{ $item->surname }} {{ $item->first_name }} {{ $item->last_name }}</p>
                            <p class="text-gray-500 text-sm font-normal truncate sm/max:w-12">
                                {{ $item->email }}
                            </p>
                        </div>
                    </div>

                </td>
                <td class="px-6 py-3 text-left bg-transparent">
                    <p class="text-gray-500 text-sm font-normal truncate xs/max:w-12">{{ $item->username }}</p>
                </td>
                <td class="px-6 py-3 text-left bg-transparent">
                    <p class="text-gray-500 text-sm font-normal truncate xs/max:w-12">
                        @convert2($item->basic_salary)
                    </p>
                </td>
                <td class="px-6 py-3 text-left bg-transparent">
                    <p class="text-gray-500 text-sm font-normal truncate xs/max:w-12">
                        @convert2($item->pay_component)
                    </p>
                </td>
                <td class="px-6 py-3 text-left bg-transparent">
                    {!! $item->statusBox !!}
                </td>

                @canany(['employee.update', 'employee.delete'])
                <td class="px-4 py-4 bg-transparent rounded-r-xl">
                    <div class="flex justify-end gap-1">
                        @can('employee.delete')
                        <button onclick="openModalConfirm({{ $item->id }})"
                            class="p-2.5 cursor-pointer text-gray-500 rounded hover:bg-gray-50 delete-btn">
                            <x-icon icon="trash-2" width=16 height=16 viewBox="20 20" />
                        </button>
                        @endcan
                        @can('employee.update')
                        <button onclick="getModal({{ $item->id }})"
                            class="p-2.5 cursor-pointer text-gray-500 rounded hover:bg-gray-50">
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
<footer class="flex justify-between items-center pb-4 pt-3">
    <p class="text-gray-700 text-sm">Page <span>1</span> of <span>7</span></p>
    <div class="flex gap-3">
        <button
            class="bg-white px-3.5 py-2 border border-gray-200 rounded-lg text-sm font-medium hover:bg-gray-100 text-gray-500">
            Prev</button>
        <button
            class="bg-white px-3.5 py-2 border border-gray-200 rounded-lg text-sm font-medium hover:bg-gray-100 text-gray-500">
            Next</button>
    </div>
</footer>