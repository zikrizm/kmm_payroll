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
    <x-ui.search-data placeholder="Cari karyawan" url="{{ route('employee.index') }}" />
    <div class="table-content"></div>
    <x-ui.confirm-modal class="submit-delete-employee"></x-ui.confirm-modal>
</div>

<script type="application/javascript">
    let dataParams = {};

    window.addEventListener('DOMContentLoaded', (event) => {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

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