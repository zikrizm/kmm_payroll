function init_dropzone(options) {
    Dropzone.autoDiscover = false;
    var dropzone = new Dropzone('#upload_csv', {
        url: options?.url,
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        paramName: "file",
        previewsContainer: "#preview-contents",
        previewTemplate: document.querySelector('#preview-template').innerHTML,
        autoProcessQueue: false,
        uploadMultiple: true,
        parallelUploads: 10,
        addRemoveLinks: true,
        acceptedFiles: 'text/csv',
        // acceptedFiles: '.xls,.csv',
        maxFiles: 1,
        init: function () {
            $("#submit-all").on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                if (dropzone.getQueuedFiles().length === 0) {
                    alert("Please drop or select file to upload !!!");
                } else {
                    $('#loading-block-document').show();
                    dropzone.processQueue();
                }
            })

            this.on("error", function (file, errorMessage) {
                alert("error : " + errorMessage);
                this.removeFile(file);
            });

            this.on("success", function (file, response) {
                handleMessage(response)
                $('#loading-block-document').hide();
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

                    onInit({ q: $('.search-data-input').val() });
                }
            })

            this.on("maxfilesexceeded", function (file) {
                this.removeFile(file);
            });

            this.on("sending", function (file, xhr, data) {
                // console.log(file);
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

            this.on("addedfile", function (file) {
                // var extension = file.name.substring(file.name.lastIndexOf('.')).replaceAll('.', '');
                // var isImage = mimeListImage.includes(extension);

                var image = $(file.previewElement.querySelector("[data-dz-thumbnail]"));
                var isImage = file.type.includes('image/');
                $(file.previewElement.querySelector("#icon-mime-loading")).hide()
                if (isImage) {
                    $(file.previewElement.querySelector("#icon-image")).show();
                    var totalSteps = Math.round(Math.min(maxSteps, Math.max(minSteps, file.size / bytesPerStep)));
                    for (var step = 0; step < totalSteps; step++) {
                        var duration = timeBetweenSteps * (step + 1);
                        if (step == totalSteps - 1)
                            setTimeout(function (file, totalSteps, step) {
                                return function () {
                                    $(file.previewElement.querySelector("#loading-icon")).hide();
                                    $(image).parent().parent().show();
                                    image.attr({ alt: file.name, src: file.dataURL });
                                };
                            }(file, totalSteps, step), duration);
                    }
                } else {
                    $(file.previewElement.querySelector("#loading-icon")).hide();
                    $(file.previewElement.querySelector("#icon-file")).show()
                }
            });
        },
    });
}