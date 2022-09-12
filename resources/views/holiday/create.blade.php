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
                    <x-icon icon="sun" width=18 height=18 viewBox="20 20" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 xs/max:text-xl xs/max:font-semibold">New holiday</p>
                    <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                        Please provide the holiday's detail.
                    </p>
                </div>
            </div>
        </div>
        <hr>
    </header>
    <form autocomplete="off" action="{{ route('holidays.store') }}" method="POST" class="submit-holiday">
        @csrf
        <!-- {{ csrf_field() }} -->
        <main class="px-4 flex flex-col gap-4 xs/max:gap-3 mb-8">
            <section class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">name*</label>
                {!! FormCustom::input('name', null, [ "placeholder" => 'Enter new your name']) !!}
            </section>
            <div class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Start date</label>
                {!! FormCustom::input('start_date', null, [
                    'placeholder' => 'Enter new your start date',
                    'readonly' => true,
                    'prefixiconname' => 'calendar',
                ]) !!}
            </div>
            <div class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">End date</label>
                {!! FormCustom::input('end_date', null, [
                    'placeholder' => 'Enter new your end date',
                    'readonly' => true,
                    'prefixiconname' => 'calendar',
                ]) !!}
            </div>
            <section class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Status*</label>
                {!! FormCustom::togglebutton('status', null, ['Active', 'Inactive'], []) !!}
            </section>
            <div class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Notes*</label>
                {!! FormCustom::textarea('notes', null, [ "placeholder" => 'Enter new your notes'])
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
</main>
