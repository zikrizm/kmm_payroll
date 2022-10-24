<form autocomplete="off" action="{{ route('employee.uploadCSV-store') }}" method="POST"
    class="submit-employee-csv flex items-start gap-5 justify-center">
    @csrf
    <!-- {{ csrf_field() }} -->
    <section
        class="flex flex-col gap-6 pt-4 bg-white w-[375px] max-h-[95vh] overflow-y-auto overflow-x-hidden relative rounded-lg">
        <header class="px-4 flex flex-col gap-5 pt-4 xs/max:gap-3 relative">
            <button
                class="absolute top-[-5px] right-3 xs/max:top-[-6px] modal-close hover:bg-gray-100 text-red rounded p-2">
                <x-icon icon="x" width=16 height=16 viewBox="20 20" />
            </button>
            <div class="flex flex-col gap-1">
                <div class="flex items-start gap-2">
                    <div
                        class="rounded-full bg-violet-100 p-1.5 border-[4px] border-violet-50 box-border mr-2 text-violet-800">
                        <x-icon icon="upload-cloud" width=18 height=18 viewBox="20 20" />
                    </div>
                    <div>
                        <p class="text-xl font-semibold text-gray-900">Import CSV
                        </p>
                        <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                            Harap memberikan file csv anda.
                        </p>
                    </div>
                </div>
            </div>
            <hr>
        </header>
        <div>
            <main class="px-4 flex flex-col gap-2.5 xs/max:gap-3 mb-8" id="dropzone">
                <label for="contained-button-file" class="flex items-center cursor-pointer bg-gray-50">
                    <input name="file_csv" id="contained-button-file" class="hidden" type="file" />
                    <section
                        class="p-6 gap-3 w-full border-2 border-violet-600 border-dashed rounded-lg flex flex-col items-center justify-center">
                        <span class="text-gray-400">
                            <x-icon icon="file-text" width=45 height=45 viewBox="20 20" />
                        </span>
                        <div class="flex flex-col items-center justify-center text-center">
                            <p class="text-sm text-violet-600 font-semibold">Pilih sebuah file CSV untuk di upload</p>
                            <p class="text-xs text-gray-400">atau seret dan lepas</p>
                        </div>
                    </section>
                </label>
            </main>
            <hr>
            <footer class="flex justify-end items-center gap-3 p-4 pb-6">
                <button type="reset"
                    class="modal-close shadow text-gray-500 bg-white hover:bg-gray-100 focus:ring-2 focus:ring-gray-300 rounded-lg xs/max:rounded-md border border-gray-200 text-sm xs/max:text-xs font-medium xs/max:px-4 px-6 xs/max:py-1.5 py-2 hover:text-gray-900 focus:z-10">
                    Cancel</button>
                <button type="submit"
                    class="text-white shadow bg-violet-600 hover:bg-violet-700 focus:ring-2 focus:ring-violet-700 font-medium rounded-lg xs/max:rounded-md text-sm xs/max:text-xs inline-flex items-center xs/max:px-4 px-6 xs/max:py-1.5 py-2 text-center">Done</button>
            </footer>
        </div>
    </section>
    
</form>