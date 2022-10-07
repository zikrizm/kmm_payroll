function select2_employee() {
    $(".select2-employee").select2({
        ajax: {
            url: '/search-employee-for-dropdown',
            data: function (params) {
                return { q: params.term };
            },
            processResults: function (data) {
                return { results: data.data };
            }
        },
        templateResult: templateResultEmp,
        templateSelection: templateSelectionEmp,
    });

    function templateResultEmp(opt) {
        opt.photo = (opt.photo) ? opt.photo : 'files/nophoto.gif';
        var $opt = $(
            `<div class="flex items-center">
                <div class="flex gap-3 items-center py">
                    <img src="${API + opt.photo}" alt="" class="w-8 object-cover h-8 min-w-[32px] min-h-[32px] rounded-full">
                    <div>
                        <p class="text-gray-900 text-sm font-medium truncate sm/max:w-12">
                            ${opt.first_name ?? opt.text ?? '-'} ${opt.last_name ?? ''}
                        </p>
                        <p class="text-gray-500 text-sm font-normal truncate sm/max:w-12">
                            ${opt.email ?? '-'}
                        </p>
                    </div>
                </div>
            </div> `
        );
        return $opt;
    };

    function templateSelectionEmp(opt) {
        var isDefault = false;
        var attr = $(opt.element).attr('default');
        isDefault = (typeof attr !== 'undefined' && attr !== false);

        opt.photo = (opt.photo) ? opt.photo : 'files/nophoto.gif';

        var $opt = $(
            `<div class="flex items-center bg-black">
                <div class="flex gap-3 items-center py">
                    ${(!isDefault) ? `<img src="${API + opt.photo}" alt="" class="w-5 object-cover h-5 min-w-[20px] min-h-[20px] rounded-full">` : ''}
                    <div>
                        <p class="${!isDefault ? 'text-gray-900' : 'text-gray-500'} text-sm font-medium truncate">
                            ${opt.first_name ?? opt.text ?? ''} ${opt.last_name ?? ''}
                        </p>
                    </div>
                </div>
            </div> `
        );
        return $opt;
    };
}

function select2_break_time() {
    $(".select2-break-time").select2({
        placeholder: "Silahkan pilih istirahat",
        templateResult: (opt) => {
            let _data = {};
            if(opt.title) _data = JSON.parse(opt.title);

            var $opt = $(
                `<div class="flex items-center">
                    <div class="flex flex-1 flex-col gap-1 items-start">
                        <p class="text-sm font-medium text-gray-700 capitalize">${_data.name}</p> 
                        <div class="flex items-center gap-2"> 
                            <p class="text-sm text-gray-700">${_data.start_time} -</p> 
                            <p class="text-sm text-gray-700">${_data.start_time}</p> 
                        </div>
                    </div>
                    <div class="flexc items-center"> 
                        <p class="flex items-center gap-2 text-base font-medium text-gray-700">${_data.duration ?? ''}<span class="text-xs">${opt.duration ? 'Menit(s)' : ''}</span></p> 
                    </div>
                </div>`
            );
            return $opt;
        },
    });
}

function select2_timetable() {
    $(".select2-timetable").select2({
        placeholder: "Silahkan pilih jadwal",
        templateResult: (opt) => {
            let _data = {};
            if(opt.title) _data = JSON.parse(opt.title);

            var $opt = $(
                `<div class="flex items-center">
                    <div class="flex flex-1 flex-col gap-1 items-start">
                        <p class="text-sm font-medium text-gray-700 capitalize">${_data.name}</p> 
                        <div class="flex items-center gap-2"> 
                            <p class="text-sm text-gray-700">${_data.in_time} -</p> 
                            <p class="text-sm text-gray-700">${_data.out_time}</p> 
                        </div>
                    </div>
                </div>`
            );
            return $opt;
        },
    });
}