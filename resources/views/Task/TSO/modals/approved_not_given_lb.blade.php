<form autocomplete="off" action="{{ route('holiday.store') }}" method="POST" class="submit-holiday">
    @csrf
    <!-- {{ csrf_field() }} -->
    <section
        class="flex flex-col gap-8 py-4 w-[400px] bg-gradient-to-r from-violet-400 to-violet-500 max-h-[95vh] overflow-y-auto overflow-x-hidden relative rounded-lg">
        <div class="flex items-center gap-5 px-4 relative">
            <button
                class="absolute top-[-5px] right-3 xs/max:top-[-6px] modal-close hover:border-white  border border-transparent text-white rounded p-0.5">
                <x-icon icon="x" width=16 height=16 viewBox="20 20" />
            </button>
            <div class="text-white">
                <x-icon icon="bullhorn" width=100 height=100 viewBox="20 20" />
            </div>
            <div class="flex flex-col gap-3 flex-1">
                <div class="flex flex-col gap-1">
                    <p class="text-white font-bold text-lg">Hei tunggu</p>
                    <p class="text-white font-normal text-xs">
                        Apakah anda yakin, ingin melakuakan kalkulasi penggajian ini?,
                        karena data akan tersimpan.</p>
                </div>
                <div class="flex items-center gap-3 flex-1">
                    <button class="text-xs rounded bg-white p-2.5 text-violet-500 font-medium">Ya, lakukan
                        kalkulasi</button>
                    <button type="reset"
                        class="modal-close flex-1 text-xs rounded bg-transparent border border-white p-2.5 text-white font-medium">Kembali</button>
                </div>
            </div>
        </div>
    </section>
</form>
