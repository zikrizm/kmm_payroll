<div class="fixed z-10 inset-0  min-h-screen duration-300 hidden invisible confirmation-modal" aria-labelledby="modal-title" role="dialog"
    aria-modal="true">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-50 transition-opacity inner-modal" aria-hidden="true">
    </div>
    <div class="flex items-center justify-center px-4 h-full">
        <div
            class="flex bg-white rounded-lg text-left max-h-[95vh] overflow-y-auto overflow-x-hidde transform transition-all">
            <div class="sm:flex sm:items-start relative ">
                <button class="absolute top-3 right-3 modal-close hover:bg-gray-100 text-gray-500 rounded p-2">
                    <x-icon icon="x" width=16 height=16 viewBox="20 20" />
                </button>
                <div class="flex flex-col gap-8 xs/max:gap-6">
                    <form autocomplete="off" class="{{ $class }}" action="void:0" method="DELETE">
                        @csrf
                        <!-- {{ csrf_field() }} -->
                        <main class="p-4 pt-6 flex flex-col xs/max:gap-6 gap-12">
                            <div class="flex flex-col items-center">
                                <div class="rounded-full bg-violet-200 p-5 mb-3 xs/max:p-0 text-violet-700">
                                    <div class="xs/max:scale-50">
                                        <x-icon icon="time" width=80 height=80 viewBox="20 20" />
                                    </div>
                                </div>
                                <p class="text-xl font-semibold xs/max:text-base text-gray-500">{{ $title }}</p>
                                <p
                                    class="mb-2 text-sm xs/max:text-xs font-normal text-gray-500 dark:text-gray-400 text-center">
                                    {{ $subTitle }}</p>
                            </div>
                        </main>
                        <hr>
                        <footer class="flex justify-end items-center gap-3 p-4">
                            <button type="reset"
                                class="modal-close shadow text-gray-500 bg-white hover:bg-gray-100 focus:ring-2 focus:ring-gray-300 rounded-lg xs/max:rounded-md border border-gray-200 text-sm xs/max:text-xs font-medium xs/max:px-4 px-6 xs/max:py-1.5 py-2 hover:text-gray-900 focus:z-10">
                                Cancel</button>
                            <button type="submit"
                                class="text-white shadow bg-violet-600 hover:bg-violet-700 focus:ring-2 focus:ring-violet-700 font-medium rounded-lg xs/max:rounded-md text-sm xs/max:text-xs inline-flex items-center xs/max:px-4 px-6 xs/max:py-1.5 py-2 text-center">Delete</button>
                        </footer>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>