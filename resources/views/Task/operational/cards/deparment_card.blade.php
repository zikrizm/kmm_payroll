<div class="flex flex-col gap-2 max-h-[350px] overflow-auto no-scrollbar">
    @foreach ($timetable_cards as $key => $item)
        <div class="border rounded p-3 flex flex-col gap-2.5">
            <div class="flex flex-col gap-3" id="timetable-day-content">
                <div class="flex flex-col gap-2">
                    <div class="flex items-start gap-2">
                        <div class="flex flex-col">
                            <p class="text-gray-700 text-sm dayname flex items-center gap-2">
                                {{ Carbon\Carbon::create($item['date'])->locale('id_ID')->dayName }}
                                <span class="text-[10px] text-gray-400">
                                    @if ($item['is_range'])
                                        {{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item['date'], 'UTC')->setTimezone('Asia/Jakarta')->format('F d, Y') }}
                                    @endif
                                </span>
                            </p>
                            <dd class="text-gray-500 text-xs">Pilih Jadwal</dd>
                        </div>
                    </div>
                    <hr>
                    <div class="overflow-auto overflow-y-hidden border border-gray-200 rounded-lg shadow-sm ml-5">
                        <table class='table border-collapse w-full'>
                            <thead class='border-b border-gray-200 bg-gray-50'>
                                <tr class=''>
                                    <th class='text-left'>
                                        <div class='flex items-center'>
                                            <div class='pl-4 py-2 flex items-center'>
                                                {!! FormCustom::checkbox('select_all_timetable') !!}
                                                <p class="px-4 text-xs font-medium text-gray-500 truncate">
                                                    Nama jadwal</p>
                                            </div>
                                        </div>
                                    </th>
                                    <th class="text-xs font-medium text-gray-500 truncate">Jam Lembur</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($item['timetables'] as $key_timetable => $item_timetable)
                                    <tr class='hover:bg-gray-50 border-b border-gray-200 cursor-pointer h-12'>
                                        <td class='text-left'>
                                            <div class="flex items-center">
                                                <input type="hidden" value="{{ $item_timetable['id'] }}"
                                                    name={{ 'shift[' . $key . '][timetables][' . $key_timetable . '][timetable_id]' }}>
                                                <div class="pl-4">
                                                    {!! FormCustom::checkbox('shift[' . $key . '][timetables][' . $key_timetable . '][status]', -1, [
                                                        'checked' => !empty($item_timetable['status']) && $item_timetable['status'] == 'active',
                                                        'class' => 'timetable_status',
                                                    ]) !!}
                                                </div>
                                                <div class="px-4">
                                                    <p class="text-gray-700 text-sm">{{ $item_timetable['name'] }}
                                                    </p>
                                                    <dd class="text-gray-400 text-[10px]">
                                                        {{ date('H:i', strtotime($item_timetable['check_in'])) }} -
                                                        {{ date('H:i', strtotime($item_timetable['check_out'])) }}
                                                    </dd>
                                                </div>
                                            </div>
                                        </td>
                                        <td class='px-3 text-gray-500 text-sm'>
                                            <div class="flex items-center justify-center gap-2">
                                                <div
                                                    class="{{ !empty($item_timetable['status']) && $item_timetable['status'] == 'active' ? '' : 'hidden' }} ot-limit-content">
                                                    <div class="w-14 pt-1">
                                                        {!! FormCustom::input(
                                                            'shift[' . $key . '][timetables][' . $key_timetable . '][ot_limit]',
                                                            $item_timetable['ot_limit'] ?? 0,
                                                            [
                                                                'placeholder' => '-',
                                                                'type' => 'number',
                                                                'class' => '!h-7  text-center',
                                                            ],
                                                        ) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                @php
                                    $existing_ids = collect($item['timetables'])->pluck('id')->toArray();
                                    $extra_timetables = isset($all_timetables)
                                        ? $all_timetables->whereNotIn('id', $existing_ids)
                                        : collect([]);
                                    $start_index = count($item['timetables']);
                                    $show_extra_by_default = count($item['timetables']) === 0 && $extra_timetables->isNotEmpty();
                                @endphp

                                @foreach ($extra_timetables as $extra_timetable)
                                    <tr
                                        class='hover:bg-gray-50 border-b border-gray-200 cursor-pointer h-12 {{ $show_extra_by_default ? '' : 'hidden' }} extra-timetable-{{ $key }}'>
                                        <td class='text-left'>
                                            <div class="flex items-center">
                                                <input type="hidden" value="{{ $extra_timetable->id }}"
                                                    name="shift[{{ $key }}][timetables][{{ $start_index + $loop->index }}][timetable_id]">
                                                <div class="pl-4">
                                                    {!! FormCustom::checkbox('shift[' . $key . '][timetables][' . ($start_index + $loop->index) . '][status]', -1, [
                                                        'checked' => false,
                                                        'class' => 'timetable_status',
                                                    ]) !!}
                                                </div>
                                                <div class="px-4">
                                                    <p class="text-gray-700 text-sm">{{ $extra_timetable->name }}
                                                    </p>
                                                    <dd class="text-gray-400 text-[10px]">
                                                        {{ date('H:i', strtotime($extra_timetable->check_in)) }} -
                                                        {{ date('H:i', strtotime($extra_timetable->check_out)) }}
                                                    </dd>
                                                </div>
                                            </div>
                                        </td>
                                        <td class='px-3 text-gray-500 text-sm'>
                                            <div class="flex items-center justify-center gap-2">
                                                <div class="hidden ot-limit-content">
                                                    <div class="w-14 pt-1">
                                                        {!! FormCustom::input('shift[' . $key . '][timetables][' . ($start_index + $loop->index) . '][ot_limit]', 0, [
                                                            'placeholder' => '-',
                                                            'type' => 'number',
                                                            'class' => '!h-7  text-center',
                                                        ]) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if ($extra_timetables->isNotEmpty() && !$show_extra_by_default)
                            <div class="p-2 text-center border-t border-gray-200">
                                <button type="button"
                                    class="text-xs text-blue-500 hover:text-blue-700 hover:underline font-medium"
                                    onclick="let rows = document.querySelectorAll('.extra-timetable-{{ $key }}'); rows.forEach(el => el.classList.toggle('hidden')); this.innerText = rows[0].classList.contains('hidden') ? 'Lihat Semua Jadwal' : 'Sembunyikan Jadwal Lain';">Lihat
                                    Semua Jadwal</button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- <div class="border rounded p-3 flex flex-col gap-2.5">
    <div class="flex flex-col gap-3" id="timetable-day-content">
        <div class="flex flex-col gap-2">
            <div class="flex items-start gap-2">
                <div class="flex flex-col">
                    <p class="text-gray-700 text-sm dayname">{{
                        Carbon\Carbon::create($timetable_card['date'])->locale('id_ID')->dayName }}</p>
                    <dd class="text-gray-500 text-xs">Pilih Jadwal</dd>
                </div>
            </div>
            <hr>
            <div class="overflow-auto overflow-y-hidden border border-gray-200 rounded-lg shadow-sm ml-5">
                <table class='table border-collapse w-full'>
                    <thead class='border-b border-gray-200 bg-gray-50'>
                        <tr class=''>
                            <th class='text-left'>
                                <div class='flex items-center'>
                                    <div class='pl-4 py-2 flex items-center'>
                                        {!! FormCustom::checkbox('select_all_timetable') !!}
                                        <p class="px-4 text-xs font-medium text-gray-500 truncate">
                                            Nama jadwal</p>
                                    </div>
                                </div>
                            </th>
                            <th class="text-xs font-medium text-gray-500 truncate">Jam Lembur</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($timetable_card['timetables'] as $key_timetable => $item_timetable)
                        <tr class='hover:bg-gray-50 border-b border-gray-200 cursor-pointer h-12'>
                            <td class='text-left'>
                                <div class="flex items-center">
                                    <input type="hidden" value="{{ $item_timetable['id'] }}"
                                        name={{ 'shift[timetables][' . $key_timetable . '][timetable_id]' }}>
                                    <div class="pl-4">
                                        {!! FormCustom::checkbox('shift[timetables][' . $key_timetable . '][status]',
                                        -1, [
                                        'checked' => !empty($item_timetable['status']) && $item_timetable['status'] ==
                                        'active',
                                        'class' => 'timetable_status',
                                        ]) !!}
                                    </div>
                                    <div class="px-4">
                                        <p class="text-gray-700 text-sm">{{ $item_timetable['name'] }}</p>
                                        <dd class="text-gray-400 text-[10px]">
                                            {{ date('H:i', strtotime($item_timetable['check_out'])) }} -
                                            {{ date('H:i', strtotime($item_timetable['check_out'])) }}
                                        </dd>
                                    </div>
                                </div>
                            </td>
                            <td class='px-3 text-gray-500 text-sm'>
                                <div class="flex items-center justify-center gap-2">
                                    <div
                                        class="{{ !empty($item_timetable['status']) && $item_timetable['status'] == 'active'?'':'hidden' }} ot-limit-content">
                                        <div class="w-12 pt-1">
                                            {!! FormCustom::input('shift[timetables][' . $key_timetable . '][ot_limit]',
                                            $item_timetable['ot_limit'] ?? 0,
                                            ['placeholder' => '-', 'class' => '!h-7 number text-center'])
                                            !!}
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div> --}}
