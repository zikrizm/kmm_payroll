@extends('layouts.app')
@section('title', 'User')
@section('css')
<style></style>
@endsection
@section('content')
<div class="flex flex-col gap-6 flex-1 h-full overflow-auto bg-white px-8 pt-8 pb-12">
    <header class="flex justify-between items-start">
        <div class="flex flex-col gap-1">
            <p class="text-3xl font-medium text-gray-900">Laporan penggajian</p>
            <p class="text-base font-normal text-gray-500">Disini untuk melihat status laporan penggajian.</p>
        </div>
        <div class="">
        </div>
    </header>
    <hr>
    <div class="flex justify-between">
        <div class="flex items-center gap-3">
            <div class="w-72">
                {!! FormCustom::input('date', null, [
                'placeholder' => 'Pilih tanggal penggajian',
                'class' => 'date_input',
                'readonly' => true,
                'prefixiconname' => 'calendar',
                ]) !!}
            </div>
            <button onclick="get_modal()" class="flex items-center gap-2.5 px-4 h-[36px] mb-1 text-gray-500 text-sm font-medium  flex items-center border border-gray-200 shadow-sm rounded-lg">
                <x-icon icon="dollar-sign" width=18 height=18 viewBox="20 20" />
                Hitung penggajian
            </button>
        </div>
        <x-ui.search-data placeholder="Cari penggajian" url="{{ route('payroll-report.index') }}" />
    </div>
    <div class="table-content"></div>
    <x-ui.confirm-modal class="submit-delete-payroll-report"></x-ui.confirm-modal>
</div>

<script type="application/javascript">
    let dataParams = {};

        window.addEventListener('DOMContentLoaded', (event) => {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            
            dataParams = {
                date: { 
                    start_time: convertLocalTimezone(moment().startOf("month").toDate(), 'YYYY-MM-DD'), 
                    end_time: convertLocalTimezone(moment().subtract(6, 'days'), 'YYYY-MM-DD')
                }
            }
            onInit(dataParams);

            $('input[name="date"]').daterangepicker({
                locale: { format: 'YYYY-MM-DD' },
                startDate: moment().startOf("month").toDate(),
                endDate: moment().subtract(6, 'days'),
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
                        start_time: convertLocalTimezone(start, dateFormat), 
                        end_time: convertLocalTimezone(end, dateFormat)
                    } 
                });
            });


            $(".search-data-input").on('keyup', debounce(function(e) {
                if(e.key == 'Shift') return 0;
                delete dataParams.page;
                onInit( {...dataParams, q: this.value });
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
            var res = await ApiService.get_table('/payroll-report', data);
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
    
        async function get_modal(payroll_report_id) {
            // **
            // * open modal form ----->
            // *
            console.log(payroll_report_id)
            var URL = (payroll_report_id) ? '/payroll-report/' + payroll_report_id + '/edit' : '/payroll-report/create';
            var res = await ApiService.get_modal(URL, null);
            $('input[name="holiday_date"]').daterangepicker({
                locale: { format: 'YYYY-MM-DD' },
                showDropdowns: true,
                minYear: 2000,
                drops: "auto",
                maxYear: parseInt(moment().format('YYYY'), 10)
            });

            // **
            // * submit form ----->
            // *
            var resSubmit = ApiService.submit_form('.submit-payroll-report', (data) => { 
                onInit( { q: $('.search-data-input').val() });
            });
        }

        async function open_modal_confirm(payroll_report_id) {
        // **
        // * open modal confirm ----->
        // *
        await ApiService.get_confirm('.submit-delete-payroll-report', '/payroll-report/' + payroll_report_id, null, () => {
            onInit();
        })
    }
</script>
@endsection