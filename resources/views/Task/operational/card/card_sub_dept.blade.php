@forelse ($departments as $key => $item)
@php
$date_name = "group[$key][date]";
$note_name = "group[$key][note]";
$date_hint = "group-$key-date";
$note_hint = "group-$key-note";
$date_value = (!empty($operational) ? (date('Y-m-d', strtotime($operational->operational_groups[$key]->start_date))
.' - '.date('Y-m-d', strtotime($operational->operational_groups[$key]->end_date))): null);
$node_value = !empty($operational) ?$operational->operational_groups[$key]->note: null;
@endphp
<section class="border p-4 flex flex-col gap-2">
    <input type="hidden" name="group[{{ $key }}][dept_id]" value="{{ $item['id'] }}">
    <input type="hidden" name="group[{{ $key }}][dept_code]" value="{{ $item['dept_code'] }}">
    <input type="hidden" name="group[{{ $key }}][dept_name]" value="{{ $item['dept_name'] }}">
    <p class="text-base text-gray-700 font-semibold capitalize">{{ $item["dept_name"] }}</p>
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
        <section class="flex flex-col gap-1 w-20">
            <label class="font-normal text-sm text-gray-500 xs/max:text-xs opacity-0">Bagian*</label>
            <select class="select2-status" name="group[{{ $key }}][status]">
                <option value="" disabled>Silahkan Pilih</option>
                <option value="active" selected>Aktif</option>
                <option value="inactive">Tidak aktif</option>
            </select>
            <label class="font-normal text-xs text-red-500 xs/max:text-xs status hint-text"></label>
        </section>
    </div>
    <div class="flex flex-col gap-1">
        <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Catatan</label>
        {!! FormCustom::textarea($note_name, $node_value, ["placeholder" => "Masukkan catatan", 'hintclass' =>
        $note_hint]) !!}
    </div>
</section>
@empty

@endforelse