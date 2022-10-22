@forelse ($departments as $key => $item)
@php
$date_name = "group[$key][date]";
$status_name = "group[$key][status]";
$note_name = "group[$key][note]";
$date_hint = "group-$key-date";
$note_hint = "group-$key-note";
$date_value = (!empty($operational) ? (date('Y-m-d',
strtotime($operational->operational_has_depertments[$key]->start_date))
.' - '.date('Y-m-d', strtotime($operational->operational_has_depertments[$key]->end_date))): null);
$node_value = !empty($operational) ? $operational->operational_has_depertments[$key]->note: null;
$status_value = !empty($operational) ? $operational->operational_has_depertments[$key]->status: null;
@endphp
<div class="bg-gray-100 {{ $status_value =='inactive' ? 'hidden' : '' }}">
    <div class="bg-white {{ $status_value =='inactive' ? 'hidden' : '' }}">
        <section class="border p-4 flex flex-col gap-2">
            <input type="hidden" name="group[{{ $key }}][dept_id]" value="{{ $item['id'] }}">
            <input type="hidden" name="group[{{ $key }}][dept_code]" value="{{ $item['dept_code'] }}">
            <input type="hidden" name="group[{{ $key }}][dept_name]" value="{{ $item['dept_name'] }}">
            <div class="flex items-center gap-3">
                {!! FormCustom::checkbox($status_name, -1, ['checked'=> (empty($status_value))? true: $status_value
                =='active'])
                !!}
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

@endforelse