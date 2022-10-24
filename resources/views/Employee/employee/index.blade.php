@extends('layouts.app')
@section('title', 'User')
@section('css')
    <style></style>
@endsection
@section('content')
    <div class="flex flex-col gap-6 flex-1 h-full overflow-auto bg-white px-8 pt-8 pb-12">
        <header class="flex justify-between items-start">
            <div class="flex flex-col gap-1">
                <p class="text-3xl font-medium text-gray-900">Karyawan</p>
                <p class="text-base font-normal text-gray-500">Disini untuk mengatur status setiap karyawan.</p>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="get_modal()"
                    class="flex items-center gap-2.5 px-4 py-2 text-gray-500 text-sm font-medium 
                flex items-center border border-gray-200 shadow-sm rounded-lg">
                    <x-icon icon="plus" width=18 height=18 viewBox="20 20" />
                    Tambah karyawan
                </button>
                <button onclick="get_modal_CSV()"
                    class="flex items-center gap-2.5 px-4 py-2 text-gray-500 text-sm font-medium 
                flex items-center border border-gray-200 shadow-sm rounded-lg">
                    <x-icon icon="upload-cloud" width=18 height=18 viewBox="20 20" />
                    Import CSV
                </button>
            </div>
        </header>
        <hr>

        {{-- <form action="/employee" enctype="multipart/form-data" method="post">
            @csrf

            <div id="dropzone">
                <div class="dz-message needsclick m-0">
                    <div
                        class="border border-violet-300 bg-violet-25 rounded-lg px-6 py-4 flex flex-col items-center gap-3 cursor-pointer aside-data">
                        <span
                            class="box-content text-violet-600 rounded-full flex justify-center items-center w-10 h-10 bg-violet-100 border border-[6px] border-violet-50">
                            <x-icon icon="upload-cloud" width=18 height=18 viewBox="20 20" />
                        </span>
                        <div class="flex flex-col items-center gap-1">
                            <p class="flex items-center gap-1"><span class="text-sm font-medium text-violet-700">Click
                                    to
                                    upload</span><span class="text-sm font-normal text-violet-600">or drag and
                                    drop</span>
                            </p>
                            <p class="text-xs font-normal text-violet-600">CSV (max. 200MB) </p>
                        </div>
                    </div>
                </div>
                <div id="template-preview">
                    <img src="" alt="" />

                    <div class="dz-preview dz-file-preview well" id="dz-preview-template">
                        <div class="dz-details">
                            <div class="dz-filename"><span data-dz-name></span></div>
                            <div class="dz-size" data-dz-size></div>
                        </div>
                        <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
                        <div class="dz-success-mark"><span></span></div>
                        <div class="dz-error-mark"><span></span></div>
                        <div class="dz-error-message"><span data-dz-errormessage></span></div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-success" id="submit-all">
                Enviar files
            </button>
        </form> --}}

        <h1>DropzoneJS File Upload Demo</h1>
        <section>
            <div id="dropzone">
                <form class="dropzone needsclick" id="demo-upload" action="/employee" enctype="multipart/form-data"
                    method="post">
                    @csrf
                    <div class="dz-message needsclick m-0">
                        <div
                            class="border border-violet-300 bg-violet-25 rounded-lg px-6 py-4 flex flex-col items-center gap-3 cursor-pointer aside-data">
                            <span
                                class="box-content text-violet-600 rounded-full flex justify-center items-center w-10 h-10 bg-violet-100 border border-[6px] border-violet-50">
                                <x-icon icon="upload-cloud" width=18 height=18 viewBox="20 20" />
                            </span>
                            <div class="flex flex-col items-center gap-1">
                                <p class="flex items-center gap-1"><span class="text-sm font-medium text-violet-700">Click
                                        to
                                        upload</span><span class="text-sm font-normal text-violet-600">or drag and
                                        drop</span>
                                </p>
                                <p class="text-xs font-normal text-violet-600">CSV (max. 200MB) </p>
                            </div>
                        </div>
                    </div>
                    <div id="template-preview-content"></div>
                    <button type="submit" class="border" id="submit-all">
                        Enviar files
                    </button>
                </form>
                <div id="preview-template" style="display: none;">
                    <div class="dz-preview dz-file-preview ">
                        <section class="border-violet-600 border rounded-lg p-4 flex items-start gap-4">
                            <div
                                class="box-content text-violet-600 rounded-full flex justify-center items-center w-8 h-8 bg-violet-100 border border-4 border-violet-50">
                                <span id="icon-image" class="hidden">
                                    <x-icon icon="image" width=16 height=16 viewBox="20 20" />
                                </span>
                                <span id="icon-file" class="hidden">
                                    <x-icon icon="file" width=16 height=16 viewBox="20 20" />
                                </span>
                                <span id="icon-mime-loading">
                                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" strokeWidth="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </span>
                            </div>
                            <div class="flex-1 flex flex-col gap-1">
                                <div class="w-full flex justify-between relative pr-10">
                                    <div class="flex-1">
                                        <p class="text-gray-700 text-sm font-medium dz-filename">
                                            <span class="break-words truncate w-[225px] block" data-dz-name=""></span>
                                        </p>
                                        <p class="text-gray-500 text-xs font-normal dz-size">
                                            <span data-dz-size=""></span>
                                        </p>
                                    </div>
                                    <button disabled=""
                                        class="text-gray-300 cursor-not-allowed absolute top-[-8px] right-[-8px] hover:bg-gray-50 rounded w-9 h-9 flex items-center justify-center">
                                        <x-icon icon="trash-2" width=18 height=18 viewBox="20 20" />
                                    </button>
                                </div>
                                <div class="w-full flex items-center gap-3">
                                    <div class="flex-1 h-2 rounded bg-gray-100 overflow-hidden"><span
                                            class="flex h-full rounded bg-violet-600" style="width: 100%;"></span></div>
                                    <p class="w-8 text-gray-500 text-xs font-medium text-center">100%</p>
                                </div>
                                <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress=""></span></div>
                                <div>
                                    <div class="hidden">
                                        <div
                                            class="rounded-lg overflow-hidden w-full h-40 flex justify-center items-center dz-image">
                                            <img src="" alt="" data-dz-thumbnail=""
                                                class="w-full h-full object-contain">
                                        </div>
                                    </div>
                                    <div id="loading-icon">
                                        <div class='h-40 text-sm font-medium text-gray-900 flex items-center justify-center px-3'>
                                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                    stroke="currentColor" strokeWidth="4"></circle>
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
        </section>

        {{-- <section class="border-violet-600 border rounded-lg p-4 flex items-start gap-4"><span
                class="box-content text-violet-600 rounded-full flex justify-center items-center w-8 h-8 bg-violet-100 border border-4 border-violet-50"><svg
                    xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                    <polyline points="13 2 13 9 20 9"></polyline>
                </svg></span>
            <div class="flex-1 flex flex-col gap-1">
                <div class="w-full flex justify-between relative">
                    <div class="flex-1">
                        <p class="text-gray-700 text-sm font-medium truncate w-[225px] searchAble hidden">fisdas tgs 8.pdf</p>
                        <p class="text-gray-700 text-sm font-medium truncate w-[225px] result">fisdas tgs 8.pdf</p>
                        <p class="text-gray-500 text-sm font-normal">0.28 MB</p>
                    </div><button disabled=""
                        class="text-gray-300 cursor-not-allowed absolute top-[-8px] right-[-8px] hover:bg-gray-50 rounded w-9 h-9 flex items-center justify-center"><svg
                            xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                            </path>
                            <line x1="10" y1="11" x2="10" y2="17"></line>
                            <line x1="14" y1="11" x2="14" y2="17"></line>
                        </svg></button>
                </div>
                <div class="w-full flex items-center gap-3">
                    <div class="flex-1 h-2 rounded bg-gray-100 overflow-hidden"><span
                            class="flex h-full rounded bg-violet-600" style="width: 100%;"></span></div>
                    <p class="w-10 text-gray-700 text-sm font-medium text-center">100%</p>
                </div>
            </div>
        </section> --}}
        {{-- <div id="preview-template" style="display: none;">
            <div class="dz-preview dz-file-preview">
                <div class="dz-image"><img data-dz-thumbnail=""></div>
                <div class="dz-details">
                    <div class="dz-size"><span data-dz-size=""></span></div>
                    <div class="dz-filename"><span data-dz-name=""></span></div>
                </div>
                <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress=""></span></div>
                <div class="dz-error-message"><span data-dz-errormessage=""></span></div>
                <div class="dz-success-mark">
                    <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1" xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns">
                        <title>Check</title>
                        <desc>Created with Sketch.</desc>
                        <defs></defs>
                        <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"
                            sketch:type="MSPage">
                            <path
                                d="M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z"
                                id="Oval-2" stroke-opacity="0.198794158" stroke="#747474" fill-opacity="0.816519475"
                                fill="#FFFFFF" sketch:type="MSShapeGroup"></path>
                        </g>
                    </svg>
                </div>
                <div class="dz-error-mark">
                    <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1" xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns">
                        <title>error</title>
                        <desc>Created with Sketch.</desc>
                        <defs></defs>
                        <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"
                            sketch:type="MSPage">
                            <g id="Check-+-Oval-2" sketch:type="MSLayerGroup" stroke="#747474" stroke-opacity="0.198794158"
                                fill="#FFFFFF" fill-opacity="0.816519475">
                                <path
                                    d="M32.6568542,29 L38.3106978,23.3461564 C39.8771021,21.7797521 39.8758057,19.2483887 38.3137085,17.6862915 C36.7547899,16.1273729 34.2176035,16.1255422 32.6538436,17.6893022 L27,23.3431458 L21.3461564,17.6893022 C19.7823965,16.1255422 17.2452101,16.1273729 15.6862915,17.6862915 C14.1241943,19.2483887 14.1228979,21.7797521 15.6893022,23.3461564 L21.3431458,29 L15.6893022,34.6538436 C14.1228979,36.2202479 14.1241943,38.7516113 15.6862915,40.3137085 C17.2452101,41.8726271 19.7823965,41.8744578 21.3461564,40.3106978 L27,34.6568542 L32.6538436,40.3106978 C34.2176035,41.8744578 36.7547899,41.8726271 38.3137085,40.3137085 C39.8758057,38.7516113 39.8771021,36.2202479 38.3106978,34.6538436 L32.6568542,29 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z"
                                    id="Oval-2" sketch:type="MSShapeGroup"></path>
                            </g>
                        </g>
                    </svg>
                </div>
            </div>
        </div> --}}



        {{-- <form action="/employee" enctype="multipart/form-data" method="post">
            @csrf

            <div id="dropzone">
                <div class="bg-black w-32 h-32 flex flex-col gap-2.5 xs/max:gap-3">
                    <label for="contained-button-file" class="flex items-center cursor-pointer bg-gray-50">
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
                </div>
                <div id="template-preview">
                    <img src="" alt="" />

                    <div class="dz-preview dz-file-preview well" id="dz-preview-template">
                        <div class="dz-details">
                            <div class="dz-filename"><span data-dz-name></span></div>
                            <div class="dz-size" data-dz-size></div>
                        </div>
                        <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
                        <div class="dz-success-mark"><span></span></div>
                        <div class="dz-error-mark"><span></span></div>
                        <div class="dz-error-message"><span data-dz-errormessage></span></div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-success" id="submit-all">
                Enviar files
            </button>
        </form> --}}


        {{-- <div class="">
            <form action="/employee" enctype="multipart/form-data" method="post">
                @csrf
                <!-- {{ csrf_field() }} -->
                
                <div class="px-4 flex flex-col gap-2.5 xs/max:gap-3 mb-8 dropzone" id="image-upload">
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
                </div>
                <button type="submit" class="btn btn-success" id="submit-all">
                    Enviar files
                </button>
            </form>
        </div> --}}

        <div id="meta"></div>
        <x-ui.search-data placeholder="Cari karyawan" url="{{ route('employee.index') }}" />
        <div class="table-content"></div>
        {{-- <x-ui.confirm-modal class="submit-delete-employee"></x-ui.confirm-modal> --}}
    </div>

    <script type="module">
        Dropzone.autoDiscover = false;
    </script>

    <script type="application/javascript">
    let dataParams = {};

    window.addEventListener('DOMContentLoaded', (event) => {
        var minSteps = 6, maxSteps = 60, timeBetweenSteps = 100, bytesPerStep = 1000;
        var dropzone = new Dropzone('#demo-upload', {
            url: "/employee", 
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            previewsContainer: "#template-preview-content",
            previewTemplate: document.querySelector('#preview-template').innerHTML,
            autoProcessQueue:false,
            uploadMultiple: true,
            parallelUploads: 30,
            // thumbnailHeight: 500,
            // thumbnailWidth: 1000,
            // maxFilesize: 3,
            // filesizeBase: 1000,
            init: function() {
                var submitButton = document.querySelector("#submit-all");
                // dropzone = this; // closure

                submitButton.addEventListener("click", function(e) {
                    console.log("sdfsd")
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.processQueue();
                });

                // You might want to show the submit button only when 
                // files are dropped here:
                this.on("addedfile", function(file) {
                    // var extension = file.name.substring(file.name.lastIndexOf('.')).replaceAll('.', '');
                    // var isImage = mimeListImage.includes(extension);
                    
                    var image = $(file.previewElement.querySelector("[data-dz-thumbnail]"));
                    var isImage = file.type.includes('image/');
                    $(file.previewElement.querySelector("#icon-mime-loading")).hide()
                    if(isImage) {
                        $(file.previewElement.querySelector("#icon-image")).show();
                        var totalSteps = Math.round(Math.min(maxSteps, Math.max(minSteps, file.size / bytesPerStep)));
                        for (var step = 0; step < totalSteps; step++) {
                            var duration = timeBetweenSteps * (step + 1);
                            if(step == totalSteps - 1)
                            setTimeout(function(file, totalSteps, step) {
                                return function() {
                                    $(file.previewElement.querySelector("#loading-icon")).hide();
                                    $(image).parent().parent().show();
                                    image.attr({ alt: file.name, src: file.dataURL});
                                };
                            } (file, totalSteps, step), duration);
                        }
                    } else {
                        $(file.previewElement.querySelector("#loading-icon")).hide();
                        $(file.previewElement.querySelector("#icon-file")).show()
                    }
                });
            },
            thumbnail: function(file, dataUrl) {
                if (file.previewElement) {
                    // console.log(dataUrl)
                    // console.log(file)
                    // file.previewElement.classList.remove("dz-file-preview");
                    // var images = file.previewElement.querySelectorAll("[data-dz-thumbnail]");
                    // for (var i = 0; i < images.length; i++) {
                    //     var thumbnailElement = images[i];
                    //     thumbnailElement.alt = file.name;
                    //     thumbnailElement.src = dataUrl;
                    // }
                    // setTimeout(function() { file.previewElement.classList.add("dz-image-preview"); }, 1);
                }
            }
        });
        
        // dropzone.uploadFiles = function(files) {
        //     var self = this;
        //     for (var i = 0; i < files.length; i++) {

        //         var file = files[i];
        //         totalSteps = Math.round(Math.min(maxSteps, Math.max(minSteps, file.size / bytesPerStep)));
        //         for (var step = 0; step < totalSteps; step++) {
        //             var duration = timeBetweenSteps * (step + 1);
        //             setTimeout(function(file, totalSteps, step) {
        //                 return function() {
        //                     file.upload = {
        //                         progress: 100 * (step + 1) / totalSteps,
        //                         total: file.size,
        //                         bytesSent: (step + 1) * file.size / totalSteps
        //                     };

        //                     self.emit('uploadprogress', file, file.upload.progress, file.upload.bytesSent);
        //                     if (file.upload.progress == 100) {
        //                         file.status = Dropzone.SUCCESS;
        //                         self.emit("success", file, 'success', null);
        //                         self.emit("complete", file);
        //                         self.processQueue();
        //                         //document.getElementsByClassName("dz-success-mark").style.opacity = "1";
        //                     }
        //                 };
        //             }(file, totalSteps, step), duration);
        //         }
        //     }
        // }

//         var drop = $('#dz-preview-template').html();
//         var imageUpload = new Dropzone("div#dropzone", { 
//             headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
//             url: "/employee", 
//             autoProcessQueue:false,
//             uploadMultiple: true,
//             maxFilesize:5,
//             maxFiles:3,
//             previewTemplate: drop,
//    previewsContainer: "#template-preview",
//             dictDefaultMessage: 'Drop image here (or click) to capture/upload',

//             acceptedFiles: ".jpeg,.jpg,.png,.gif",

//             init: function() {
//                 var submitButton = document.querySelector("#submit-all");
//                     //imageUpload = this; // closure

//                 submitButton.addEventListener("click", function(e) {
//                     e.preventDefault();
//                     e.stopPropagation();
//                     imageUpload.processQueue(); // Tell Dropzone to process all queued files.
//                 });

//                 // You might want to show the submit button only when 
//                 // files are dropped here:
//                 this.on("addedfile", function(file) {
//                     console.log(file)
//                 // Show submit button here and/or inform user to click it.
//                 });
//             }
//         });
            // let myDropzone = new Dropzone(".dropzone",{
            //                         url: "/employee", 
            //     maxFilesize: 30,        //in MB
            //     acceptedFiles: ".jpeg,.jpg,.png,.pdf",    //accepted file types
            //     method: "post" ,          //sets the form method to PUT,
            //     headers: {
            //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //     },
            // });
        // $(document).ready(function () {
        // $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        //     $(".dropzone").dropzone({
        //         maxFiles: 2000,
        //         url: "/employee/",
        //         processData: false,
        //         contentType: false,
        //         cache: false,
        //         success: function (file, response) {
        //             console.log(response);
        //         }
        //     });
        // })
        // onInit($('.search-data-input').val());
        // $(".search-data-input").on('keyup', debounce(function(e) {
        //     if(e.key == 'Shift') return 0;
        //     delete dataParams.page;
        //     onInit( { q: this.value });
        // }, 250));
    });

    async function onInit(data) {
        // **
        // * Build data params table ----->
        // *
        dataParams = { ...dataParams, ...data };

        // **
        // * get table ----->
        // *
        var res = await ApiService.get_table('/employee', dataParams);
        $('.table-content').html(res);

        
        // **
        // * pagination table ----->
        // *
        $('.pagination-button').on('click', function() {
            var url = new URL($(this).data('pagination-url'));
            var page = url.searchParams.get("page");
            onInit({page})
        })
    }

    async function get_modal_CSV() {
        var res = await ApiService.get_modal('/employee-csv', null);

        var resSubmit = await ApiService.submit_form('.submit-employee-csv', (data) => {

        });
    }

    async function get_modal(employee_code) {
        // **
        // * open modal form ----->
        // *
        var URL = (employee_code) ? '/employee/' + employee_code + '/edit' : '/employee/create';
        var res = await ApiService.get_modal(URL, null);
        $('.select2').select2();
        var anElementNumber = new AutoNumeric.multiple('.number',{decimalPlaces:0,minimumValue: 0,decimalCharacter: ',', digitGroupSeparator : '.'});
        // var anElementMobile = new AutoNumeric.multiple('.mobile',{decimalPlaces:0,minimumValue: 0, decimalCharacter: ',', digitGroupSeparator : ''});
        $('.date_input').daterangepicker({
            autoUpdateInput: false,
            locale: { format: 'YYYY-MM-DD', cancelLabel: 'Clear' },
            singleDatePicker: true,
            showDropdowns: true,
            minYear: 1945,
            drops: "auto",
            maxYear: parseInt(moment().format('YYYY'), 10)
        });

        $('.date_input').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
        });

        $('.date_input').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

         $('*[data-ref-class-content]').on('click', function(e) {
            let _idContent = $(this).data('ref-class-content');
            $('*[data-ref-class-content]').each(function () {
                let _idContent = $(this).data('ref-class-content');
                $(this).removeClass('border-b-2 text-violet-700');
                $('#'+_idContent).addClass('hidden');
            });

            $(this).addClass('border-b-2 text-violet-700');
            $('#'+_idContent).removeClass('hidden');
        })

        $('#add-info').on('click', function(e) {
            $('.add-info-icon').toggleClass('rotate-180');
            $('#add-info-content').toggle('hidden');
        })
        // **
        // * submit form ----->
        // *
        var resSubmit = await ApiService.submit_form('.submit-employee', (data) => {
            console.log('data', data)
            if(data.status == 'error'){
                if(data?.msg?.user_capture && data?.msg?.user_capture[0]?.toLowerCase().includes('invalid photo, no face found')) {
                    $('input[name=is_error_image]').val(-1);
                    $('input[name=emp_code]').addClass('cursor-not-allowed text-gray-300');
                    $('input[name=emp_code]').removeClass('focus:shadow-xs/focused(4px-primary) focus:border-violet-300');
                    $('input[name=emp_code]').attr('readonly', 'readonly');
                }
            } else {
                onInit($('.search-data-input').val());
            }

        });
    }

    async function open_modal_confirm(employee_id) {
        // **
        // * open modal confirm ----->
        // *
        await ApiService.get_confirm('.submit-delete-employee', '/employee/' + employee_id, null, () => {
            onInit($('.search-data-input').val());
        })
    }
</script>
@endsection
