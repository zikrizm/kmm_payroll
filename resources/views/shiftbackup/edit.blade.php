<main class="flex flex-col gap-8  pt-4 w-[375px] xs/max:w-[280px]">
    <header class="px-4 flex flex-col gap-5 pt-4 xs/max:gap-3 relative">
        <button class="absolute top-[-5px] right-3 xs/max:top-[-6px] modal-close hover:bg-gray-100 
        text-red rounded p-2">
            <x-icon icon="x" width=16 height=16 viewBox="20 20" />
        </button>
        <div class="flex flex-col gap-1">
            <div class="flex items-start gap-2">
                <div class="rounded-full bg-violet-100 p-1.5 border-[4px] border-violet-50 box-border mr-2 text-violet-800">
                    <x-icon icon="clock" width=18 height=18 viewBox="20 20" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 xs/max:text-xl xs/max:font-semibold">Shift</p>
                    <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                        Please provide the shift's detail.
                    </p>
                </div>
            </div>
        </div>
        <hr>
    </header>
    <form autocomplete="off" action="{{ route('shifts.update', ['shift' => $shift->id]) }}" method="POST" class="submit-shift">
        @csrf
        <!-- {{ csrf_field() }} -->
        <main class="px-4 flex flex-col gap-4 xs/max:gap-3 mb-8">
            <section class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">name*</label>
                {!! FormCustom::input('name', $shift->name, [ "placeholder" => 'Enter new your name']) !!}
            </section>
            <section class="flex flex-col gap-1 mb-4">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Status*</label>
                {!! FormCustom::togglebutton('status', $shift->status, ['Active', 'Inactive'], []) !!}
            </section>
            <hr>
            <section class="flex flex-col gap-5">
                <div>
                    <p class="text-xl font-bold text-gray-900 xs/max:text-xl xs/max:font-semibold">Time</p>
                    <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                        Please provide the amount of time shift.
                    </p>
                </div>
                <div class="flex items-start gap-2 ">
                    <div class="flex-1 flex flex-col gap-1">
                        <label class="text-sm font-normal text-gray-500">Start*</label>
                        {!! FormCustom::input('time_start', $shift->time_start->format('H:i'), [ "placeholder" => '-', 'type' => 'time']) !!}
                    </div>
                    <div class="flex-1 flex flex-col gap-1">
                        <label class="text-sm font-normal text-gray-500">End*</label>
                        {!! FormCustom::input('time_end', $shift->time_end->format('H:i'), [ "placeholder" => '-',  'type' => 'time']) !!}
                    </div>
                </div>
            </section>
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
    </div>
</main>