{{-- @forelse ($departments as $key => $item)
@php
$date_name = "group[$key][date]";
$status_name = "group[$key][status]";
$note_name = "group[$key][note]";
$date_hint = "group-$key-date";
$note_hint = "group-$key-note";
$date_value = (!empty($operational) ? (date('d-m-Y',
strtotime($operational->operational_has_depts[$key]->start_date))
.' - '.date('d-m-Y', strtotime($operational->operational_has_depts[$key]->end_date))): null);
$node_value = !empty($operational) ? $operational->operational_has_depts[$key]->note: null;
$status_value = !empty($operational) ? $operational->operational_has_depts[$key]->status: null;
@endphp
<div class="bg-gray-100 {{ $status_value =='inactive' ? 'hidden' : '' }}">
    <div class="bg-white {{ $status_value =='inactive' ? 'hidden' : '' }}">
        <section class="border p-4 flex flex-col gap-2">
            <input type="hidden" name="group[{{ $key }}][dept_id]" value="{{ $item['id'] }}">
            <input type="hidden" name="group[{{ $key }}][dept_code]" value="{{ $item['dept_code'] }}">
            <input type="hidden" name="group[{{ $key }}][dept_name]" value="{{ $item['dept_name'] }}">
            <div class="flex items-center gap-3">
                
                <p class="text-base text-gray-700 font-semibold capitalize">{{ $item["dept_name"] }}</p>
            </div>
            <div class="flex items-start gap-3">
                <section class="flex flex-col gap-1 flex-3">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">
                        Tanggal operasional*</label>
                    {!! FormCustom::input($date_name, $date_value, [
                    "placeholder" => "Pilih tanggal operasional",
                    "class" => "specific_date",
                    "readonly" => true,
                    'hintclass' => $date_hint,
                    "prefixiconname" => "calendar",
                    ]) !!}
                </section>
            </div>
            <div class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Catatan</label>
                {!! FormCustom::textarea($note_name, $node_value, ["placeholder" => "Masukkan catatan", 'hintclass' =>
                $note_hint]) !!}
            </div>
        </section>
    </div>
</div>
@empty

@endforelse --}}

<div class="border rounded p-3 flex flex-col gap-2.5">
    <div class="flex flex-col gap-3" id="timetable-day-content">
        <div class="flex flex-col gap-2">
            <div class="flex items-start gap-2">
                <div class="flex flex-col">
                    <p class="text-gray-700 text-sm dayname">{{ $timetable_card['dayname'] }}</p>
                    <dd class="text-gray-500 text-xs">Lorem ipsum dolor sit, amet consectetur</dd>
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
                            <th class="text-xs font-medium text-gray-500 truncate">Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($timetable_card['timetables'] as $key_timetable => $item_timetable)
                            <tr class='hover:bg-gray-50 border-b border-gray-200 cursor-pointer'>
                                <td class='text-left w-[80%]'>
                                    <div class="flex items-center">
                                        <input type="hidden" value="{{ $item_timetable['id'] }}"
                                            name={{ 'shift[timetables][' . $key_timetable . '][timetable_id]' }}>
                                        <div class="pl-4">
                                            {!! FormCustom::checkbox('shift[timetables][' . $key_timetable . '][status]', -1, [
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
                                    <div class="flex items-center gap-2">
                                        <div class="w-12 pt-1">
                                            {!! FormCustom::input('shift[timetables][' . $key_timetable . '][ot_limit]', 0, [
                                                'placeholder' => '-',
                                                'class' => '!h-7 number text-center',
                                            ]) !!}
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
</div>
{{-- <div class="border rounded p-3 flex flex-col gap-2.5">
    <div class="flex flex-col gap-3" id="timetable-day-content">
        @foreach ($timetable_cards as $key => $item)
            <div data-timetable-day="{{ $key }}" class="flex flex-col gap-2 {{ $key != 0 ? 'hidden' : '' }}">
                <div class="flex items-start gap-2">
                    <span class="pt-1">
                        {!! FormCustom::checkbox('shift[' . $key . '][day_status]', -1) !!}
                    </span>
                    <div class="flex flex-col">
                        <p class="text-gray-700 text-sm dayname">{{ $item['dayname'] }}</p>
                        <dd class="text-gray-500 text-xs">Lorem ipsum dolor sit, amet consectetur</dd>
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
                                            {!! FormCustom::checkbox('select_all_timetable_' . $key) !!}
                                            <p class="px-4 text-xs font-medium text-gray-500 truncate">
                                                Nama jadwal</p>
                                        </div>
                                    </div>
                                </th>
                                <th class="text-xs font-medium text-gray-500 truncate">Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($item['timetables'] as $key_timetable => $item_timetable)
                                <tr class='hover:bg-gray-50 border-b border-gray-200 cursor-pointer'>
                                    <td class='text-left w-[80%]'>
                                        <div class="flex items-center">
                                            <input type="hidden" value="{{ $item_timetable['id'] }}"
                                                name={{ 'shift[' . $key . '][timetables][' . $key_timetable . '][timetable_id]' }}>
                                            <div class="pl-4">
                                                {!! FormCustom::checkbox('shift[' . $key . '][timetables][' . $key_timetable . '][status]', -1, [
                                                    'class' => 'timetable_status_' . $key,
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
                                        <div class="flex items-center gap-2">
                                            <div class="w-12 pt-1">
                                                {!! FormCustom::input('shift[' . $key . '][timetables][' . $key_timetable . '][ot_limit]', 0, [
                                                    'placeholder' => '-',
                                                    'class' => '!h-7 number text-center',
                                                ]) !!}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
    @if (count($timetable_cards) != 1)
        <hr>
        <div class="flex items-center justify-between">
            <div>
                <button type="button" id="prev-day"
                    class="hover:bg-gray-50 rounded-lg p-1.5 flex items-center gap-2 text-gray-700 hidden">
                    <x-icon icon="chevron-left" width=12 height=12 viewBox="20 20" />
                    <p class="text-sm"></p>
                </button>
            </div>
            <button type="button" id="next-day"
                class="hover:bg-gray-50 rounded-lg p-1.5 flex items-center gap-2 text-gray-700">
                <p class="text-sm"></p>
                <x-icon icon="chevron-right" width=12 height=12 viewBox="20 20" />
            </button>
        </div>
    @endif
</div> --}}
