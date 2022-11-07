<form autocomplete="off" action="{{ route('request-task.update', ['request_task' => $request_task->id]) }}"
    method="POST" class="submit-request-task flex items-start gap-5 justify-center">
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
                        <p class="text-xl font-semibold text-gray-900">Penugasan
                        </p>
                        <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                            Harap berikan detail panugasan.
                        </p>
                    </div>
                </div>
            </div>
            <hr>
        </header>
        <div>
            <main class="px-4 flex flex-col gap-2.5 xs/max:gap-3 mb-8">
                <section class="flex flex-col gap-1 flex-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Tanggal
                        penugasan*</label>
                    {!! FormCustom::input('date', date('d-m-Y', strtotime($request_task->start_date)).' - '.
                    date('d-m-Y', strtotime($request_task->end_date)), [
                    'placeholder' => 'Pilih tanggal penugasan',
                    'class' => 'request-task-date',
                    'readonly' => true,
                    'prefixiconname' => 'calendar',
                    ]) !!}
                </section>
                <section class="flex flex-col gap-1">
                    <select class="select2-position" name="position">
                        <option value="" disabled selected>Silahkan Pilih posisi</option>
                        @foreach ($position ?? [] as $item)
                            <option value="{{ $item['position_id'] }}" @selected($request_task->position_id == $item['position_id'])>{{ $item['position_name'] }}</option>
                        @endforeach
                    </select>
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs department hint-text"></label>
                </section>
                <section class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Karyawan*</label>
                    <select data-ajax--url="{{ route('employee.search-employee-request-position') }}"
                        data-ajax--cache="true" class="select2-employee" name="emps[]" multiple="multiple">
                        @foreach ($request_task->request_task_has_emps as $item)
                        <option value="{{ $item->emp_id }}" selected>
                            {{ $item->emp_first_name }} {{ $item->emp_last_name ?? '' }}
                        </option>
                        @endforeach
                    </select>
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs emps hint-text"></label>
                </section>
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