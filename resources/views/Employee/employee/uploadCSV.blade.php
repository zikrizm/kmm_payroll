<div id="dropzone">
    <form class="dropzone needsclick flex items-start gap-5 justify-center" id="upload_csv"
        enctype="multipart/form-data" method="post">
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
                <div class="px-4 flex flex-col gap-2.5 xs/max:gap-3 mb-8">
                    <header class="dz-message needsclick m-0">
                        <div class="border border-violet-300 bg-violet-25 rounded-lg px-6 py-4 flex flex-col 
                        items-center gap-3 cursor-pointer">
                            <span class="box-content text-violet-600 rounded-full flex justify-center items-center w-10 h-10 
                            bg-violet-100 border border-[6px] border-violet-50">
                                <x-icon icon="upload-cloud" width=18 height=18 viewBox="20 20" />
                            </span>
                            <div class="flex flex-col items-center gap-1">
                                <p class="flex items-center gap-1">
                                    <span class="text-sm font-medium text-violet-700">Click to upload</span>
                                    <span class="text-sm font-normal text-violet-600">or drag and drop</span>
                                </p>
                                <p class="text-xs font-normal text-violet-600">CSV (max. 200MB) </p>
                            </div>
                        </div>
                    </header>
                    <main id="preview-contents" class="flex flex-col gap-2.5"></main>
                </div>
                <hr>
                <footer class="flex justify-end items-center gap-3 p-4 pb-6">
                    <button type="reset"
                        class="modal-close shadow text-gray-500 bg-white hover:bg-gray-100 focus:ring-2 focus:ring-gray-300 rounded-lg xs/max:rounded-md border border-gray-200 text-sm xs/max:text-xs font-medium xs/max:px-4 px-6 xs/max:py-1.5 py-2 hover:text-gray-900 focus:z-10">
                        Cancel</button>
                    <button type="submit" id="submit-all"
                        class="text-white shadow bg-violet-600 hover:bg-violet-700 focus:ring-2 focus:ring-violet-700 font-medium rounded-lg xs/max:rounded-md text-sm xs/max:text-xs inline-flex items-center xs/max:px-4 px-6 xs/max:py-1.5 py-2 text-center">Upload</button>
                </footer>
            </div>
        </section>
    </form>
    <div id="preview-template" style="display: none;">
        <div class="dz-preview dz-file-preview ">
            {{-- border-violet-600 --}}
            <section id="box-preview" class="border-gray-200 border rounded-lg p-4 flex items-start gap-4">
                {{-- text-violet-600 bg-violet-100 --}}
                <div id="icon-mime-preview" class="box-content text-gray-600 bg-gray-100 rounded-full flex justify-center items-center w-8 h-8 
                    border border-4 border-gray-50">
                    <span id="icon-image" class="hidden">
                        <x-icon icon="image" width=16 height=16 viewBox="20 20" />
                    </span>
                    <span id="icon-file" class="hidden">
                        <x-icon icon="file" width=16 height=16 viewBox="20 20" />
                    </span>
                    <span id="icon-mime-loading">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </span>
                </div>
                <div class="flex-1 flex flex-col gap-1">
                    <div class="w-full flex justify-between relative">
                        <div class="flex-1">
                            <p class="text-gray-700 text-sm font-medium dz-filename">
                                <span class="break-words truncate w-[220px] block" data-dz-name=""></span>
                            </p>
                            <p class="text-gray-500 text-xs font-normal dz-size">
                                <span data-dz-size=""></span>
                            </p>
                        </div>
                        <button data-dz-remove
                            class="dz-remove dz-remove-file cursor-pointer text-gray-500 absolute top-[-8px] right-[-8px] hover:bg-gray-50 rounded w-9 h-9 flex items-center justify-center">
                            <x-icon icon="trash-2" width=18 height=18 viewBox="20 20" />
                        </button>
                    </div>
                    <div class="w-full flex items-center gap-3">
                        <div class="flex-1 h-2 rounded bg-gray-100 overflow-hidden">
                            <span data-dz-uploadprogress class="progress-bar flex h-full rounded "
                                style="width: 0%;"></span>
                        </div>
                        <p class="w-8 text-gray-500 text-xs font-medium text-center progress-text">0%</p>
                    </div>
                    <div>
                        <div class="hidden">
                            <div class="rounded-lg overflow-hidden w-full h-40 flex justify-center items-center 
                                dz-image">
                                <img src="" alt="" data-dz-thumbnail="" class="w-full h-full object-contain">
                            </div>
                        </div>
                        <div id="loading-icon">
                            <div class='h-40 text-sm font-medium text-gray-900 flex items-center 
                                justify-center px-3'>
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        strokeWidth="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Loading ...
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>