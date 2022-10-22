<form autocomplete="off" action="{{ route('additional-employee.store') }}" method="POST"
    class="submit-additional-employee flex items-start gap-5 justify-center">
    @csrf
    <!-- {{ csrf_field() }} -->
    <section
        class="flex flex-col gap-8 pt-4 bg-white w-[375px] max-h-[95vh] overflow-y-auto overflow-x-hidden relative rounded-lg no-scrollbar">
        <header class="px-4 flex flex-col gap-5 pt-4 xs/max:gap-3 relative">
            <button
                class="absolute top-[-5px] right-3 xs/max:top-[-6px] modal-close hover:bg-gray-100 text-red rounded p-2">
                <x-icon icon="x" width=16 height=16 viewBox="20 20" />
            </button>
            <div class="flex flex-col gap-1">
                <div class="flex items-start gap-2">
                    <div
                        class="rounded-full bg-violet-100 p-2 border-[4px] border-violet-50 box-border mr-2 text-violet-800">
                        <x-icon icon="users" width=16 height=16 viewBox="20 20" />
                    </div>
                    <div>
                        <p class="text-xl font-semibold text-gray-900">Tambah bantuan
                        </p>
                        <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                            Harap berikan detail bantuan ke bagian lain.
                        </p>
                    </div>
                </div>
            </div>
            <hr>
        </header>
        <div>
            <main class="px-4 flex flex-col gap-2.5 xs/max:gap-3 mb-8">
                <section class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Tanggal operasional*</label>
                    <select class="select2-operational" name="operational_group">
                        <option value="" disabled selected>Silahkan Pilih</option>
                        @php
                        $operational_groups = array_column($operationals->toArray(), 'operational_groups');
                        $indexs = array_keys($operational_groups);
                        @endphp
                        @foreach (array_column($operational_groups, array_shift($indexs)) as $item)
                        @php
                        $indexOp = array_search($item['operational_id'], array_column($operationals->toArray(), 'id'));
                        @endphp
                        <option data-group="{{ json_encode($item) }}" data-operational="{{ $operationals[$indexOp] }}"
                            value="{{ $item['id'] }}" title="{{ $item['dept_name'] }}">
                            {{ date('Y-m-d', strtotime($item['start_date'])) }} -
                            {{ date('Y-m-d', strtotime($item['end_date'])) }}
                        </option>
                        @endforeach
                    </select>
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs operational hint-text"></label>
                </section>
                <div id="additional-employee-content" class="hidden">
                    <div class="flex flex-col gap-2.5">
                        <section class="flex flex-col gap-1 flex-1">
                            <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Tanggal
                                bantuan bagian*</label>
                            {!! FormCustom::input('date', null, [
                            'placeholder' => 'Pilih tanggal bantuan',
                            'class' => 'additional-employee-date',
                            'readonly' => true,
                            'prefixiconname' => 'calendar',
                            ]) !!}
                        </section>
                        <section class="flex flex-col gap-1">
                            <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Karyawan libur*</label>
                            <select data-ajax--url="{{ route('employee.search-employee-off-in-depts') }}"
                                data-ajax--cache="true" class="select2-employee" name="emps[]" multiple="multiple">
                            </select>
                            <label class="font-normal text-xs text-red-500 xs/max:text-xs emps hint-text"></label>
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