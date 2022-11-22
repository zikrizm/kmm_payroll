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
        <div class="flex items-center gap-2.5">
            <div class="w-72">
                {!! FormCustom::input('date', null, [
                'placeholder' => 'Pilih tanggal penggajian',
                'class' => 'date_input',
                'readonly' => true,
                'prefixiconname' => 'calendar',
                ]) !!}
            </div>
            <section class="flex flex-col gap-1">
                <select class="select2-department hidden" name="">
                    <option value="" selected>Semua bagian</option>
                    @foreach (($department_bios ?? []) as $department)
                    <option value="{{ $department['id'] }}" @selected($department_bios->first()['id'] ==
                        $department['id'])>{{ $department['dept_name'] }}</option>
                    @endforeach
                </select>
                <label class="font-normal text-xs text-red-500 xs/max:text-xs parent_dept hint-text"></label>
            </section>
            <button onclick="get_modal()"
                class="flex items-center gap-2.5 px-4 h-[36px] mb-1 text-gray-500 text-sm font-medium  flex items-center border border-gray-200 shadow-sm rounded-lg">
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
            
            onInit({
                q: $('.search-data-input').val(),
                department_id: "{{ $department_bios->first()['id'] }}",
                date: { 
                    start_time: convertLocalTimezone(moment().startOf('week'), 'YYYY-MM-DD'), 
                    end_time: convertLocalTimezone(moment().endOf('week'), 'YYYY-MM-DD')
                }
            });

            $('input[name="date"]').daterangepicker({
                locale: { format: 'DD-MM-YYYY' },
                startDate: moment().startOf('week'),
                endDate: moment().endOf('week'),
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

                delete dataParams.page;
                onInit({ 
                    q: $('.search-data-input').val(),
                    date: { 
                        start_time: convertLocalTimezone(start, dateFormat), 
                        end_time: convertLocalTimezone(end, dateFormat)
                    } 
                });
            });

            $('.select2-department').select2({ minimumResultsForSearch: -1 });  
            $('.select2-department').show();
            $('.select2-department').on('select2:select', function (e) {
                delete dataParams.page;
                onInit({department_id: this.value});
            });


            $(".search-data-input").on('keyup', debounce(function(e) {
                if(e.key == 'Shift') return 0;
                delete dataParams.page;
                onInit( {...dataParams, q: this.value });
            }, 250));
        });
    
        async function onInit(data) {
            $('#loading-block-document').show();
            // **
            // * Build data params table ----->
            // *
            dataParams = { ...dataParams, ...data };
            
            // **
            // * get table ----->
            // *
            var res = await ApiService.get_table('/payroll-report', dataParams);
            $('.table-content').html(res);
            
            $('#loading-block-document').hide();

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
            var URL = (payroll_report_id) ? '/payroll-report/' + payroll_report_id + '/edit' : '/payroll-report/create';
            var res = await ApiService.get_modal(URL, null);
            $('input[name="holiday_date"]').daterangepicker({
                locale: { format: 'DD-MM-YYYY' },
                showDropdowns: true,
                minYear: 2000,
                drops: "auto",
                maxYear: parseInt(moment().format('YYYY'), 10)
            });
            let start_date = $('input[name="date"]').data('daterangepicker').startDate;
            let end_date = $('input[name="date"]').data('daterangepicker').endDate;
            let duration = 4000;
            $('.prosess-cointainer').each(function (index) {
                let thisElement = $(this);
                let thisIndex = index;
                let nextElement = thisElement.next();
                let nextElement4 = $('.prosess-cointainer').eq(index+4);
                console.log(nextElement4)
                let percentElementBeforeLength = 0;
                if(index > 0) percentElementBeforeLength = $('.prosess-cointainer').eq(index-1).find('.percent').length;

                let percentElement = $(this).find('.percent');
                percentElement.each(function (index) {
                    $(this).prop('Counter',0).delay( (index * duration) + ((percentElementBeforeLength * thisIndex) * duration) ).animate({
                        Counter: 100
                    }, {
                        duration: duration,
                        easing: 'swing',
                        step: function (now) {
                            $(this).text(Math.ceil(now));
                        },
                        complete: function () {
                            $(this).parent().toggle();
                            let iconElement = $(this).closest('.container-perdate').find('.icon-date-finish-prosess');
                            let dateTextElement = $(this).closest('.container-perdate').find('.proses-date');
                            iconElement.toggle();
                            dateTextElement.removeClass('text-gray-500');
                            dateTextElement.addClass('text-gray-300');

                            if(percentElement.length -1 == index) {
                                thisElement.find('.prosess-container-perdates').toggle('flex')
                                thisElement.find('.icon-finish-prosess').toggle();
                                thisElement.find('.text-prosess').text('Selesai');
                                if (!thisElement.find('.icon-on-prosess').is(':hidden')) 
                                    thisElement.find('.icon-on-prosess').toggle()
                                if (!thisElement.find('.icon-waiting-prosess').is(':hidden')) 
                                    thisElement.find('.icon-waiting-prosess').toggle()

                                if(nextElement.length){
                                    nextElement.find('.prosess-container-perdates').toggle('flex')
                                    nextElement.find('.icon-waiting-prosess').toggle();
                                    nextElement.find('.icon-on-prosess').toggle();
                                    nextElement.find('.text-prosess').text('Dalam proses');
                                }

                                let percentNextElementLength = nextElement.find('.percent').length;
                                setTimeout(() => {
                                    thisElement.toggle('flex');
                                    if(nextElement4.length) {
                                        nextElement4.toggle('flex');
                                    }
                                }, percentNextElementLength * duration);
                               
                                
                            }
                        }
                    });
                });
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