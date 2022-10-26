<main class='border border-gray-200 rounded-lg shadow-sm w-max overflow-hidden'>
    <div class="w-full overflow-auto overflow-y-hidden">
        <table class='table border-collapse w-full'>
            <thead class='border-b border-gray-200 bg-gray-50'>
                <tr class=''>
                    <th class='text-left'>
                        <div class='flex items-center'>
                            <div class='pl-4 py-2 flex items-center'>
                                {!! FormCustom::checkbox() !!}
                            </div>
                            <div class='px-6 py-3 cursor-pointer flex-1'>
                                <x-ui.sort-table text="Kode karyawan" url="{{ route('employee.index') }}"
                                    field="emp_code" order="{{ $order }}" />
                            </div>
                        </div>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <x-ui.sort-table text="Nama karyawan" url="{{ route('employee.index') }}" field="first_name"
                            order="{{ $order }}" />
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Bagian</p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Jabatan</p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Tanggal masuk</p>
                    </th>
                    @canany(['employee.update', 'employee.delete'])
                    <th class='px-3 py-3 text-left text-gray-500 text-xs font-medium'></th>
                    @endcanany
                </tr>
            </thead>
            <tbody>
                @forelse (($employees['data'] ?? []) as $item)
                <tr class='hover:bg-gray-50 border-b border-gray-200'>
                    <td class='text-left'>
                        <div class="flex items-center">
                            <div class="pl-4 py ">
                                {!! FormCustom::checkbox() !!}
                            </div>
                            <div class="flex gap-3 items-center px-6 py-3 underline decoration-violet-600 cursor-pointer"
                                onclick="get_modal('{{ $item['emp_code'] }}')">
                                <p class="text-violet-600 text-sm font-medium truncate">
                                    {{ $item['emp_code'] }}
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class='text-left'>
                        <div class="flex items-center">
                            <div class="flex gap-3 items-center px-3 py">
                                @if (!empty($item['photo']))
                                <img src="@zkPhoto({{ $item['photo'] }})" alt=""
                                    class="w-8 object-cover h-8 min-w-[32px] min-h-[32px] rounded-full">
                                @else
                                <img src="@zkPhoto(files/nophoto.gif)" alt=""
                                    class="w-8 object-cover h-8 min-w-[32px] min-h-[32px] rounded-full">
                                @endif
                                <div>
                                    <p class="text-gray-900 text-sm font-medium truncate sm/max:w-12">
                                        {{ $item['first_name'] ?? '' }} {{ $item['last_name'] ?? '' }}
                                    </p>
                                    <p class="text-gray-500 text-sm font-normal truncate sm/max:w-12">
                                        {{ $item['email'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        <p class="truncate">
                            {{ !empty($item['department']) ? $item['department']['dept_name'] ?? '-' : '-' }}
                        </p>
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        <p class="truncate">
                            @if (!empty($item['position']))
                            {{ implode(', ', array_column($item['position'], 'position_name')) }}
                            @else
                            -
                            @endif
                        </p>
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        <p class="truncate">
                            {{ $item['hire_date'] }}
                        </p>
                    </td>
                    <td class='px-3 py'>
                        <div class='flex gap-1'>
                            <button onclick="open_modal_confirm('{{ $item['id'] }}')"
                                class='p-2.5 cursor-pointer text-gray-500 delete-btn'>
                                <x-icon icon="trash-2" width=18 height=18 viewBox="20 20" />
                            </button>
                            <button class='p-2.5 cursor-pointer text-gray-500 edit-btn'
                                onclick="get_modal({{ $item['emp_code'] }})">
                                <x-icon icon="edit" width=18 height=18 viewBox="20 20" />
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>
    <footer class='flex justify-between items-center px-6 pt-3 pb-4'>
        @php
        $page_of = ceil($employees['count'] / (int)$page_size);
        if($employees['next']) {
        $parts = parse_url($employees['next']);
        parse_str($parts['query'], $query);
        $page = (int)$query['page'] -1;
        }else {
        $page = $page_of;
        }
        @endphp
        <div class="flex items-center gap-3">
                <select class="select2-page w-14" name="" id="">
                    <option value="10" @selected($page_size=="10" )>10</option>
                    <option value="20" @selected($page_size=="20" )>20</option>
                    <option value="50" @selected($page_size=="50" )>50</option>
                    <option value="100" @selected($page_size=="100" )>100</option>
                </select>
            <p class='text-gray-700 text-xs'>
                Page <span> {{ $page}} </span> of <span>{{ $page_of }}</span>
            </p>
        </div>
        <div class='flex gap-3'>
            @if (!empty($employees['previous']))
            <button data-pagination-url="{{ $employees['previous'] }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-xs hover:bg-gray-50'>Previous</button>
            @endif
            @if (!empty($employees['next']))
            <button data-pagination-url="{{ $employees['next'] }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-xs hover:bg-gray-50'>Next</button>
            @endif
        </div>
    </footer>
</main>