@if ($slug == 'not-allowed')
<form autocomplete="off" action="{{ route('approved-tso.store') }}" method="POST"
    class="submit-approve-tso flex items-start gap-5 justify-center">
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
                        <x-icon icon="check" width=16 height=16 viewBox="20 20" />
                    </div>
                    <div>
                        <p class="text-xl font-semibold text-gray-900">Penyetujui Kehadiran
                        </p>
                        <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                            Harap berikan detail karyawan TSO.
                        </p>
                    </div>
                </div>
            </div>
            <hr>
        </header>
        <div>
            <main class="px-4 flex flex-col gap-2.5 xs/max:gap-3 mb-8">
                <input type="hidden" name="tso_datas[emp_id]">
                <input type="hidden" name="tso_datas[tso_date]">
                <section class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Bagian karyawan*</label>
                    <select class="select2-modal" name="tso_datas[dept_id]">
                        <option value="" disabled selected>Silahkan Pilih</option>
                        @foreach (($department_bios ?? []) as $department)
                        <option value="{{ $department['id'] }}">{{ $department['dept_name'] }}</option>
                        @endforeach
                    </select>
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs dept_id hint-text"></label>
                </section>
                <div class="flex justify-center w-full">
                    <div class="flex flex-col gap-2.5 border rounded p-4 w-[375px]">
                        <div class="w-full flex items-center justify-center gap-3">
                            <span class="text-gray-300">
                                <x-icon icon="bullhorn" width=80 height=80 viewBox="20 20" />
                            </span>
                            <div>
                                <p class="text-sm font-medium text-gray-400">Catatan.</p>
                                <p class="text-xs text-gray-300">Silahkan pilih bagian karyawan
                                    dan pastikan karyawan sudah sesuai, karena data akan tersimpan.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <hr>
            <footer class=" flex justify-end items-center gap-3 p-4 pb-6">
                <button type="reset"
                    class="modal-close shadow text-gray-500 bg-white hover:bg-gray-100 focus:ring-2 focus:ring-gray-300 rounded-lg xs/max:rounded-md border border-gray-200 text-sm xs/max:text-xs font-medium xs/max:px-4 px-6 xs/max:py-1.5 py-2 hover:text-gray-900 focus:z-10">
                    Cancel</button>
                <button type="submit"
                    class="text-white shadow bg-violet-600 hover:bg-violet-700 focus:ring-2 focus:ring-violet-700 font-medium rounded-lg xs/max:rounded-md text-sm xs/max:text-xs inline-flex items-center xs/max:px-4 px-6 xs/max:py-1.5 py-2 text-center">Approved</button>
            </footer>
        </div>
    </section>
</form>
@else
<form autocomplete="off" action="{{ route('approved-tso.store') }}" method="POST" class="submit-approve-tso">
    @csrf
    <!-- {{ csrf_field() }} -->
    <section
        class="flex flex-col gap-8 py-4 w-[400px] bg-white border max-h-[95vh] overflow-y-auto overflow-x-hidden relative rounded-lg">
        <div class="flex items-center gap-5 px-4 relative">
            <button
                class="absolute top-[-5px] right-3 xs/max:top-[-6px] modal-close hover:border-white  border border-transparent text-white rounded p-0.5">
                <x-icon icon="x" width=16 height=16 viewBox="20 20" />
            </button>
            <div class="text-gray-500">
                <x-icon icon="bullhorn" width=100 height=100 viewBox="20 20" />
            </div>
            <div class="flex flex-col gap-3 flex-1">
                <input type="hidden" name="tso_datas[emp_id]">
                <input type="hidden" name="tso_datas[dept_id]">
                <input type="hidden" name="tso_datas[tso_date]">
                <div class="flex flex-col gap-1">
                    <p class="text-gray-700 font-bold text-lg">Konfirmasi</p>
                    <p class="text-gray-500 font-normal text-xs">Apakah Perbedaan jadwal operasional dengan Kehadiran
                        karyawan di Setujui?,
                        karena data akan tersimpan.</p>
                </div>
                <div class="flex items-center gap-3 flex-1">
                    <button
                        class="text-gray-500 shadow bg-white hover:bg-gray-100 focus:ring-2 focus:ring-gray-300 font-medium rounded-lg xs/max:rounded-md border border-gray-200  text-sm xs/max:text-xs inline-flex items-center xs/max:px-4 px-6 xs/max:py-1.5 py-1 text-center">Ya</button>
                    <button type="reset"
                        class="modal-close shadow text-gray-500 bg-white hover:bg-gray-100 focus:ring-2 focus:ring-gray-300 rounded-lg xs/max:rounded-md border border-gray-200 text-sm xs/max:text-xs font-medium xs/max:px-4 px-6 xs/max:py-1.5 py-1 hover:text-gray-900 focus:z-10">Batal</button>
                </div>
            </div>
        </div>
    </section>
</form>
@endif