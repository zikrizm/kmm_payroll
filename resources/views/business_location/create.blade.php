<main class="flex flex-col gap-8  pt-4 w-[375px] xs/max:w-[280px]">
    <header class="px-4 flex flex-col gap-5 pt-4 xs/max:gap-3 relative">
        <button class="absolute top-[-5px] right-3 xs/max:top-[-6px] modal-close hover:bg-gray-100 
        text-red rounded p-2">
            <x-icon icon="x" width=16 height=16 viewBox="20 20" />
        </button>
        <div class="flex flex-col gap-1">
            <div class="flex items-start gap-2">
                <div
                    class="rounded-full bg-violet-100 p-1.5 border-[4px] border-violet-50 box-border mr-2 text-violet-800">
                    <x-icon icon="map-pin" width=18 height=18 viewBox="20 20" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 xs/max:text-xl xs/max:font-semibold">New location</p>
                    <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                        Please provide the location's detail.
                    </p>
                </div>
            </div>
        </div>
        <hr>
    </header>
    <form autocomplete="off" action="{{ route('locations.store') }}" method="POST" class="submit-location">
        @csrf
        <!-- {{ csrf_field() }} -->
        <main class="px-4 flex flex-col gap-4 xs/max:gap-3 mb-8">
            <section class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Status*</label>
                {!! FormCustom::togglebutton('status', null, ['Active', 'Inactive'], []) !!}
            </section>
            <section class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">name*</label>
                {!! FormCustom::input('name', null, [ "placeholder" => 'Enter new your name']) !!}
            </section>
            <section class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">website</label>
                {!! FormCustom::input('website', null, [ "placeholder" => 'Enter new your website']) !!}
            </section>
            <section class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">bussines contact number</label>
                {!! FormCustom::input('mobile', null, [ "placeholder" => 'Enter new your bussines contact number']) !!}
            </section>
            <section class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">alternate contact number</label>
                {!! FormCustom::input('alternate_number', null, [ "placeholder" => 'Enter new your alternate contact number']) !!}
            </section>
            <section class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">country*</label>
                {!! FormCustom::input('country', null, [ "placeholder" => 'Enter new your country']) !!}
            </section>
            <section class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">state*</label>
                {!! FormCustom::input('state', null, [ "placeholder" => 'Enter new your state']) !!}
            </section>
            <section class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">city*</label>
                {!! FormCustom::input('city', null, [ "placeholder" => 'Enter new your city']) !!}
            </section>
            <section class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">zip code*</label>
                {!! FormCustom::input('zip_code', null, [ "placeholder" => 'Enter new your zip code']) !!}
            </section>
            <div class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">full address*</label>
                {!! FormCustom::textarea('full_address', null, [ "placeholder" => 'Enter new your address'])
                !!}
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
    </form>
</main>x