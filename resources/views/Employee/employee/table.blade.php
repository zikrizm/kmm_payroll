<main class='border border-gray-200 rounded-lg shadow-sm '>
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
                                <x-ui.sort-table text="Employee code" url="{{ route('employee.index') }}"
                                    field="emp_code" order="{{ $order }}" />
                            </div>
                        </div>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <x-ui.sort-table text="Employee name" url="{{ route('employee.index') }}" field="first_name"
                            order="{{ $order }}" />
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Department</p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Position</p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Hired date</p>
                    </th>
                    <th class='px-3 py-3 text-center cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">App status</p>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Area</p>
                    </th>
                    @canany(['employee.update', 'employee.delete'])
                    <th class='px-3 py-3 text-left text-gray-500 text-xs font-medium'></th>
                    @endcanany
                </tr>
            </thead>
            <tbody>
                @foreach ($employees['data'] as $item)
                <tr class='hover:bg-gray-50 border-b border-gray-200'>
                    <td class='text-left'>
                        <div class="flex items-center">
                            <div class="pl-4 py ">
                                {!! FormCustom::checkbox() !!}
                            </div>
                            <div class="flex gap-3 items-center px-6 py-3 hover:underline hover:text-gray-500 cursor-pointer"
                                onclick="get_modal('{{ $item['id'] }}')">
                                <p class="text-gray-500 text-sm">
                                    {{ $item['emp_code'] }}
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class='text-left'>
                        <div class="flex items-center">
                            <div class="flex gap-3 items-center px-3 py">
                                @if (!empty($item['photo']))
                                <img src="@zkPhoto({{ $item['photo']}})" alt=""
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
                            {{ (!empty($item['department']) ? $item['department']['dept_name']??'-' : '-') }}
                        </p>
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        <p class="truncate">
                            {{ (!empty($item['position']) ? $item['position']['position_name']??'-' : '-') }}
                        </p>
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        <p class="truncate">
                            {{ $item['hire_date'] }}
                        </p>
                    </td>
                    <td class='px-3 py text-center text-gray-500 text-sm'>
                        <p class="truncate">
                            {{ $item['app_status'] }}
                        </p>
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        <p class="truncate">
                            @if (!empty($item['area']))
                            {{ implode(", ",array_column($item['area'], 'area_name' )) }}
                            @else -
                            @endif
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
                @endforeach
            </tbody>
        </table>
    </div>
    <footer class='flex justify-between items-center px-6 pt-3 pb-4'>
        @php $page = 1; @endphp
        <p class='text-gray-700 text-xs'>
            Page <span> {{ $page }} </span> of <span>{{ ceil($employees['count'] / 10) }}</span>
        </p>
        <div class='flex gap-3'>
            @if (!empty($employees['previous']))
            <button data-pagination-url="{{ $employees['previous'] }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-xs hover:bg-gray-50'>Previous</button>
            @elseif (!empty($employees['next']))
            <button data-pagination-url="{{ $employees['next'] }}"
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-xs hover:bg-gray-50'>Next</button>
            @endif
        </div>
    </footer>
</main>