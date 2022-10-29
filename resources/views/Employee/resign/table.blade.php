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
                                <x-ui.sort-table text="Tanggal" url="{{ route('employee.index') }}" field="resign_date"
                                    order="{{ $order }}" />
                            </div>
                        </div>
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <x-ui.sort-table text="Karyawan" url="{{ route('employee.index') }}" field="first_name"
                            order="{{ $order }}" />
                    </th>
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Bagian</p>
                    </th>
                    {{-- <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Posisi</p>
                    </th> --}}
                    <th class='px-3 py-3 text-left cursor-pointer'>
                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Jenis pengunduran</p>
                    </th>
                    @canany(['resign.update', 'resign.delete'])
                    <th class='px-3 py-3 text-left text-gray-500 text-xs font-medium'></th>
                    @endcanany
                </tr>
            </thead>
            <tbody>
                @forelse (($resigns['data'] ?? []) as $item)
                <tr class='hover:bg-gray-50 border-b border-gray-200'>
                    <td class='text-left'>
                        <div class="flex items-center">
                            <div class="pl-4 py ">
                                {!! FormCustom::checkbox() !!}
                            </div>
                            <div class="flex gap-3 items-center px-6 py-3">
                                <div class="flex items-center gap-2 text-gray-500 text-sm">
                                    <x-icon icon="calendar" width=18 height=18 viewBox="20 20" />
                                    <p class="truncate ">
                                        {{ date('d-m-Y', strtotime($item['resign_date'])); }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        ({{ $item['employee']['emp_code'] }})
                        {{ $item['employee']['first_name'] ?? '-' }} {{ $item['employee']['last_name'] ?? '' }}
                    </td>
                    <td class='px-3 py text-gray-500 text-sm'>
                        <p class="truncate">
                            {{$item['employee']['dept_name'] ?? '-' }}
                        </p>
                    </td>
                    {{-- <td class='px-3 py text-gray-500 text-sm'>
                        <p class="truncate">
                            {{ (!empty($item['position']) ? $item['position']['position_name']??'-' : '-') }}
                        </p>
                    </td> --}}
                    <td class='px-3 py text-gray-500 text-sm'>
                        <p class="truncate">
                            @switch($item['resign_type'])
                            @case(1)
                            Berhenti
                            @break
                            @case(2)
                            Dihentikan
                            @break
                            @case(3)
                            Mengundurkan diri
                            @break
                            @case(4)
                            Transfer
                            @break
                            @case(5)
                            Mempertahankan pekerjaan tanpa bayaran
                            @break
                            @default
                            -
                            @endswitch
                        </p>
                    </td>
                    <td class='px-3 py'>
                        <div class='flex gap-1'>
                            <button onclick="open_modal_confirm('{{ $item['id'] }}')"
                                class='p-2.5 cursor-pointer text-gray-500 delete-btn'>
                                <x-icon icon="trash-2" width=18 height=18 viewBox="20 20" />
                            </button>
                            <button class='p-2.5 cursor-pointer text-gray-500 edit-btn'
                                onclick="get_modal({{ $item['id'] }})">
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
        <p class='text-gray-700 text-xs'>
            Page <span> 1 </span> of <span>{{ ceil(($resigns['count'] ?? 0) / 10) }}</span>
        </p>
        <div class='flex gap-3'>
            @if (!empty($resigns['previous']))
            <button data-pagination-url=""
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-xs hover:bg-gray-50'>Previous</button>
            @elseif (!empty($resigns['next']))
            <button data-pagination-url=""
                class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-xs hover:bg-gray-50'>Next</button>
            @endif
        </div>
    </footer>
</main>