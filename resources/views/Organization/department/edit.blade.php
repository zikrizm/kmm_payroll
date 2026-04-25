<form autocomplete="off" action="{{ route('department.update', ['department' => $dept['id']]) }}" method="POST"
    class="submit-department">
    @csrf
    <!-- {{ csrf_field() }} -->
    <main
        class="flex flex-col gap-8  pt-4 w-[375px] bg-white max-h-[95vh] overflow-y-auto overflow-x-hidden relative rounded-lg">
        <header class="px-4 flex flex-col gap-5 pt-4 xs/max:gap-3 relative">
            <button
                class="absolute top-[-5px] right-3 xs/max:top-[-6px] modal-close hover:bg-gray-100 text-red rounded p-2">
                <x-icon icon="x" width=16 height=16 viewBox="20 20" />
            </button>
            <div class="flex flex-col gap-1">
                <div class="flex items-start gap-2">
                    <div
                        class="rounded-full bg-violet-100 p-1.5 border-[4px] border-violet-50 box-border mr-2 text-violet-800">
                        <x-icon icon="layers" width=18 height=18 viewBox="20 20" />
                    </div>
                    <div>
                        <p class="text-xl font-semibold text-gray-900">Bagian </p>
                        <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                            Harap berikan detail bagian.
                        </p>
                    </div>
                </div>
            </div>
            <hr>
        </header>
        <div>
            <main class="px-4 flex flex-col gap-4 xs/max:gap-3 mb-8">
                <section class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Kode bagian*</label>
                    {!! FormCustom::input('dept_code', $dept['dept_code'], [ "placeholder" => 'Masukkan kode bagian'])
                    !!}
                </section>
                <section class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Nama bagian*</label>
                    {!! FormCustom::input('dept_name', $dept['dept_name'], [ "placeholder" => 'Masukkan nama bagian'])
                    !!}
                </section>
                <section class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Bagian utama*</label>
                    <select class="select2" name="parent_dept">
                        <option value="" selected>Silahkan Pilih</option>
                        @foreach ($departments['data'] as $item)
                        <option value="{{ $item['id'] }}" {{ (!empty($dept['parent_dept']) &&
                            ($dept['parent_dept']['id']==$item['id'])) ? 'selected' : '' }}>{{
                            $item['dept_name'] }}</option>
                        @endforeach
                    </select>
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs parent_dept hint-text"></label>
                </section>
                <section class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Status*</label>
                    <select class="select2" name="status">
                        <option value="active" {{ ($dept['status'] == 'active') ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ ($dept['status'] == 'inactive') ? 'selected' : '' }}>Inactive</option>
                    </select>
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs status hint-text"></label>
                </section>
                <div class="flex items-start gap-2.5">
                    <span class="pt-0.5">
                        {!! FormCustom::checkbox('still_paid', -1,
                        ['checked'=> $dept['still_paid']]) !!}
                    </span>
                    <div class="flex flex-col gap-px">
                        <p class="font-medium text-sm text-gray-700">Uang Minggu atau Libur Nasional</p>
                        <p class="font-normal text-sm text-gray-500">Tetap dapat upah harian meskipun hari minggu atau
                            libur nasional
                        </p>
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <section>
                        <div class="flex items-start gap-2.5">
                            <span class="pt-0.5">
                                {!! FormCustom::checkbox('sitting_money_check', -1,
                                ['checked'=>$dept['sitting_money_check']]) !!}
                            </span>
                            <div class="flex flex-col gap-px">
                                <p class="font-medium text-sm text-gray-700">Uang libur</p>
                                <p class="font-normal text-sm text-gray-500">Atur uang libur atas permintaan
                                    perusahaan
                                </p>
                            </div>
                        </div>
                    </section>
                    <div id="sitting-money-content" class="{{ !$dept['sitting_money_check'] ? 'hidden': '' }}">
                        <section class="flex flex-col gap-1 pl-[26px]">
                            {!! FormCustom::input('sitting_money', $dept['sitting_money'], ['prefixtext' => 'Rp',
                            "placeholder" => 'Masukkan uang libur perusahaan', 'class'=> 'number']) !!}
                        </section>
                    </div>
                </div>
            </main>
            <hr>
            <footer class="flex justify-end items-center gap-3 p-4 pb-6 relative">
                <p class="absolute bottom-0.5 left-2 text-gray-400 italic text-[10px]">
                    {{ $dept['updated_by'] ?? '' }}
                </p>
                <button type="reset"
                    class="modal-close shadow text-gray-500 bg-white hover:bg-gray-100 focus:ring-2 focus:ring-gray-300 rounded-lg xs/max:rounded-md border border-gray-200 text-sm xs/max:text-xs font-medium xs/max:px-4 px-6 xs/max:py-1.5 py-2 hover:text-gray-900 focus:z-10">
                    Cancel</button>
                <button type="submit"
                    class="text-white shadow bg-violet-600 hover:bg-violet-700 focus:ring-2 focus:ring-violet-700 font-medium rounded-lg xs/max:rounded-md text-sm xs/max:text-xs inline-flex items-center xs/max:px-4 px-6 xs/max:py-1.5 py-2 text-center">Done</button>
            </footer>
        </div>
    </main>
</form>