<section
    class="flex flex-col gap-8 py-4 w-[440px] bg-white border max-h-[95vh] overflow-y-auto overflow-x-hidden relative rounded-lg duration-300">
    <div class="flex items-center gap-5 px-4 relative">
        <button id="x-icon-close"
            class="absolute top-[-5px] right-3 xs/max:top-[-6px] modal-close hover:border-gray-500  border border-transparent text-gray-500 rounded p-0.5">
            <x-icon icon="x" width=16 height=16 viewBox="20 20" />
        </button>
        <div class="flex items-center gap-6 w-full">
            <div class="border-r pr-3 h-full" style="min-width: 156px;">
                <img src="{{ asset('assets/images/clock.jpg') }}" alt="" width="260" height="260">
            </div>
            <div class="flex flex-col gap-2 flex-1 py-1 justify-center">
                <div class="flex items-center gap-2 text-violet-600">
                    <p class="font-medium text-base ">Opps sorry</p>
                </div>
                <hr>
                <p class="text-gray-500 font-normal text-xs">
                    Kalkulasi pada tanggal ini telah di lakukan.</p>
                <div class="flex items-center gap-2.5 mt-2" style="flex-wrap: wrap">
                    <a href="{{ route('salary-archive.index') }}"
                        class="modal-close w-max shadow text-gray-500 bg-white hover:bg-gray-100 focus:ring-2 focus:ring-gray-300 rounded-lg xs/max:rounded-md border border-gray-200 text-sm xs/max:text-xs font-medium xs/max:px-4 px-6 xs/max:py-1.5 py-1 hover:text-gray-900 focus:z-10">Lihat
                        arsip</a>
                    <button type="button" onclick="re_calculation({{ $salary_archive->id }})"
                        class="modal-close w-max shadow text-gray-500 bg-white hover:bg-gray-100 focus:ring-2 focus:ring-gray-300 rounded-lg xs/max:rounded-md border border-gray-200 text-sm xs/max:text-xs font-medium xs/max:px-4 px-6 xs/max:py-1.5 py-1 hover:text-gray-900 focus:z-10">Re-calculataion</button>
                    <button type="reset"
                        class="modal-close shadow text-gray-500 bg-white hover:bg-gray-100 focus:ring-2 focus:ring-gray-300 rounded-lg xs/max:rounded-md border border-gray-200 text-sm xs/max:text-xs font-medium xs/max:px-4 px-6 xs/max:py-1.5 py-1 hover:text-gray-900 focus:z-10">Batal</button>
                </div>
            </div>
        </div>
    </div>
</section>