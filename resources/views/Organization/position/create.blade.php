<form autocomplete="off" action="{{ route('position.store') }}" method="POST" class="submit-position">
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
                        <p class="text-xl font-semibold text-gray-900">Tambah jabatan
                        </p>
                        <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                            Harap berikan detail jabatan.
                        </p>
                    </div>
                </div>
            </div>
            <hr>
        </header>
        <div>
            <main class="px-4 flex flex-col gap-2.5 xs/max:gap-3 mb-8">
                <section class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Kode jabatan*</label>
                    {!! FormCustom::input('position_code', null, ['placeholder' => 'Masukkan kode jabatan']) !!}
                </section>
                <section class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Nama jabatan*</label>
                    {!! FormCustom::input('position_name', null, ['placeholder' => 'Masukkan nama jabatan']) !!}
                </section>
                <button type="button" class="mt-3 text-left" id="add-info">
                    <p class="text-base font-medium text-gray-900">Informasi tambahan</p>
                    <div class="flex items-center justify-between text-sm font-normal text-gray-500">
                        <p> Silakan lengkapi data jababan ini jika di butuhkan </p>
                        <x-icon icon="chevron-down" class="add-info-icon duration-300" width=18 height=18
                            viewBox="20 20" />
                    </div>
                </button>
                <div id="add-info-content" class="hidden">
                    <div class="flex flex-col gap-2.5">
                        <hr class="mb-2">
                        <section>
                            <div class="flex items-start gap-2.5">
                                <span class="pt-0.5">
                                    {!! FormCustom::checkbox('must_attend', -1, ['checked' => true]) !!}
                                </span>
                                <div class="flex flex-col gap-px">
                                    <p class="font-medium text-sm text-gray-700">Wajib hadir</p>
                                    <p class="font-normal text-sm text-gray-500">
                                        Karyawan dibolehkan tidak hadir
                                        {{-- Atur yang boleh hadir / tidak hadir --}}
                                    </p>
                                </div>
                            </div>
                        </section>
                        <section>
                            <div class="flex items-start gap-2.5">
                                <span class="pt-0.5">
                                    {!! FormCustom::checkbox('permanently', -1, ['checked' => true]) !!}
                                </span>
                                <div class="flex flex-col gap-px">
                                    <p class="font-medium text-sm text-gray-700">Tetap</p>
                                    <p class="font-normal text-sm text-gray-500">
                                        Jika tidak tetap maka sesuai permintaan
                                    </p>
                                </div>
                            </div>
                        </section>
                        <div class="flex flex-col gap-2">
                            <section>
                                <div class="flex items-start gap-2.5">
                                    <span class="pt-0.5">
                                        {!! FormCustom::checkbox('extra_pay_check', -1) !!}
                                    </span>
                                    <div class="flex flex-col gap-px">
                                        <p class="font-medium text-sm text-gray-700">Upah tambahan</p>
                                        <p class="font-normal text-sm text-gray-500">
                                            Atur upah tambahan jababan
                                        </p>
                                    </div>
                                </div>
                            </section>
                            <div id="extra-pay-content" class="hidden">
                                <section class="flex flex-col gap-1 pl-[26px]">
                                    {!! FormCustom::input('extra_pay', null, [
                                        'prefixtext' => 'Rp',
                                        'placeholder' => 'Masukkan upah tambahan',
                                        'class' => 'number',
                                    ]) !!}
                                </section>
                            </div>
                        </div>
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
    </main>
</form>
