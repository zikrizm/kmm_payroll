<form autocomplete="off" action="{{ route('shift.store') }}" method="POST" class="submit-shift">
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
                        <p class="text-2xl font-bold text-gray-900 xs/max:text-xl xs/max:font-semibold">Tambah shift
                        </p>
                        <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                            Harap berikan detail shiftnya.
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
                    {!! FormCustom::input('name', null, [ "placeholder" => 'Masukkan nama jadwal anda yang baru'])
                    !!}
                </section>
                <section class="flex flex-col gap-1 flex-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Bagian*</label>
                    <select class="select2" name="dept_id">
                        @foreach ($onlyParentDept as $item)
                        <option value="{{ $item['id'] }}">{{ $item['dept_name'] }}</option>
                        @endforeach
                    </select>
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs cross_day hint-text"></label>
                </section>
                <ul class="flex border-b mb-4">
                    <li>
                        <button type="button" data-ref-class-content="basic-settings-content"
                            class="text-gray-500 text-violet-700 border-b-2 mr-4 pt px-1 pb-[19px] border-violet-700 text-sm font-medium">
                            Shift Harian
                        </button>
                    </li>
                </ul>
                <div class="flex flex-col gap-2" id="basic-settings-content">
                    <div class="flex items-center justify-between gap-1">
                        <div class="flex flex-1">
                            <p class="text-base font-semibold text-gray-500">Senin: </p>
                        </div>
                        <section class="flex flex-col gap-1 flex-3">
                            <select class="select2-timetable" name="timetables[senin][]" multiple="multiple">
                                @foreach ($timetables as $item)
                                <option value="{{ $item->id }}" title="{{ $item }}">
                                    {{ $item->name }}
                                </option>
                                @endforeach
                            </select>
                            <label
                                class="font-normal text-xs text-red-500 xs/max:text-xs timetables-senin hint-text"></label>
                        </section>
                    </div>
                    <div class="flex items-center justify-between gap-1">
                        <div class="flex flex-1">
                            <p class="text-base font-semibold text-gray-500">Selasa: </p>
                        </div>
                        <section class="flex flex-col gap-1 flex-3">
                            <select class="select2-timetable" name="timetables[selasa][]" multiple="multiple">
                                @foreach ($timetables as $item)
                                <option value="{{ $item->id }}" title="{{ $item }}">
                                    {{ $item->name }}
                                </option>
                                @endforeach
                            </select>
                            <label
                                class="font-normal text-xs text-red-500 xs/max:text-xs timetables-selasa hint-text"></label>
                        </section>
                    </div>
                    <div class="flex items-center justify-between gap-1">
                        <div class="flex flex-1">
                            <p class="text-base font-semibold text-gray-500">Rabu: </p>
                        </div>
                        <section class="flex flex-col gap-1 flex-3">
                            <select class="select2-timetable" name="timetables[rabu][]" multiple="multiple">
                                @foreach ($timetables as $item)
                                <option value="{{ $item->id }}" title="{{ $item }}">
                                    {{ $item->name }}
                                </option>
                                @endforeach
                            </select>
                            <label
                                class="font-normal text-xs text-red-500 xs/max:text-xs timetables-rabu hint-text"></label>
                        </section>
                    </div>
                    <div class="flex items-center justify-between gap-1">
                        <div class="flex flex-1">
                            <p class="text-base font-semibold text-gray-500">Kamis: </p>
                        </div>
                        <section class="flex flex-col gap-1 flex-3">
                            <select class="select2-timetable" name="timetables[kamis][]" multiple="multiple">
                                @foreach ($timetables as $item)
                                <option value="{{ $item->id }}" title="{{ $item }}">
                                    {{ $item->name }}
                                </option>
                                @endforeach
                            </select>
                            <label
                                class="font-normal text-xs text-red-500 xs/max:text-xs timetables-kamis hint-text"></label>
                        </section>
                    </div>
                    <div class="flex items-center justify-between gap-1">
                        <div class="flex flex-1">
                            <p class="text-base font-semibold text-gray-500">Jumat: </p>
                        </div>
                        <section class="flex flex-col gap-1 flex-3">
                            <select class="select2-timetable" name="timetables[jumat][]" multiple="multiple">
                                @foreach ($timetables as $item)
                                <option value="{{ $item->id }}" title="{{ $item }}">
                                    {{ $item->name }}
                                </option>
                                @endforeach
                            </select>
                            <label
                                class="font-normal text-xs text-red-500 xs/max:text-xs timetables-jumat hint-text"></label>
                        </section>
                    </div>
                    <div class="flex items-center justify-between gap-1">
                        <div class="flex flex-1">
                            <p class="text-base font-semibold text-gray-500">Sabtu: </p>
                        </div>
                        <section class="flex flex-col gap-1 flex-3">
                            <select class="select2-timetable" name="timetables[sabtu][]" multiple="multiple">
                                @foreach ($timetables as $item)
                                <option value="{{ $item->id }}" title="{{ $item }}">
                                    {{ $item->name }}
                                </option>
                                @endforeach
                            </select>
                            <label
                                class="font-normal text-xs text-red-500 xs/max:text-xs timetables-sabtu hint-text"></label>
                        </section>
                    </div>
                    <div class="flex items-center justify-between gap-1">
                        <div class="flex flex-1">
                            <p class="text-base font-semibold text-gray-500">Minggu/Libur: </p>
                        </div>
                        <section class="flex flex-col gap-1 flex-3">
                            <select class="select2-timetable" name="timetables[minggu][]" multiple="multiple">
                                @foreach ($timetables as $item)
                                <option value="{{ $item->id }}" title="{{ $item }}">
                                    {{ $item->name }}
                                </option>
                                @endforeach
                            </select>
                            <label
                                class="font-normal text-xs text-red-500 xs/max:text-xs timetables-minggu hint-text"></label>
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