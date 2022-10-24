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
            <button onclick="get_modal()" class="flex items-center gap-2.5 px-4 py-2 text-gray-500 text-sm font-medium 
                flex items-center border border-gray-200 shadow-sm rounded-lg">
                <x-icon icon="plus" width=18 height=18 viewBox="20 20" />
                Tambah karyawan
            </button>
            <button onclick="get_modal_CSV()" class="flex items-center gap-2.5 px-4 py-2 text-gray-500 text-sm font-medium 
                flex items-center border border-gray-200 shadow-sm rounded-lg">
                <x-icon icon="upload-cloud" width=18 height=18 viewBox="20 20" />
                Import CSV
            </button>
        </div>
    </header>
    <hr>
    {{-- <section id="dropzone">
        <form class="dropzone needsclick" id="upload_csv" enctype="multipart/form-data" method="post">
            @csrf
            <!-- {{ csrf_field() }} -->
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
            <main id="preview-contents"></main>
            <button type="submit" class="border" id="submit-all">
                Enviar files
            </button>
        </form>
        <div id="preview-template" style="display: none;">
            <div class="dz-preview dz-file-preview ">
                <section class="border-violet-600 border rounded-lg p-4 flex items-start gap-4">
                    <div class="box-content text-violet-600 rounded-full flex justify-center items-center w-8 h-8 bg-violet-100 
                        border border-4 border-violet-50">
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
                        <div class="w-full flex justify-between relative pr-10">
                            <div class="flex-1">
                                <p class="text-gray-700 text-sm font-medium dz-filename">
                                    <span class="break-words truncate w-[225px] block" data-dz-name=""></span>
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
                                <span data-dz-uploadprogress class="progress-bar flex h-full rounded bg-violet-600"
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
    </section> --}}
    <x-ui.search-data placeholder="Cari karyawan" url="{{ route('employee.index') }}" />
    <div class="table-content"></div>
    <x-ui.confirm-modal class="submit-delete-employee"></x-ui.confirm-modal>
</div>

<script type="module">
    Dropzone.autoDiscover = false;
</script>

