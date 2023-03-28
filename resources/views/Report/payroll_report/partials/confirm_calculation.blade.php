<div class="flex items-center gap-6 w-full">
    <div class="border-r pr-3 h-full">
        <img src="{{ asset('assets/images/clock.jpg') }}" alt="" width="260" height="260">
    </div>
    <div class="flex flex-col gap-2 flex-1 py-1 justify-center">
        <div class="flex items-center gap-2 text-violet-600">
            <p class="font-medium text-base "> Hei tunggu</p>
        </div>
        <hr>
        <p class="text-gray-500 font-normal text-xs">
            Apakah anda yakin, ingin melakukan kalkulasi penggajian ini?,
            karena data akan tersimpan.</p>
        <div class="flex items-center gap-2.5 mt-2">
            <button id="kalkulasi"
                class="truncate text-gray-500 shadow bg-white hover:bg-gray-100 focus:ring-2 focus:ring-gray-300 font-medium rounded-lg xs/max:rounded-md border border-gray-200  text-sm xs/max:text-xs inline-flex items-center xs/max:px-4 px-6 xs/max:py-1.5 py-1 text-center">Ya,
                kalkulasi</button>
            <button type="reset"
                class="modal-close shadow text-gray-500 bg-white hover:bg-gray-100 focus:ring-2 focus:ring-gray-300 rounded-lg xs/max:rounded-md border border-gray-200 text-sm xs/max:text-xs font-medium xs/max:px-4 px-6 xs/max:py-1.5 py-1 hover:text-gray-900 focus:z-10">Batal</button>
        </div>
    </div>
</div>