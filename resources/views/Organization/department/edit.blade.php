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
                    <x-icon icon="layers" width=18 height=18 viewBox="20 20" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 xs/max:text-xl xs/max:font-semibold">Department</p>
                    <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                        Please provide the department's detail.
                    </p>
                </div>
            </div>
        </div>
        <hr>
    </header>
    <form autocomplete="off" action="{{ route('department.update', ['department' => $dept['id']]) }}" method="POST"
        class="submit-department">
        @csrf
        <!-- {{ csrf_field() }} -->
        <main class="px-4 flex flex-col gap-4 xs/max:gap-3 mb-8">
            <section class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Department code*</label>
                {!! FormCustom::input('dept_code', $dept['dept_code'], [ "placeholder" => 'Enter new your
                department code']) !!}
            </section>
            <section class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Department name*</label>
                {!! FormCustom::input('dept_name', $dept['dept_name'], [ "placeholder" => 'Enter new your
                department name']) !!}
            </section>
            <section class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Parent department*</label>
                <select class="select2" name="parent_dept">
                    <option value="" selected>Silahkan Pilih</option>
                    @foreach ($departments['data'] as $item)
                    <option value="{{ $item['id'] }}" {{ (!empty($dept['parent_dept']) &&
                        ($dept['parent_dept']['id']==$item['id'])) ? 'selected' : '' }}>{{
                        $item['dept_name'] }}</option>
                    @endforeach
                </select>
                <label class="font-normal text-xs text-red-500 xs/max:text-xs parent_dept text-error"></label>
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