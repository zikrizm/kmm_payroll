<div class="flex flex-col gap-6 w-full h-full business-info-content bg-white rounded-lg w-max p-4">
    <header class="flex flex-col gap-4 w-full">
        <div class="flex gap-4 w-full">
            <span
                class="h-7 w-7 rounded-full bg-violet-100 flex justify-center items-center text-violet-600 border-[4px] border-violet-50 box-content">
                <x-icon icon="book" width=12 height=12 viewBox="20 20" />
            </span>
            <div class="pr-10">
                <p class="text-xl font-medium">Business infomation</p>
                <p class="text-sm font-normal text-gray-500">Please provide your business infomation.</p>
            </div>
        </div>
        <hr>
    </header>
    <main class="flex flex-col gap-3 w-full">
        <section>
            <p class='text-gray-700 text-sm font-medium'>Your logo</p>
            <p class='text-gray-500 text-sm font-normal'>This will be displayed on your profile.</p>
        </section>
        <section class='flex justify-between items-start'>
            <div class="relative">
                <button type="button" id="remove-img"
                    class="hidden absolute right-0 bg-red-500 rounded-full text-white p-0.5"
                    onclick="removePhoto('#business_logo', '#photo_preview','#contained-button-file', this)">
                    <x-icon icon="x" width=12 height=12 viewBox="20 20" />
                </button>
                <img src='{{ asset('storage/business_logos/'.$business->logo.'')}}' class='object-contain h-16 w-16
                bg-gray-50 rounded-full overflow-hidden' id="photo_preview">
            </div>
            <label for="contained-button-file" class="flex items-center cursor-pointer">
                <input name="business_logo" accept="image/*" id="contained-button-file" class="hidden" type="file"
                    onchange="loadPic('#business_logo', 'photo_preview', '#remove-img')" />
                <span class='cursor-pointer text-sm font-medium text-violet-700 hover:bg-gray-100 rounded p-1'>
                    Upload
                </span>
            </label>
            <input type="hidden" name="business_logo" id="business_logo">
        </section>
        <section class="flex flex-col gap-1">
            <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Business name*</label>
            {!! FormCustom::input('name', $business->name, [ "placeholder" => 'Enter new your business name']) !!}
        </section>
        <section class="flex flex-col gap-1">
            <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Start date</label>
            {!! FormCustom::input('start_date', @formatDate($business->start_date), [
            'placeholder' => 'Enter new your start date',
            'readonly' => true,
            'prefixiconname' => 'calendar',
            ]) !!}
        </section>
    </main>

</div>