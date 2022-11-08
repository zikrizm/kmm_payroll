<div class="flex flex-col gap-6 w-full h-full bg-white rounded-lg w-max p-4">
    <main class="flex flex-col gap-2.5 w-full">
        <section>
            <p class='text-gray-700 text-sm font-medium'>Logo Perusahaan</p>
            <p class='text-gray-500 text-sm font-normal'>Klik logo dibawah ini untuk merubah logo perusahaan</p>
        </section>
        <section class='flex justify-start items-center'>
            <label for="contained-button-file" class="flex items-center justify-center cursor-pointer">
                <input name="business_logo" accept="image/*" id="contained-button-file" class="hidden" type="file"
                    onchange="loadPic('#business_logo', 'photo_preview', '#remove-img')" />
                <div class="relative">
                    <button type="button" id="remove-img"
                        class="hidden absolute right-0 bg-red-500 rounded-full text-white p-0.5"
                        onclick="removePhoto('#business_logo', '#photo_preview','#contained-button-file', this)">
                        <x-icon icon="x" width=12 height=12 viewBox="20 20" />
                    </button>
                    <img src='{{$business->logo}}' class='object-cover h-16 w-16
                    bg-gray-50 rounded-full overflow-hidden' id="photo_preview">
                </div>
            </label>
            <input type="hidden" name="business_logo" id="business_logo">
        </section>
        <section class="flex flex-col gap-1">
            <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Nama perusahaan*</label>
            {!! FormCustom::input('name', $business->name, [ "placeholder" => 'Enter new your business name']) !!}
        </section>
        <section class="flex flex-col gap-1">
            <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Tanggal mulai*</label>
            {!! FormCustom::input('start_date', $business->start_date, [
            'placeholder' => 'Enter new your start date',
            'readonly' => true,
            'prefixiconname' => 'calendar',
            ]) !!}
        </section>
        <section class="flex flex-col gap-1">
            <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Jumlah hari gantungan</label>
            {!! FormCustom::input('pending_day', $business->pending_day, [ "placeholder" => 'Masukkan jumlah hari untuk upah gantungan','class' => 'number']) !!}
        </section>
    </main>

</div>