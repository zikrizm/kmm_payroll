@extends('layouts.app')
@section('title', 'Operational')
@section('css')
<style></style>
@endsection
@section('content')
<div class="flex flex-col gap-6 flex-1 h-full overflow-auto bg-white px-8 pt-8 pb-12">
    <header class="flex justify-between items-start">
        <div class="flex flex-col gap-1">
            <p class="text-3xl font-medium text-gray-900">Operasional</p>
            <p class="text-base font-normal text-gray-500">Disini untuk memasukan jadwal kerjaan seminggu kedepan.</p>
        </div>
        <div class="">
            <button onclick="get_modal()" class="flex items-center gap-2.5 px-4 py-2 text-gray-500 text-sm font-medium 
                flex items-center border border-gray-200 shadow-sm rounded-lg">
                <x-icon icon="plus" width=18 height=18 viewBox="20 20" />
                Tambah operasional
            </button>
        </div>
    </header>
    <hr>
    <div class="flex justify-between">
        <div class="w-72">
            {!! FormCustom::input('date', null, [
            'placeholder' => 'Pilih tanggal operasional',
            'class' => 'date_input',
            'readonly' => true,
            'prefixiconname' => 'calendar',
            ]) !!}
        </div>
        <x-ui.search-data placeholder="Cari operasional" url="{{ route('operational.index') }}" />
    </div>
    <div class="table-content"></div>
    <x-ui.confirm-modal class="submit-delete-operational"></x-ui.confirm-modal>
</div>

<script type="application/javascript">
    let dataParams = {};

        window.addEventListener('DOMContentLoaded', (event) => {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            
            onInit( { 
                q: $('.search-data-input').val(),
                date: { 
                    start_date:convertLocalTimezone(moment().subtract(6, 'days')), 
                    end_date: convertLocalTimezone(moment())
                }
            });

            $('input[name="date"]').daterangepicker({
                locale: { format: 'YYYY-MM-DD' },
                startDate: moment().subtract(6, 'days'),
                endDate: moment(),
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                alwaysShowCalendars: true,
                showCustomRangeLabel: false,
                showDropdowns: true,
                minYear: 2000,
                drops: "auto",
                maxYear: parseInt(moment().format('YYYY'), 10)
            },function(start, end, label) {
                var dateFormat = 'YYYY-MM-DD';

                $('.search-data-input').val('');
                delete dataParams.page;
                onInit({ 
                    date: { 
                        start_date: convertLocalTimezone(start, dateFormat), 
                        end_date: convertLocalTimezone(end, dateFormat)
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
            var res = await ApiService.get_table('/operational', data);
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
    
        async function get_modal(operational_id) {
            // **
            // * open modal form ----->
            // *
            var URL = (operational_id) ? '/operational/' + operational_id + '/edit' : '/operational/create';
            var res = await ApiService.get_modal(URL, null);
            $('.operational_date').daterangepicker({
                locale: { format: 'YYYY-MM-DD' },
                startDate: moment().subtract(6, 'days'),
                endDate: moment(),
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                alwaysShowCalendars: true,
                showCustomRangeLabel: false,
                showDropdowns: true,
                minYear: 2000,
                drops: "auto",
                maxYear: parseInt(moment().format('YYYY'), 10)
            });

            $('.operational_date').on('change', function(){
                if($('.specific_date').length) {
                    $('.specific_date').data('daterangepicker').minDate = moment($('.operational_date').data('daterangepicker').startDate._d);
                    $('.specific_date').data('daterangepicker').maxDate = moment($('.operational_date').data('daterangepicker').endDate._d);
                }
            });

            $('.select2-department').select2();
            $('.select2-department').on('select2:select', async function (e) {
                // var str = $("#s2id_search_code .select2-choice span").text('ss');
                let _response = await (new NetworkUtils()).emitter('GET', '/operational-card-sub-dept', {dept_id: this.value}, {})
                if (_response.response < 200 || _response.response >= 300) {
                    // * SHOW NOTIFICATION ----->
                } else {
                    $('#sub-department-content').html(_response.data);
                    $('.select2-status').select2();
                    $('.specific_date').daterangepicker({
                        locale: { format: 'YYYY-MM-DD' },
                        startDate: $('.operational_date').data('daterangepicker').startDate._d,
                        endDate: $('.operational_date').data('daterangepicker').endDate._d ,
                        minDate: $('.operational_date').data('daterangepicker').startDate._d,
                        maxDate: $('.operational_date').data('daterangepicker').endDate._d,
                        ranges: {
                            'Today': [moment(), moment()],
                            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                            'This Month': [moment().startOf('month'), moment().endOf('month')],
                            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                        },
                        alwaysShowCalendars: true,
                        showCustomRangeLabel: false,
                        showDropdowns: true,
                        minYear: 2000,
                        drops: "auto",
                        maxYear: parseInt(moment().format('YYYY'), 10)
                    });
                }
            });
            $('.select2-status').select2();
            $('.specific_date').daterangepicker({
                locale: { format: 'YYYY-MM-DD' },
                startDate: $('.operational_date').data('daterangepicker').startDate._d,
                endDate: $('.operational_date').data('daterangepicker').endDate._d ,
                minDate: $('.operational_date').data('daterangepicker').startDate._d,
                maxDate: $('.operational_date').data('daterangepicker').endDate._d,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                alwaysShowCalendars: true,
                showCustomRangeLabel: false,
                showDropdowns: true,
                minYear: 2000,
                drops: "auto",
                maxYear: parseInt(moment().format('YYYY'), 10)
            });
           
            // **
            // * submit form ----->
            // *
            var resSubmit = ApiService.submit_form('.submit-operational', (data) => { 
                onInit( { q: $('.search-data-input').val() });
            });
        }
        // '/search-employee-for-dropdown'
        async function get_modal_add_employee(operational_id) {
            // **
            // * open modal form ----->
            // *
            var URL = '/operational/' + operational_id + '/add-employees-to-help';
            var res = await ApiService.get_modal(URL, null);
            $('.select2').select2();
            select2_employee_to_help('/search-employees-to-help', {
                operational_id: operational_id,
            });
            $('.employee-helpdate').daterangepicker({
                locale: { format: 'YYYY-MM-DD' },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                alwaysShowCalendars: true,
                showCustomRangeLabel: false,
                showDropdowns: true,
                minYear: 2000,
                drops: "auto",
                maxYear: parseInt(moment().format('YYYY'), 10)
            });
            $('.employee-helpdate').data('daterangepicker').minDate = moment($('.employee-helpdate').data('daterangepicker').startDate._d);
            $('.employee-helpdate').data('daterangepicker').maxDate = moment($('.employee-helpdate').data('daterangepicker').endDate._d);
        }

        async function open_modal_confirm(operational_id) {
        // **
        // * open modal confirm ----->
        // *
        await ApiService.get_confirm('.submit-delete-operational', '/operational/' + operational_id, null, () => {
            onInit();
        })
    }
</script>
@endsection