<form autocomplete="off" action="{{ route('resign.store') }}" method="POST"
    class="submit-resign flex items-start gap-5 justify-center">
    @csrf
    <!-- {{ csrf_field() }} -->
    <section
        class="flex flex-col gap-8 pt-4 bg-white w-[400px] max-h-[95vh] overflow-y-auto overflow-x-hidden relative rounded-lg">
        <header class="px-4 flex flex-col gap-5 pt-4 xs/max:gap-3 relative">
            <button
                class="absolute top-[-5px] right-3 xs/max:top-[-6px] modal-close hover:bg-gray-100 text-red rounded p-2">
                <x-icon icon="x" width=16 height=16 viewBox="20 20" />
            </button>
            <div class="flex flex-col gap-1">
                <div class="flex items-start gap-2">
                    <div
                        class="rounded-full bg-violet-100 p-1.5 border-[4px] border-violet-50 box-border mr-2 text-violet-800">
                        <x-icon icon="user-x" width=18 height=18 viewBox="20 20" />
                    </div>
                    <div>
                        <p class="text-xl font-semibold text-gray-900">Tambah pengunduran
                        </p>
                        <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                            Harap berikan detail pengunduran karyawan.
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
                    <select class="select2-employee" name="employee" data-ajax--url="{{ route('employee.search-employee-for-dropdown') }}"
                    data-ajax--cache="true">
                        <option value="" default disabled selected> Silahkan pilih </option>
                    </select>
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs employee hint-text"></label>
                </section>
                <section class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Tanggal Pengunduran Diri*</label>
                    {!! FormCustom::input('resign_date', null, [ 'placeholder' => 'Pilih tanggal Pengunduran Diri
                    karyawan',
                    'class' => 'date_input', 'readonly' => true, 'prefixiconname' => 'calendar' ]) !!}
                </section>
                <div class="flex items-start gap-4 w-full">
                    <section class="flex flex-col gap-1 flex-1 w-1/2">
                        <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Jenis pengunduran diri*</label>
                        <select class="select2" name="resign_type">
                            <option value="1" selected>Berhenti</option>
                            <option value="2">Dihentikan</option>
                            <option value="3">Mengundurkan diri</option>
                            <option value="4">Transfer</option>
                            <option value="5">Mempertahankan pekerjaan tanpa bayaran</option>
                        </select>
                        <label class="font-normal text-xs text-red-500 xs/max:text-xs gender hint-text"></label>
                    </section>
                    <section class="flex flex-col gap-1 flex-1 w-1/2">
                        <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Kehadiran*</label>
                        <select class="select2" name="disableatt">
                            <option value="True" selected>Enable</option>
                            <option value="False">Disable</option>
                        </select>
                        <label class="font-normal text-xs text-red-500 xs/max:text-xs gender hint-text"></label>
                    </section>
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