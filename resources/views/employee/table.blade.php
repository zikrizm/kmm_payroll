<div class="w-full overflow-x-auto">
    <table class="border-separate border-spacing-y-2 w-full">
        <thead class="">
            <tr class="text-left">
                <th class="py-3 px-4 text-xs font-medium text-gray-500 xs/max:px-4 xs/max:py">
                    <p>Kode</p>
                </th>
                <th class="py-3 px-4 text-xs font-medium text-gray-500 xs/max:px-4 xs/max:py">
                    <p>Nama</p>
                </th>
                <th class="py-3 px-4 text-xs font-medium text-gray-500 xs/max:px-4 xs/max:py">
                    <p class="truncate xl/max:w-20">Tanggal bergabung</p>
                </th>
                <th class="py-3 px-4 text-xs font-medium text-gray-500 xs/max:px-4 xs/max:py">
                    <p class="truncate xl/max:w-20">Jenis karyawan</p>
                </th>
                <th class="py-3 px-4 text-xs font-medium text-gray-500 xs/max:px-4 xs/max:py">
                    <p>Departemen</p>
                </th>
                <th class="py-3 px-4 text-xs font-medium text-gray-500 xs/max:px-4 xs/max:py">
                    <p>Posisi</p>
                </th>
                <th class="py-3 px-4 text-xs font-medium text-gray-500 xs/max:px-4 xs/max:py">
                    <div class="flex items-center gap-2 flex items-center justify-center">
                        <p>Status</p>
                    </div>
                </th>
                @canany(['employee.update', 'employee.delete'])
                <th class="py-3 px-6"></th>
                @endcanany
            </tr>
        </thead>
        <tbody class="text-sm font-normal text-gray-700">
            @foreach ($device_emps['data']['data'] as $item)
            <tr class="cursor-pointer bg-white hover:bg-gray-50">
                <td class="px-4 py-3 text-left bg-transparent rounded-l-xl">
                    <p class="text-violet-600 text-sm font-normal truncate xs/max:w-12">{{ $item['emp_code'] }}</p>
                </td>
                <td class="px-4 py-3 text-left bg-transparent">
                    <div class="flex gap-3 items-center">
                        <img src="@zkPhoto({{ $item['photo'] }})" alt=""
                            class="w-10 object-contain h-10 min-w-[40px] min-h-[40px]">
                        <div>
                            <p class="text-gray-900 text-sm font-medium truncate sm/max:w-12">
                                {{ $item['first_name'] }} {{ $item['last_name'] }} {{ (!empty($item['gender'])) ?
                                ('('.$item['gender'].')'): '' }}</p>
                            <p class="text-gray-500 text-sm font-normal truncate sm/max:w-12">
                                {{ $item['birthday'] ?? '-' }}
                            </p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 text-left bg-transparent">
                    <p class="text-gray-500 text-sm font-normal truncate">
                        {{ $item['hire_date'] ?? '-' }}
                    </p>
                </td>
                <td class="px-4 py-3 text-left bg-transparent">
                    <p class="text-gray-500 text-sm font-normal truncate xs/max:w-12">
                        {{ ($item['emp_type'] == 1) ? 'Permanen' : (($item['emp_type'] == 2) ? 'Sementara' : 'Masa
                        percobaan')}}
                    </p>
                </td>
                <td class="px-4 py-3 text-left bg-transparent">
                    <p class="text-gray-500 text-sm font-normal truncate xl/max:w-28">
                        {{ $item['department']['dept_name'] }}
                    </p>
                </td>
                <td class="px-4 py-3 text-left bg-transparent">
                    <p class="text-gray-500 text-sm font-normal truncate xs/max:w-12">
                        {{ (!empty($item['position']))? $item['position']['position_name'] : '-'}}
                    </p>
                </td>
                <td class="px-4 py-3 text-left bg-transparent">
                    <div class="text-gray-500 text-sm font-normal flex items-center justify-center">
                        @if ( $item['app_status'])
                        <x-icon icon="check-circle" width=16 height=16 viewBox="20 20" /> :
                        @else
                        <x-icon icon="x-circle" width=16 height=16 viewBox="20 20" />
                        @endif
                    </div>
                </td>
                @canany(['employee.update', 'employee.delete'])
                <td class="px-4 py-4 bg-transparent rounded-r-xl">
                    <div class="flex justify-end gap-1">
                        @can('employee.delete')
                        <button onclick="open_modal_confirm({{ $item['id'] }})"
                            class="px-2.5 cursor-pointer text-gray-500 rounded hover:bg-gray-50 delete-btn">
                            <x-icon icon="trash-2" width=16 height=16 viewBox="20 20" />
                        </button>
                        @endcan
                        @can('employee.update')
                        <button onclick="get_modal({{ $item['id'] }})"
                            class="px-2.5 cursor-pointer text-gray-500 rounded hover:bg-gray-50">
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
{{--
<x-ui.pagination-custom :pagination="$employees" /> --}}