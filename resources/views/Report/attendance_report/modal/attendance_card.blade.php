<form autocomplete="off" action="{{ route('check-attendance-card') }}" method="POST"
    class="submit-attendance-report flex items-center gap-5 justify-center">
    @csrf
    <!-- {{ csrf_field() }} -->
    <section
        class="flex flex-col gap-8 pt-4 bg-white w-[320px] max-h-[95vh] overflow-y-auto overflow-x-hidden relative rounded-lg">
        <header class="px-4 flex flex-col gap-5 pt-4 xs/max:gap-3 relative">
            <button
                class="absolute top-[-5px] right-3 xs/max:top-[-6px] modal-close hover:bg-gray-100 text-red rounded p-2">
                <x-icon icon="x" width=16 height=16 viewBox="20 20" />
            </button>
            <div class="flex flex-col gap-1">
                <div class="flex items-start gap-2">
                    <div
                        class="rounded-full bg-violet-100 p-1.5 border-[4px] border-violet-50 box-border mr-2 text-violet-800">
                        <x-icon icon="file-text" width=18 height=18 viewBox="20 20" />
                    </div> 
                    <div>
                        <p class="text-xl font-semibold text-gray-900">Kartu absen
                        </p>
                        <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                            Melihat laporan kartu absen-nya.
                        </p>
                    </div>
                </div>
            </div>
            <hr>
        </header>
        <div>
            <main class="px-4 flex flex-col gap-2.5 xs/max:gap-3 mb-8">
                <section class="flex flex-col gap-1 flex-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Karyawan*</label>
                    <select class="select2-employee" name="emp_code">
                        <option value="" default disabled selected> Silahkan pilih </option>
                    </select>
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs emp_code hint-text"></label>
                </section>
                <div class="flex flex-col gap-1 flex-1">
                    {!! FormCustom::input('date', null, [
                    'placeholder' => 'Pilih tanggal absensi',
                    'class' => 'date_input',
                    'readonly' => true,
                    'prefixiconname' => 'calendar',
                    ]) !!}
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs date hint-text"></label>
                </div>
                <button type="submit" class="text-white shadow bg-violet-600 hover:bg-violet-700 focus:ring-2 focus:ring-violet-700 font-medium rounded-lg xs/max:rounded-md text-sm xs/max:text-xs inline-flex items-center xs/max:px-4 
                    px-6 xs/max:py-1.5 py-2 text-center flex justify-center">Check laporan</button>
            </main>
        </div>
    </section>

    <section class="flexflex-col gap-8 pt-4 bg-white w-max max-h-[95vh] 
        overflow-y-auto overflow-x-hidden relative rounded-lg">
        <div>
            <main class="px-4 flex flex-col gap-2.5 xs/max:gap-3 mb-8 flex-1" id="content-attendance-card">
                <div class="flex flex-col gap-2.5 items-end flex-1">
                    <button onclick="get_modal()" disabled
                        class="cursor-not-allowed text-gray-300 flex items-center gap-2.5 px-4 py-2  text-sm font-medium flex items-center border border-gray-200 shadow-sm rounded-lg">
                        <x-icon icon="printer" width=18 height=18 viewBox="20 20" />
                        Cetak kartu
                    </button>
                    <div class="flex justify-center w-full">
                        <div class="flex flex-col gap-2.5 border rounded p-4 w-[375px]">
                            <div class="w-full flex items-center justify-center gap-3">
                                <span class="text-gray-300">
                                    <x-icon icon="accounting" width=80 height=80 viewBox="20 20" />
                                </span>
                                <p class="text-xs text-gray-300">Silahkan cari karyawan untuk melihat laporan kartu
                                    absen-nya dan klik <span class="font-semibold">(tombol check laporan)</span>.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </section>
</form>