<form autocomplete="off" action="{{ route('timetable.update', ['timetable' => $timetable->id]) }}" method="POST"
    class="submit-timetable">
    @csrf
    <!-- {{ csrf_field() }} -->
    <section
        class="flex flex-col gap-6 pt-4 w-[520px] bg-white max-h-[95vh] overflow-y-auto overflow-x-hidden relative rounded-lg no-scrollbar">
        <header class="px-4 flex flex-col gap-5 pt-4 xs/max:gap-3 relative">
            <button
                class="absolute top-[-5px] right-3 xs/max:top-[-6px] modal-close hover:bg-gray-100 text-red rounded p-2">
                <x-icon icon="x" width=16 height=16 viewBox="20 20" />
            </button>
            <div class="flex flex-col gap-1">
                <div class="flex items-start gap-2">
                    <div
                        class="rounded-full bg-violet-100 p-1.5 border-[4px] border-violet-50 box-border mr-2 text-violet-800">
                        <x-icon icon="clock" width=18 height=18 viewBox="20 20" />
                    </div>
                    <div>
                        <p class="text-xl font-semibold text-gray-900">Jadwal
                        </p>
                        <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                            Harap berikan detail jadwalnya.
                        </p>
                    </div>
                </div>
            </div>
            <hr>
        </header>
        <div>
            <main class="px-4 flex flex-col gap-4 mb-8">
                <section class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Nama jadwal*</label>
                    {!! FormCustom::input('name', $timetable->name, [
                        'placeholder' => 'Masukkan nama jadwal anda yang baru',
                    ]) !!}
                </section>
                <ul class="flex border-b mb-4">
                    <li>
                        <button type="button" data-ref-class-content="basic-settings-content"
                            class="text-gray-500 text-violet-700 border-b-2 mr-4 pt px-1 pb-[19px] border-violet-700 text-sm font-medium">
                            Pengaturan awal
                        </button>
                    </li>
                    <li>
                        <button type="button" data-ref-class-content="break-time-settings-content"
                            class="text-gray-500 mr-4 pt px-1 pb-[19px] border-violet-700 text-sm font-medium">
                            Pembulatan lembur
                        </button>
                    </li>
                    <li>
                        <button type="button" data-ref-class-content="overtime-rule-content"
                            class="text-gray-500 mr-4 pt px-1 pb-[19px] border-violet-700 text-sm font-medium">
                            Upah tambahan
                        </button>
                    </li>
                </ul>
                <div class="flex flex-col gap-2.5" id="basic-settings-content">
                    <div class="flex gap-3 w-full">
                        <div class="flex items-center gap-1 flex-col flex-1">
                            <div class="flex flex-col gap-1 w-full">
                                <label class="text-sm font-normal text-gray-500">Masuk*</label>
                                {!! FormCustom::input('check_in', $timetable->check_in, ['placeholder' => '-', 'type' => 'time']) !!}
                            </div>
                            <div class="flex flex-col gap-1 w-full">
                                <label class="text-sm font-normal text-gray-500">Batas Sebelum*</label>
                                <div class="flex items-center gap-2">
                                    {!! FormCustom::input('check_in_min', $timetable->check_in_min, [
                                        'placeholder' => '-',
                                        'prefixiconname' => 'minus',
                                        'class' => 'plus-minus',
                                    ]) !!}
                                    <p class="text-sm font-normal text-gray-500">Menit</p>
                                </div>
                            </div>
                            <div class="flex flex-col gap-1 w-full">
                                <label class="text-sm font-normal text-gray-500">Batas Setelah*</label>
                                <div class="flex items-center gap-2">
                                    {!! FormCustom::input('check_in_plus', $timetable->check_in_plus, [
                                        'placeholder' => '-',
                                        'prefixiconname' => 'plus',
                                        'class' => 'plus-minus',
                                    ]) !!}
                                    <p class="text-sm font-normal text-gray-500">Menit</p>
                                </div>
                            </div>
                            {{-- <div class="flex flex-col gap-1 w-full">
                                <label class="text-sm font-normal text-gray-500">Batas ± (60 menit)*</label>
                                {!! FormCustom::input('check_in_plusmn', $timetable->check_in_plusmn, [
                                "placeholder" => '-',
                                'prefixiconname' => 'plus-minus','class' => 'plus-minus' ])
                                !!}
                            </div> --}}
                        </div>
                        <div class="w-1 bg-gray-300" style="width: 2px;"></div>
                        <div class="flex items-center gap-1 flex-col flex-1">
                            <div class="flex flex-col gap-1 w-full">
                                <label class="text-sm font-normal text-gray-500">Keluar*</label>
                                {!! FormCustom::input('check_out', $timetable->check_out, ['placeholder' => '-', 'type' => 'time']) !!}
                            </div>
                            <div class="flex flex-col gap-1 w-full">
                                <label class="text-sm font-normal text-gray-500">Batas Sebelum*</label>
                                <div class="flex items-center gap-2">
                                    {!! FormCustom::input('check_out_min', $timetable->check_out_min, [
                                        'placeholder' => '-',
                                        'prefixiconname' => 'minus',
                                        'class' => 'plus-minus',
                                    ]) !!}
                                    <p class="text-sm font-normal text-gray-500">Menit</p>
                                </div>
                            </div>
                            <div class="flex flex-col gap-1 w-full">
                                <label class="text-sm font-normal text-gray-500">Batas Setelah*</label>
                                <div class="flex items-center gap-2">
                                    {!! FormCustom::input('check_out_plus', $timetable->check_out_plus, [
                                        'placeholder' => '-',
                                        'prefixiconname' => 'plus',
                                        'class' => 'plus-minus',
                                    ]) !!}
                                    <p class="text-sm font-normal text-gray-500">Menit</p>
                                </div>
                            </div>
                            {{-- <div class="flex flex-col gap-1 w-full">
                                <label class="text-sm font-normal text-gray-500">Batas ± (60 menit)*</label>
                                {!! FormCustom::input('check_out_plusmn', $timetable->check_out_plusmn, [
                                "placeholder" => '-',
                                'prefixiconname' => 'plus-minus','class' => 'plus-minus' ])
                                !!}
                            </div> --}}
                        </div>
                        <div class="w-1 bg-gray-300" style="width: 2px;"></div>
                        <section class="flex flex-col gap-1 flex-1">
                            <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Beda Hari*</label>
                            <select class="select2" name="cross_day">
                                <option value="0" @selected($timetable->cross_day == 0)>0 hari</option>
                                <option value="1" @selected($timetable->cross_day == 1)>1 hari</option>
                                <option value="2" @selected($timetable->cross_day == 2)>2 hari</option>
                                <option value="3" @selected($timetable->cross_day == 3)>3 hari</option>
                            </select>
                            <label class="font-normal text-xs text-red-500 xs/max:text-xs cross_day hint-text"></label>
                        </section>
                    </div>
                    {{-- <section class="flex flex-col gap-1">
                        <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Jenis Hari*</label>
                        <select class="select2" name="work_type">
                            <option value="0" @selected($timetable->work_type == 0)>Hari kerja</option>
                            <option value="1" @selected($timetable->work_type == 1)>Minggu</option>
                            <option value="2" @selected($timetable->work_type == 2)>Libur nasional</option>
                        </select>
                        <label
                            class="font-normal text-xs text-red-500 xs/max:text-xs parent_position hint-text"></label>
                    </section> --}}
                    <section class="flex flex-col gap-1">
                        <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Jam Istirahat</label>
                        <select class="select2-break-time" name="break_times[]" multiple="multiple">
                            @foreach ($break_times as $item)
                                @forelse ($timetable_has_break_times as $itemHas)
                                    @if ($itemHas->break_time->id == $item->id)
                                        <option value="{{ $itemHas->break_time->id }}"
                                            title="{{ $itemHas->break_time }}" selected>
                                            {{ $itemHas->break_time->name }} ({{ $itemHas->break_time->duration }}
                                            Menit)
                                        </option>
                                    @else
                                        <option value="{{ $item->id }}" title="{{ $item }}">
                                            {{ $item->name }} ({{ $item->duration }} Menit)
                                        </option>
                                    @endif
                                @empty
                                    <option value="{{ $item->id }}" title="{{ $item }}">
                                        {{ $item->name }} ({{ $item->duration }} Menit)
                                    </option>
                                @endforelse
                            @endforeach
                        </select>
                        <label class="font-normal text-xs text-red-500 xs/max:text-xs break_times hint-text"></label>
                    </section>
                    <div id="is-without-break-content"
                        class="{{ empty($timetable_has_break_times) || count($timetable_has_break_times) == 0 ? 'hidden' : '' }}">
                        <section>
                            <div class="flex items-start gap-2.5">
                                <span class="pt-0.5">
                                    {!! FormCustom::checkbox('is_without_break', true, ['checked' => $timetable->is_without_break]) !!}
                                </span>
                                <div class="flex flex-col gap-px">
                                    <p class="font-medium text-sm text-gray-700">Bisa tanpa istirahat</p>
                                    <p class="font-normal text-sm text-gray-500">
                                        Karyawan di perbolehkan untuk tidak istirahat
                                    </p>
                                </div>
                            </div>
                        </section>
                    </div>

                </div>
                <div class="flex flex-col gap-4 hidden" id="break-time-settings-content">
                    <section class="flex flex-col gap-1 flex-1">
                        <div class="flex items-center gap-2">
                            {!! FormCustom::checkbox('is_ot_rounding', -1, [
                                'checked' => $timetable->ot_roundone_hr || $timetable->ot_roundhalf_hr,
                            ]) !!}
                            <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Pembulatan</label>
                        </div>
                    </section>
                    <div id="overtime-rounded-content"
                        class="{{ $timetable->ot_roundone_hr || $timetable->ot_roundhalf_hr ? '' : 'hidden' }}">
                        <div class="flex flex-col gap-4">
                            <div class="flex-1 flex flex-col gap-1">
                                <label class="text-sm font-normal text-gray-500 flex items-center gap-1">Durasi minimal
                                    Pembulatan 1 jam <span class="text-xs"> (menit)</span>*</label>
                                {!! FormCustom::input('ot_roundone_hr', $timetable->ot_roundone_hr, [
                                    'placeholder' => 'Masukkan durasi',
                                    'type' => 'number',
                                ]) !!}
                            </div>
                            <div class="flex-1 flex flex-col gap-1">
                                <label class="text-sm font-normal text-gray-500 flex items-center gap-1">Durasi minimal
                                    Pembulatan 1/2 jam <span class="text-xs"> (menit)</span>*</label>
                                {!! FormCustom::input('ot_roundhalf_hr', $timetable->ot_roundhalf_hr, [
                                    'placeholder' => 'Masukkan durasi',
                                    'type' => 'number',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col gap-4 hidden" id="overtime-rule-content">
                    <section class="flex flex-col gap-1 flex-1">
                        <div class="flex items-center gap-2">
                            {!! FormCustom::checkbox('enable_extra_pay', true, ['checked' => $timetable->enable_extra_pay]) !!}
                            <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Upah Tambahan</label>
                        </div>
                        <label class="font-normal text-xs text-red-500 xs/max:text-xs cross_day hint-text"></label>
                    </section>
                    <div id="extra-pay-content" class="{{ $timetable->enable_extra_pay ? 'block' : 'hidden' }}">
                        <section class="flex flex-col gap-1 flex-2">
                            <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Nominal Upah
                                Tambahan</label>
                            {!! FormCustom::input('extra_pay', $timetable->extra_pay, [
                                'prefixtext' => 'Rp',
                                'placeholder' => 'Masukkan upah tambahan',
                                'class' => 'number',
                            ]) !!}
                        </section>
                    </div>
                    <section class="flex flex-col gap-1 flex-1">
                        <div class="flex items-center gap-2">
                            {!! FormCustom::checkbox('is_ot', true, [
                                'checked' =>
                                    $timetable->time_period ||
                                    $timetable->overtime_pay ||
                                    $timetable->duration_calculate_one_shift ||
                                    $timetable->duration_ot_limit,
                            ]) !!}
                            <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Lembur</label>
                        </div>
                        <label class="font-normal text-xs text-red-500 xs/max:text-xs cross_day hint-text"></label>
                    </section>
                    <div id="overtime-content"
                        class="{{ !empty($timetable->ot_period) || !empty($timetable->ot_pay) || !empty($timetable->duration_count_one_shift) || !empty($timetable->duration_ot_limit) ? '' : 'hidden' }}">
                        <div class="flex flex-col gap-4">
                            <section class="flex items-start gap-3">
                                <div class="flex-1 flex flex-col gap-1">
                                    <label class="text-sm font-normal text-gray-500">Durasi Menit*</label>
                                    {!! FormCustom::input('ot_period', $timetable->ot_period, ['placeholder' => '-', 'type' => 'number']) !!}
                                </div>
                                <div class="flex-2 flex flex-col gap-1">
                                    <label class="text-sm font-normal text-gray-500">Upah lembur*</label>
                                    {!! FormCustom::input('ot_pay', $timetable->ot_pay, [
                                        'class' => 'number',
                                        'placeholder' => '-',
                                        'prefixtext' => 'Rp',
                                    ]) !!}
                                </div>
                            </section>
                            <section class="flex flex-col gap-1">
                                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Durasi jam lembur
                                    dikonversikan jadi 1 shift*</label>
                                {!! FormCustom::input('duration_count_one_shift', $timetable->duration_count_one_shift, [
                                    'placeholder' => 'Masukkan durasi waktu (jam)',
                                    'type' => 'number',
                                ]) !!}
                            </section>
                            <section class="flex flex-col gap-1">
                                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Batas maksimal jam
                                    lembur*</label>
                                {!! FormCustom::input('duration_ot_limit', $timetable->duration_ot_limit, [
                                    'placeholder' => 'Masukkan batas durasi lembur',
                                    'type' => 'number',
                                ]) !!}
                            </section>
                        </div>
                    </div>
                    <section class="flex flex-col gap-1 flex-1">
                        <div class="flex items-center gap-2">
                            {!! FormCustom::checkbox('is_ot_rice', true, [
                                'checked' => !is_null($timetable->duration_rice_shift) && $timetable->duration_rice_shift >= 0,
                            ]) !!}
                            <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Nasi lembur</label>
                        </div>
                        <label class="font-normal text-xs text-red-500 xs/max:text-xs cross_day hint-text"></label>
                    </section>
                    <div id="rice-overtime-content"
                        class="{{ !is_null($timetable->duration_rice_shift) && $timetable->duration_rice_shift >= 0 ? '' : 'hidden' }}">
                        <section class="flex flex-col gap-1">
                            <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Durasi jam nasi
                                lembur*</label>
                            {!! FormCustom::input('duration_rice_shift', $timetable->duration_rice_shift, [
                                'placeholder' => 'Masukkan durasi waktu (jam)',
                                'type' => 'number',
                            ]) !!}
                        </section>
                    </div>
                </div>
            </main>
            <hr>
            <footer class="flex justify-end items-center gap-3 p-4  pb-6">
                <button type="reset"
                    class="modal-close shadow text-gray-500 bg-white hover:bg-gray-100 focus:ring-2 focus:ring-gray-300 rounded-lg xs/max:rounded-md border border-gray-200 text-sm xs/max:text-xs font-medium xs/max:px-4 px-6 xs/max:py-1.5 py-2 hover:text-gray-900 focus:z-10">
                    Cancel</button>
                <button type="submit"
                    class="text-white shadow bg-violet-600 hover:bg-violet-700 focus:ring-2 focus:ring-violet-700 font-medium rounded-lg xs/max:rounded-md text-sm xs/max:text-xs inline-flex items-center xs/max:px-4 px-6 xs/max:py-1.5 py-2 text-center">Done</button>
            </footer>
        </div>
    </section>
</form>