<script type="application/javascript">
    let dataParams = {};
    let minSteps = 6, maxSteps = 60, timeBetweenSteps = 100, bytesPerStep = 1000;

    window.addEventListener('DOMContentLoaded', (event) => {
        // var dropzone = new Dropzone('#upload_csv', {
        //     url: "{{ route('employee.uploadCSV-store') }}", 
        //     headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        //     paramName: "file",
        //     previewsContainer: "#preview-contents",
        //     previewTemplate: document.querySelector('#preview-template').innerHTML,
        //     autoProcessQueue:false,
        //     uploadMultiple: true,
        //     parallelUploads: 10,
        //     addRemoveLinks: true,
        //     init: function() {
        //         $("#submit-all").on('click', function(e) {
        //             e.preventDefault();
        //             e.stopPropagation();
        //             if(dropzone.getQueuedFiles().length === 0) {
        //                 alert("Please drop or select file to upload !!!");
        //             } else {
        //                dropzone.processQueue();
        //             } 
        //         })

        //         this.on("maxfilesexceeded", function(file){
        //             this.removeFile(file);
        //         });

        //         this.on("sending", function(file, xhr, data) {
        //             // First param is the variable name used server side
        //             // Second param is the value, you can add what you what
        //             // Here I added an input value
        //             // console.log(file);
        //             // data.append("your_variable", $('#your_input').val());
        //         });

        //         this.on('uploadprogress', (file, progress, bytesSent) => {
        //             if (file.previewElement) {
        //                 var progressElement = file.previewElement.querySelector("[data-dz-uploadprogress]");
        //                 progressElement.style.width = progress + "%";
        //                 progressElement.parentElement.parentElement.querySelector(".progress-text").textContent = progress + "%";
        //             }
        //         });
                
        //         this.on("addedfile", function(file) {
        //             // var extension = file.name.substring(file.name.lastIndexOf('.')).replaceAll('.', '');
        //             // var isImage = mimeListImage.includes(extension);
                    
        //             var image = $(file.previewElement.querySelector("[data-dz-thumbnail]"));
        //             var isImage = file.type.includes('image/');
        //             $(file.previewElement.querySelector("#icon-mime-loading")).hide()
        //             if(isImage) {
        //                 $(file.previewElement.querySelector("#icon-image")).show();
        //                 var totalSteps = Math.round(Math.min(maxSteps, Math.max(minSteps, file.size / bytesPerStep)));
        //                 for (var step = 0; step < totalSteps; step++) {
        //                     var duration = timeBetweenSteps * (step + 1);
        //                     if(step == totalSteps - 1)
        //                     setTimeout(function(file, totalSteps, step) {
        //                         return function() {
        //                             $(file.previewElement.querySelector("#loading-icon")).hide();
        //                             $(image).parent().parent().show();
        //                             image.attr({ alt: file.name, src: file.dataURL});
        //                         };
        //                     } (file, totalSteps, step), duration);
        //                 }
        //             } else {
        //                 $(file.previewElement.querySelector("#loading-icon")).hide();
        //                 $(file.previewElement.querySelector("#icon-file")).show()
        //             }
        //         });
        //     },
        // });
        onInit($('.search-data-input').val());
        $(".search-data-input").on('keyup', debounce(function(e) {
            if(e.key == 'Shift') return 0;
            delete dataParams.page;
            onInit( { q: this.value });
        }, 250));
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
        console.log("Sdfsdfsd");
        Dropzone.autoDiscover = false;
        var dropzone = new Dropzone('#upload_csv', {
            url: "{{ route('employee.uploadCSV-store') }}", 
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            paramName: "file",
            previewsContainer: "#preview-contents",
            previewTemplate: document.querySelector('#preview-template').innerHTML,
            autoProcessQueue:false,
            uploadMultiple: true,
            parallelUploads: 10,
            addRemoveLinks: true,
            acceptedFiles: 'text/csv',
            init: function() {
                $("#submit-all").on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if(dropzone.getQueuedFiles().length === 0) {
                        alert("Please drop or select file to upload !!!");
                    } else {
                       dropzone.processQueue();
                    } 
                })

                this.on("error", function(file, errorMessage) {
                    alert("error : " + errorMessage );
                    this.removeFile(file);
                });

                this.on("success", function(file, response) {
                    handleMessage(response)
                    if (response.response < 200 || response.response >= 300) {
                        var boxPreviewElement = $(file.previewElement).find('#box-preview');
                        boxPreviewElement.removeClass('border-gray-200');
                        boxPreviewElement.addClass('border-red-300');
                        var iconMimePreviewElement = $(file.previewElement).find('#icon-mime-preview');
                        iconMimePreviewElement.addClass('text-red-600 bg-red-100 border-red-50');
                        $(file.previewElement).find('.dz-remove-file').removeClass('text-gray-500');
                        $(file.previewElement).find('.dz-remove-file').addClass('text-red-500');
                        $(file.previewElement).find('.progress-bar').addClass('bg-red-600');
                       
                    } else {
                       var boxPreviewElement = $(file.previewElement).find('#box-preview');
                       boxPreviewElement.addClass('border-violet-300');
                       var iconMimePreviewElement = $(file.previewElement).find('#icon-mime-preview');
                       iconMimePreviewElement.addClass('text-violet-600 bg-violet-100 border-violet-50');
                        $(file.previewElement).find('.progress-bar').addClass('bg-violet-600');
                       $(file.previewElement).find('.dz-remove-file').attr('disabled', 'disabled');
                       $(file.previewElement).find('.dz-remove-file').html(
                            `<span class="w-4 h-4 rounded-full bg-violet-600 flex items-center justify-center text-white">
                                <x-icon icon="check" width=12 height=12 viewBox="20 20" />
                            </span>`
                       )

                        onInit( { q: $('.search-data-input').val() });
                    }
                })

                this.on("maxfilesexceeded", function(file){
                    this.removeFile(file);
                });

                this.on("sending", function(file, xhr, data) {
                    console.log(file);
                    // First param is the variable name used server side
                    // Second param is the value, you can add what you what
                    // Here I added an input value
                    // console.log(file);
                    // data.append("your_variable", $('#your_input').val());
                });

                this.on('uploadprogress', (file, progress, bytesSent) => {
                    if (file.previewElement) {
                        var progressElement = file.previewElement.querySelector("[data-dz-uploadprogress]");
                        progressElement.style.width = progress + "%";
                        progressElement.parentElement.parentElement.querySelector(".progress-text").textContent = progress + "%";
                    }
                });
                
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
        });

        // var resubmit = await ApiService.submit_form('.submit-employee-csv', (data) => {

        // });
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