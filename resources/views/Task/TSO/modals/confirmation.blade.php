<form autocomplete="off" action="{{ route('approved-tso.store') }}" method="POST" class="submit-approve-tso">
    @csrf
    <!-- {{ csrf_field() }} -->
    <section
        class="flex flex-col gap-8 py-4 w-[380px] bg-white border max-h-[95vh] overflow-y-auto overflow-x-hidden relative rounded-lg">
        <div class="flex items-center gap-5 px-4 relative">
            <button
                class="absolute top-[-5px] right-3 xs/max:top-[-6px] modal-close hover:border-white  border border-transparent text-white rounded p-0.5">
                <x-icon icon="x" width=16 height=16 viewBox="20 20" />
            </button>
            <div class="text-gray-500">
                <x-icon icon="bullhorn" width=100 height=100 viewBox="20 20" />
            </div>
            <div class="flex flex-col gap-3 flex-1">
                <input type="hidden" name="tso_datas[0][emp_id]">
                <input type="hidden" name="tso_datas[0][tso_date]">
                <input type="hidden" name="tso_datas[0][dept_id]">
                <div class="flex flex-col gap-1">
                    <p class="text-gray-700 font-bold text-lg">{{ $title }}</p>
                    <p class="text-gray-500 font-normal text-xs">{{ $sub_title }}</p>
                </div>
                <div class="flex items-center gap-3 flex-1">
                    <button
                        class="text-gray-500 shadow bg-white hover:bg-gray-100 focus:ring-2 focus:ring-gray-300 font-medium rounded-lg xs/max:rounded-md border border-gray-200  text-sm xs/max:text-xs inline-flex items-center xs/max:px-4 px-6 xs/max:py-1.5 py-1 text-center">Ya</button>
                    <button type="reset"
                        class="modal-close shadow text-gray-500 bg-white hover:bg-gray-100 focus:ring-2 focus:ring-gray-300 rounded-lg xs/max:rounded-md border border-gray-200 text-sm xs/max:text-xs font-medium xs/max:px-4 px-6 xs/max:py-1.5 py-1 hover:text-gray-900 focus:z-10">Batal</button>
                </div>
            </div>
        </div>
    </section>
</form>