<div class="flex items-center gap-6 w-full">
    <div class="border-r pr-3 h-full">
        <img src="{{ asset('assets/images/clock.jpg') }}" alt="" width="260" height="260">
    </div>
    <div class="flex flex-col gap-2 flex-1 py-1 justify-center">
        <div class="flex items-center gap-2 text-violet-600">
            <x-icon icon="check-circle" width=30 height=30 viewBox="20 20" />
            <p class="font-medium text-base ">
                SELESAI</p>
        </div>
        <hr>
        <p class="text-gray-500 font-normal text-xs">
            Kakulasi penggajian telah selesai, dan data telah tersimpan.</p>
        <div class="flex items-center gap-2.5">
            <button type="reset"
                class="modal-close w-max shadow text-gray-500 bg-white hover:bg-gray-100 focus:ring-2 focus:ring-gray-300 rounded-lg xs/max:rounded-md border border-gray-200 text-sm xs/max:text-xs font-medium xs/max:px-4 px-6 xs/max:py-1.5 py-1 hover:text-gray-900 focus:z-10">Tutup</button>
            <a href="{{ route('salary-archive.index') }}"
                class="modal-close w-max shadow text-gray-500 bg-white hover:bg-gray-100 focus:ring-2 focus:ring-gray-300 rounded-lg xs/max:rounded-md border border-gray-200 text-sm xs/max:text-xs font-medium xs/max:px-4 px-6 xs/max:py-1.5 py-1 hover:text-gray-900 focus:z-10">Lihat
                arsip</a>
        </div>
    </div>
</div>