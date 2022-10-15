<form autocomplete="off" action="{{ route('employee.store') }}" method="POST"
    class="submit-employee flex items-start gap-5 justify-center">
    @csrf
    <!-- {{ csrf_field() }} -->
    <section
        class="flex flex-col gap-6 pt-4 bg-white w-[550px] max-h-[95vh] overflow-y-auto overflow-x-hidden relative rounded-lg">
        <header class="px-4 flex flex-col gap-5 pt-4 xs/max:gap-3 relative">
            <button
                class="absolute top-[-5px] right-3 xs/max:top-[-6px] modal-close hover:bg-gray-100 text-red rounded p-2">
                <x-icon icon="x" width=16 height=16 viewBox="20 20" />
            </button>
            <div class="flex flex-col gap-1">
                <div class="flex items-start gap-2">
                    <div
                        class="rounded-full bg-violet-100 p-1.5 border-[4px] border-violet-50 box-border mr-2 text-violet-800">
                        <x-icon icon="users" width=18 height=18 viewBox="20 20" />
                    </div>
                    <div>
                        <p class="text-xl font-semibold text-gray-900">Tambah karyawan
                        </p>
                        <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                            Harap memberikan rincian karyawan.
                        </p>
                    </div>
                </div>
            </div>
            <hr>
        </header>
        <div>
            <main class="px-4 flex flex-col gap-2.5 xs/max:gap-3 mb-8">
                <ul class="flex border-b mb-3">
                    <li>
                        <button type="button" data-ref-class-content="basic-info-content"
                            class="text-gray-500 text-violet-700 border-b-2 mr-4 pt px-1 pb-[19px] border-violet-700 text-sm font-medium">
                            Profil karyawan
                        </button>
                    </li>
                    <li>
                        <button type="button" data-ref-class-content="payroll-content"
                            class="text-gray-500 mr-4 pt px-1 pb-[19px] border-violet-700 text-sm font-medium">
                            Posisi dan gaji karyawan
                        </button>
                    </li>
                </ul>
                <div id="basic-info-content" class="flex flex-col gap-2.5 ">
                    <input type="hidden" name="is_error_image" value="0">
                    <div class="flex items-center gap-4 w-full">
                        <div class="flex flex-col gap-2.5 flex-1">
                            <section class="flex flex-col gap-1">
                                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Kode karyawan*</label>
                                {!! FormCustom::input('emp_code', null, ['placeholder' => 'Masukkan Kode karyawan']) !!}
                            </section>
                            <div class="flex items-start gap-4 w-full">
                                <section class="flex flex-col gap-1 flex-1">
                                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Nama depan*</label>
                                    {!! FormCustom::input('first_name', null, ['placeholder' => 'Nama depan']) !!}
                                </section>
                                <section class="flex flex-col gap-1 flex-1">
                                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Nama
                                        belakang</label>
                                    {!! FormCustom::input('last_name', null, ['placeholder' => 'Nama belakang']) !!}
                                </section>
                            </div>
                            <section class="flex flex-col gap-1 flex-1">
                                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Jenis kelamin*</label>
                                <select class="select2" name="gender">
                                    <option value="" disabled selected>Silahkan Pilih</option>
                                    <option value="M">Laki-Laki</option>
                                    <option value="F">Perempuan</option>
                                </select>
                                <label class="font-normal text-xs text-red-500 xs/max:text-xs gender hint-text"></label>
                            </section>
                        </div>
                        <section class="flex flex-col items-center gap-1 pt-3">
                            <div class="flex justify-center items-center px-6 ">
                                <label for="contained-button-file" class="flex items-center cursor-pointer bg-gray-50">
                                    <input name="user_capture" accept="image/*" id="contained-button-file"
                                        class="hidden" type="file"
                                        onchange="loadPic('#photo', 'photo_preview', '#remove-img')" />
                                    <span class='cursor-pointer flex w-32 h-32 border border-dashed p-2'>
                                        <img src='@zkPhoto(files / nophoto . gif)'
                                            class='object-cover h-full w-full overflow-hidden' id="photo_preview">
                                    </span>
                                </label>
                            </div>
                            <p
                                class="font-normal text-center text-xs text-red-500 xs/max:text-xs user_capture hint-text">
                        </section>
                    </div>
                    <button type="button" class="mt-3 text-left" id="add-info">
                        <p class="text-base font-medium text-gray-900">Informasi tambahan</p>
                        <div class="flex items-center justify-between text-sm font-normal text-gray-500">
                            <p> Silakan lengkapi data karyawan ini jika di butuhkan </p>
                            <x-icon icon="chevron-down" class="add-info-icon duration-300" width=18 height=18
                                viewBox="20 20" />
                        </div>
                    </button>
                    <div id="add-info-content" class="hidden">
                        <div class="flex flex-col gap-2.5">
                            <hr class="mb-2">
                            <div class="flex items-start gap-4 w-full">
                                <section class="flex flex-col gap-1 flex-1">
                                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Tanggal
                                        lahir</label>
                                    {!! FormCustom::input('birthday', null, [
                                    'placeholder' => 'Pilih tanggal lahir karyawan',
                                    'class' => 'date_input',
                                    'readonly' => true,
                                    'prefixiconname' => 'calendar',
                                    ]) !!}
                                </section>
                                <section class="flex flex-col gap-1 flex-2">
                                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Nomor
                                        telepon</label>
                                    {!! FormCustom::input('mobile', null, ['placeholder' => 'Masukkan nomor telepon
                                    keryawan', 'class' => 'mobile']) !!}
                                </section>
                            </div>
                            <section class="flex flex-col gap-1">
                                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Alamat karyawan</label>
                                {!! FormCustom::textarea('address', null, ['placeholder' => 'Masukkan alamat karyawan'])
                                !!}
                            </section>
                        </div>
                    </div>
                </div>
                <div id="payroll-content" class="flex flex-col gap-2.5 hidden">
                    <section class="flex flex-col gap-1 flex-1">
                        <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Tanggal
                            masuk</label>
                        {!! FormCustom::input('hire_date', null, [
                        'placeholder' => 'Pilih tanggal masuk karyawan',
                        'class' => 'date_input',
                        'readonly' => true,
                        'prefixiconname' => 'calendar',
                        ]) !!}
                    </section>
                    <div class="flex items-start gap-4">
                        <section class="flex flex-col gap-1 flex-2">
                            <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Bagian karyawan*</label>
                            <select class="select2" name="department">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($departments['data'] as $item)
                                <option value="{{ $item['id'] }}">{{ $item['dept_name'] }}</option>
                                @endforeach
                            </select>
                            <label class="font-normal text-xs text-red-500 xs/max:text-xs department hint-text"></label>
                        </section>
                        <section class="flex flex-col gap-1 flex-1">
                            <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Jenis karyawan*</label>
                            <select class="select2" name="emp_type">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="1">Resmi</option>
                                <option value="2">Sementara</option>
                                <option value="3">Masa percobaan</option>
                            </select>
                            <label class="font-normal text-xs text-red-500 xs/max:text-xs emp_type hint-text"></label>
                        </section>
                    </div>
                    <div class="flex items-start gap-4">
                        <section class="flex flex-col gap-1 flex-2">
                            <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Jabatan karyawan</label>
                            <select class="select2" name="position[]" multiple>
                                @foreach ($positions['data'] as $item)
                                <option value="{{ $item['id'] }}">{{ $item['position_name'] }}</option>
                                @endforeach
                            </select>
                            <label class="font-normal text-xs text-red-500 xs/max:text-xs 
                            position hint-text"></label>
                        </section>
                        <section class="flex flex-col gap-1 flex-1">
                            <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Area karyawan*</label>
                            <select class="select2" name="area[]" multiple="multiple">
                                @foreach ($areas['data'] as $item)
                                <option value="{{ $item['id'] }}">{{ $item['area_name'] }}</option>
                                @endforeach
                            </select>
                            <label class="font-normal text-xs text-red-500 xs/max:text-xs area hint-text"></label>
                        </section>
                    </div>
                    <div class="flex items-start gap-4">
                        <section class="flex flex-col gap-1 flex-2">
                            <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Gaji karyawan
                                (per-shift)*</label>
                            {!! FormCustom::input('daily_salary', null, [
                            'prefixtext' => 'Rp',
                            'placeholder' => 'Masukkan gaji karyawan',
                            'class' => 'number',
                            ]) !!}
                        </section>
                        <section class="flex flex-col gap-1 flex-1">
                            <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Periode pembayaran*</label>
                            <select class="select2" name="payment_period">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="daily">Harian</option>
                                <option value="weekly">Mingguan</option>
                                <option value="mounthly">Bulanan</option>
                            </select>
                            <label
                                class="font-normal text-xs text-red-500 xs/max:text-xs payment_period hint-text"></label>
                        </section>
                    </div>

                </div>
            </main>
            <hr>
            <footer class="flex justify-end items-center gap-3 p-4 pb-6">
                <button type="reset"
                    class="modal-close shadow text-gray-500 bg-white hover:bg-gray-100 focus:ring-2 focus:ring-gray-300 rounded-lg xs/max:rounded-md border border-gray-200 text-sm xs/max:text-xs font-medium xs/max:px-4 px-6 xs/max:py-1.5 py-2 hover:text-gray-900 focus:z-10">
                    Cancel</button>
                <button type="submit"
                    class="text-white shadow bg-violet-600 hover:bg-violet-700 focus:ring-2 focus:ring-violet-700 font-medium rounded-lg xs/max:rounded-md text-sm xs/max:text-xs inline-flex items-center xs/max:px-4 px-6 xs/max:py-1.5 py-2 text-center">Done</button>
            </footer>
        </div>
    </section>
</form>