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
                        <p class="text-2xl font-bold text-gray-900 xs/max:text-xl xs/max:font-semibold">Tambah jadwal
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
                    {!! FormCustom::input('name', $timetable->name, [ "placeholder" => 'Masukkan nama jadwal anda yang
                    baru'])
                    !!}
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
                            Pengaturan jam istirahat
                        </button>
                    </li>
                    {{-- <li>
                        <button type="button" data-ref-class-content="overtime-rule-content"
                            class="text-gray-500 mr-4 pt px-1 pb-[19px] border-violet-700 text-sm font-medium">
                            Aturan lembur
                        </button>
                    </li> --}}
                </ul>
                <div class="flex flex-col gap-4" id="basic-settings-content">
                    <div>
                        <p class="text-base font-medium text-gray-900 xs/max:font-semibold">Pengaturan awal</p>
                        <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                            Mohon lengkapi data ini.
                        </p>
                    </div>
                    <hr>
                    <div class="flex items-start gap-3">
                        <div class="flex-1 flex flex-col gap-1 flex-2">
                            <label class="text-sm font-normal text-gray-500">Check in*</label>
                            {!! FormCustom::input('in_time', $timetable->in_time, [ "placeholder" => '-', 'type' =>
                            'time']) !!}
                        </div>
                        <div class="flex-1 flex flex-col gap-1 flex-2">
                            <label class="text-sm font-normal text-gray-500">Check out*</label>
                            {!! FormCustom::input('out_time', $timetable->out_time, [ "placeholder" => '-', 'type' =>
                            'time']) !!}
                        </div>
                        <section class="flex flex-col gap-1 flex-1">
                            <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Cross day*</label>
                            <select class="select2" name="cross_day">
                                <option value="0" {{ $timetable->cross_day == 0 ? 'selected': '' }}>0 hari</option>
                                <option value="1" {{ $timetable->cross_day == 1 ? 'selected': '' }}>1 hari</option>
                                <option value="2" {{ $timetable->cross_day == 2 ? 'selected': '' }}>2 hari</option>
                                <option value="3" {{ $timetable->cross_day == 3 ? 'selected': '' }}>3 hari</option>
                            </select>
                            <label class="font-normal text-xs text-red-500 xs/max:text-xs cross_day hint-text"></label>
                        </section>
                    </div>
                    <section class="flex flex-col gap-1">
                        <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Work type*</label>
                        <select class="select2" name="work_type">
                            <option value="0" {{ $timetable->cross_day == 0 ? 'selected': '' }}>Hari kerja</option>
                            <option value="1" {{ $timetable->cross_day == 1 ? 'selected': '' }}>Minggu</option>
                            <option value="2" {{ $timetable->cross_day == 2 ? 'selected': '' }}>Libur nasional</option>
                        </select>
                        <label
                            class="font-normal text-xs text-red-500 xs/max:text-xs parent_position hint-text"></label>
                    </section>
                    <div class="flex flex-col gap-1">
                        <p class="text-sm font-medium text-gray-900">*Keterangan </p>
                        <div class="flex flex-col pl-3">
                            <p class="text-sm font-normal text-gray-500">
                                - Semua pengaturan lintas hari didasarkan pada check-in.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col gap-4 hidden" id="break-time-settings-content">
                    <div>
                        <p class="text-base font-medium text-gray-900 xs/max:font-semibold">Pengaturan jam istirahat</p>
                        <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                            Mohon lengkapi data ini.
                        </p>
                    </div>
                    <hr>
                    <section class="flex flex-col gap-1">
                        <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Jam istirahat</label>
                        <select class="select2-break-time" name="break_time[]" multiple="multiple">
                            @foreach ($timetable_has_break_times as $item)
                            <option value="{{ $item->break_time->id  }}" selected>
                                {{ $item->break_time->name }}
                            </option>
                            @endforeach
                        </select>
                        <label class="font-normal text-xs text-red-500 xs/max:text-xs break_time hint-text"></label>
                    </section>
                </div>
                {{-- <div class="flex flex-col gap-4 hidden" id="overtime-rule-content">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-base font-medium text-gray-900 xs/max:font-semibold">Aturan lembur</p>
                            <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                                Mohon lengkapi data ini.
                            </p>
                        </div>
                        <div class="">
                            <button type="button" id="add-overtime-timetable" class="flex items-center gap-2.5 px-4 py-2 text-gray-500 text-sm font-medium 
                                    flex items-center border border-gray-200 shadow-sm rounded-lg">
                                <x-icon icon="plus" width=18 height=18 viewBox="20 20" />
                                Tambah lembur
                            </button>
                        </div>
                    </div>
                    <hr>
                    <div id="overtime-contents" class="flex flex-col gap-3"></div>

                </div> --}}
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