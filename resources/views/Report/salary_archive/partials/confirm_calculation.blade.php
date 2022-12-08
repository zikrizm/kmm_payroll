<div class="flex items-center gap-5">
    <div class="text-gray-500">
        <x-icon icon="bullhorn" width=100 height=100 viewBox="20 20" />
    </div>
    <div class="flex flex-col gap-3 flex-1">
        <div class="flex flex-col gap-1">
            <p class="text-gray-700 font-bold text-lg">Hei tunggu</p>
            <p class="text-gray-500 font-normal text-xs">
                Apakah anda yakin, ingin melakukan kalkulasi penggajian ini?,
                karena data akan tersimpan.</p>
        </div>
        <div class="flex items-center gap-3 flex-1">
            <button id="kalkulasi" type="button"
                class="text-gray-500 shadow bg-white hover:bg-gray-100 focus:ring-2 focus:ring-gray-300 font-medium rounded-lg xs/max:rounded-md border border-gray-200  text-sm xs/max:text-xs inline-flex items-center xs/max:px-4 px-6 xs/max:py-1.5 py-1 text-center">Ya,
                kalkulasi</button>
            <button type="reset"
                class="modal-close shadow text-gray-500 bg-white hover:bg-gray-100 focus:ring-2 focus:ring-gray-300 rounded-lg xs/max:rounded-md border border-gray-200 text-sm xs/max:text-xs font-medium xs/max:px-4 px-6 xs/max:py-1.5 py-1 hover:text-gray-900 focus:z-10">Batal</button>
        </div>
    </div>
</div>