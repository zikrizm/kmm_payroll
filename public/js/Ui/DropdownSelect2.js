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