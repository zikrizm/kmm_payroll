<div class="flex flex-col gap-8 xs/max:gap-6 pt-8 pb-8 xs/max:pt-6 xs/max:pb-6">
    <header class="px-4 flex flex-col gap-5 xs/max:gap-3">
        <div class="flex flex-col gap-1">
            <p class="text-2xl font-bold text-gray-900 xs/max:text-xl xs/max:font-semibold">New business location</p>
            <p class="text-base font-normal text-gray-500 xs/max:text-sm">Lorem ipsum dolor sit, amet consectetur
                adipisicing
                elit.
            </p>
        </div>
        <hr>
    </header>

    <form autocomplete="off" action="{{ route('location.store') }}" method="POST" class="submit-business-location">
        @csrf
        <!-- {{ csrf_field() }} -->
        <main class="px-4 flex flex-col xs/max:gap-6 gap-12 mb-8">
            <section class="flex flex-col gap-4 xs/max:gap-3">
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">name</label>
                    {!! FormCustom::input('name', null, [ "placeholder" => 'Enter new your name']) !!}
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">website</label>
                    {!! FormCustom::input('website', null, [ "placeholder" => 'Enter new your website']) !!}
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">bussines contact number</label>
                    <input
                        class="py-1.5 px-2.5 text-sm xs/max:text-xs rounded-lg xs/max:rounded shadow-sm border border-gray-300 focus:outline-none focus:ring-2 focus:shadow focus:ring-gray-300 focus:border-transparent"
                        type="text" placeholder="Enter new your bussines contact number" name="mobile" />
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs"></label>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Alternate contact number</label>
                    <input
                        class="py-1.5 px-2.5 text-sm xs/max:text-xs rounded-lg xs/max:rounded shadow-sm border border-gray-300 focus:outline-none focus:ring-2 focus:shadow focus:ring-gray-300 focus:border-transparent"
                        type="text" placeholder="Enter new your Alternate contact number" name="alternate_number" />
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs"></label>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">country</label>
                    <input
                        class="py-1.5 px-2.5 text-sm xs/max:text-xs rounded-lg xs/max:rounded shadow-sm border border-gray-300 focus:outline-none focus:ring-2 focus:shadow focus:ring-gray-300 focus:border-transparent"
                        type="text" placeholder="Enter new your state" name="country" />
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs"></label>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">state</label>
                    <input
                        class="py-1.5 px-2.5 text-sm xs/max:text-xs rounded-lg xs/max:rounded shadow-sm border border-gray-300 focus:outline-none focus:ring-2 focus:shadow focus:ring-gray-300 focus:border-transparent"
                        type="text" placeholder="Enter new your state" name="state" />
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs"></label>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">city</label>
                    <input
                        class="py-1.5 px-2.5 text-sm xs/max:text-xs rounded-lg xs/max:rounded shadow-sm border border-gray-300 focus:outline-none focus:ring-2 focus:shadow focus:ring-gray-300 focus:border-transparent"
                        type="text" placeholder="Enter new your city" name="city" />
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs"></label>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">zip code</label>
                    <input
                        class="py-1.5 px-2.5 text-sm xs/max:text-xs rounded-lg xs/max:rounded shadow-sm border border-gray-300 focus:outline-none focus:ring-2 focus:shadow focus:ring-gray-300 focus:border-transparent"
                        type="text" placeholder="Enter new your zip code" name="zip_code" />
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs"></label>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">full address</label>
                    <textarea name="full_address" id="" rows="5" class="border"></textarea>
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs"></label>
                </div>
            </section>
        </main>
        <hr>
        <footer class="flex justify-end items-center gap-3 p-4">
            <button type="reset"
                class="modal-close shadow text-gray-500 bg-white hover:bg-gray-100 focus:ring-2 focus:ring-gray-300 rounded-lg xs/max:rounded-md border border-gray-200 text-sm xs/max:text-xs font-medium xs/max:px-4 px-6 xs/max:py-1.5 py-2 hover:text-gray-900 focus:z-10">
                Cancel</button>
            <button type="submit"
                class="text-white shadow bg-green-600 hover:bg-green-700 focus:ring-2 focus:ring-green-700 font-medium rounded-lg xs/max:rounded-md text-sm xs/max:text-xs inline-flex items-center xs/max:px-4 px-6 xs/max:py-1.5 py-2 text-center">Done</button>
        </footer>
    </form>
</div>