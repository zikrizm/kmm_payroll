@extends('layouts.app')
@section('title', 'User')
@section('css')
<style></style>
@endsection
@section('content')
<div class="flex flex-col gap-6 flex-1 h-full overflow-auto bg-white px-8 pt-8 pb-12">
    <header class="flex justify-between items-start">
        <div class="flex flex-col gap-1">
            <p class="text-3xl font-medium text-gray-900">Pengunduran Diri Karyawan</p>
            <p class="text-base font-normal text-gray-500">Pengaturan pengunduran diri karyawan.</p>
        </div>
        <div class="">
            <button onclick="get_modal()" class="flex items-center gap-2.5 px-4 py-2 text-gray-500 text-sm font-medium 
                flex items-center border border-gray-200 shadow-sm rounded-lg">
                <x-icon icon="plus" width=18 height=18 viewBox="20 20" />
                Add pengunduran
            </button>
        </div>
    </header>
    <hr>
    <div class="flex justify-between">
        <div class="w-52">
            {!! FormCustom::input('resign_date', null, [ 'placeholder' => 'Select resign date',
            'class' => 'date_input', 'readonly' => true, 'prefixiconname' => 'calendar' ]) !!}
        </div>
        <x-ui.search-data placeholder="Search for resign" url="{{ route('resign.index') }}" />
    </div>
    <div class="table-content"></div>
    <x-ui.confirm-modal class="submit-delete-resign"></x-ui.confirm-modal>
</div>

<script type="application/javascript">
    let dataParams = {};

    window.addEventListener('DOMContentLoaded', (event) => {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        onInit({ 
            q: $('.search-data-input').val(),
            date: { 
                start_date: convertLocalTimezone(moment().startOf("month").toDate(), 'YYYY-MM-DD'), 
                end_date: convertLocalTimezone(moment().endOf("month").toDate(), 'YYYY-MM-DD')
            }
        });

        $('input[name="resign_date"]').daterangepicker({
            locale: { format: 'YYYY-MM-DD' },
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            singleDatePicker: true,
            alwaysShowCalendars: true,
            showCustomRangeLabel: false,
            showDropdowns: true,
            minYear: 1980,
            drops: "auto",
            maxYear: parseInt(moment().format('YYYY'), 10)
        },function(start, end, label) {
            var dateFormat = 'YYYY-MM-DD HH:mm:ss';

            $('.search-data-input').val('');
            delete dataParams.page;
            onInit({ 
                date: { 
                    start_time: convertLocalTimezone(start, dateFormat), 
                    end_time: convertLocalTimezone(end, dateFormat)
                } 
            });
        });

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
        var res = await ApiService.get_table('/resign', dataParams);
        $('.table-content').html(res);

        // // **
        // // * pagination table ----->
        // // *
        // $('.pagination-button').on('click', function() {
        //     let pUrl = $(this).data('pagination-url');
        //     $('.search-data-input').val();
        //     get_data_table(pUrl, null);
        // })
    }

    async function get_modal(resign_id) {
        // **
        // * open modal form ----->
        // *
        var URL = (resign_id) ? '/resign/' + resign_id + '/edit' : '/resign/create';
        var res = await ApiService.get_modal(URL, null);
        $('.select2').select2();
        select2_employee();

        $('.date_input').daterangepicker({
            locale: { format: 'YYYY-MM-DD' },
            singleDatePicker: true,
            showDropdowns: true,
            minYear: 2000,
            drops: "auto",
            maxYear: parseInt(moment().format('YYYY'), 10)
        });

        // **
        // * submit form ----->
        // *
        var resSubmit = await ApiService.submit_form('.submit-resign', (data) => {
            onInit($('.search-data-input').val());
        });
    }

    async function open_modal_confirm(employee_id) {
        // **
        // * open modal confirm ----->
        // *
        await ApiService.get_confirm('.submit-delete-resign', '/resign/' + employee_id, null, () => {
            onInit($('.search-data-input').val());
        })
    }
</script>
@endsection