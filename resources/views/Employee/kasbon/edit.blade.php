<form autocomplete="off" action="{{ route('kasbon.update', ['kasbon' => $kasbon->id]) }}" method="POST"
    class="submit-kasbon flex items-start gap-5 justify-center">
    @csrf
    <!-- {{ csrf_field() }} -->
    <section
        class="flex flex-col gap-8 pt-4 bg-white w-[375px] max-h-[95vh] overflow-y-auto overflow-x-hidden relative rounded-lg">
        <header class="px-4 flex flex-col gap-5 pt-4 xs/max:gap-3 relative">
            <button
                class="absolute top-[-5px] right-3 xs/max:top-[-6px] modal-close hover:bg-gray-100 text-red rounded p-2">
                <x-icon icon="x" width=16 height=16 viewBox="20 20" />
            </button>
            <div class="flex flex-col gap-1">
                <div class="flex items-start gap-2">
                    <div
                        class="rounded-full bg-violet-100 p-1.5 border-[4px] border-violet-50 box-border mr-2 text-violet-800">
                        <x-icon icon="dollar-sign" width=18 height=18 viewBox="20 20" />
                    </div>
                    <div>
                        <p class="text-xl font-semibold text-gray-900">Kasbon
                        </p>
                        <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                            Harap berikan detail kasbon.
                        </p>
                    </div>
                </div>
            </div>
            <hr>
        </header>
        <div>
            <main class="px-4 flex flex-col gap-2.5 xs/max:gap-3 mb-8">
                <section class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Karyawan*</label>
                    <select class="select2-employee" name="emp_id" data-ajax--url="{{ route('employee.search-employee-for-dropdown') }}"
                        data-ajax--cache="true">
                        <option value="{{ $employee['id'] }}">
                            <div class="flex items-center">
                                <div class="flex gap-3 items-center py">
                                    @if (empty($employee["photo"]))
                                    <img src="@zkPhoto({{ $employee[" photo"] }})" alt=""
                                        class="w-8 object-cover h-8 min-w-[32px] min-h-[32px] rounded-full">
                                    @else
                                    <img src="@zkPhoto(files / nophoto . gif)" alt=""
                                        class="w-8 object-cover h-8 min-w-[32px] min-h-[32px] rounded-full">
                                    @endif
                                    <div>
                                        <p class="text-gray-900 text-sm font-medium truncate sm/max:w-12">
                                            {{ $employee['first_name'] }} {{ $employee['last_name'] ?? '' }}
                                        </p>
                                        <p class="text-gray-500 text-sm font-normal truncate sm/max:w-12">
                                            {{ $employee['email'] }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </option>
                    </select>
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs emp_id hint-text"></label>
                </section>
                <section class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Tanggal*</label>
                    {!! FormCustom::input('date', $kasbon->date, [
                    'placeholder' => 'Pilih tanggal kasbon',
                    'readonly' => true,
                    'prefixiconname' => 'calendar',
                    ]) !!}
                </section>
                <section class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Jumlah kasbon*</label>
                    {!! FormCustom::input('debt', $kasbon->debt, [
                    'prefixtext' => 'Rp',
                    'placeholder' => 'Masukkan kasbon karyawan',
                    'class' => 'number',
                    ]) !!}
                </section>
                <section class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Jumlah cicilan (yang akan dipotong
                        saat gajian)*</label>
                    {!! FormCustom::input('instalment', $kasbon->instalment, [
                    'prefixtext' => 'Rp',
                    'placeholder' => 'Masukkan cicilan',
                    'class' => 'number',
                    ]) !!}
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