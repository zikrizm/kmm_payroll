@extends('layouts.app')
@section('title', 'Payroll report')
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
    </header>
    <hr>
    <div class="flex justify-between gap-2.5">
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
                    <option value="">Semua bagian</option>
                    @foreach (($department_bios ?? []) as $department)
                    <option value="{{ $department['dept_code'] }}" @selected($department_bios->first()['dept_code'] ==
                        $department['id'])>{{ $department['dept_name'] }}</option>
                    @endforeach
                </select>
                <label class="font-normal text-xs text-red-500 xs/max:text-xs parent_dept hint-text"></label>
            </section>
            <button onclick="get_modal()"
                class="truncate flex items-center gap-2.5 px-4 h-[36px] mb-1 text-gray-500 text-sm font-medium  flex items-center border border-gray-200 shadow-sm rounded-lg">
                <x-icon icon="dollar-sign" width=18 height=18 viewBox="20 20" />
                <p class="truncate">Hitung penggajian</p>
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
                department_code: "{{ $department_bios->first()['dept_code'] }}",
                start_date: convertLocalTimezone(moment().startOf('week'), 'DD-MM-YYYY'), 
                end_date: convertLocalTimezone(moment().endOf('week'), 'DD-MM-YYYY')
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
                var dateFormat = 'DD-MM-YYYY';

                delete dataParams.page;
                onInit({ 
                    q: $('.search-data-input').val(),
                    start_date: convertLocalTimezone(start, dateFormat), 
                    end_date: convertLocalTimezone(end, dateFormat)
                });
            });

            $('.select2-department').select2({ minimumResultsForSearch: -1 });  
            $('.select2-department').show();
            $('.select2-department').on('select2:select', function (e) {
                delete dataParams.page;
                onInit({department_code: this.value});
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
            let runAnimation = true , setTimeoutProses;
            let start_date = $('input[name="date"]').data('daterangepicker').startDate;
            let end_date = $('input[name="date"]').data('daterangepicker').endDate;
            var URL = (payroll_report_id) ? '/payroll-report/' + payroll_report_id + '/edit' : '/payroll-report/create';
            var res = await ApiService.get_modal(URL, { 
                start_date: start_date.format('DD-MM-YYYY'),
                end_date: end_date.format('DD-MM-YYYY'),
            });

            $('#kalkulasi').on('click', function(e) {
                console.log("Dfsdfsdfsdfsdfsdfsdf")
                $('#container-modal-calculate').removeClass('w-[440px]');
                $('#container-modal-calculate').addClass('w-[650px]');
                $('#x-icon-close').hide();
                $('#content-confirm-calculate').hide();
                $('#content-loading-calculate').show();
                let duration = 1000;
                let length = $('.prosess-cointainer').length;
                $('.prosess-cointainer').each(function (i) {
                    if(length -1 != i) {
                        let element = $(this);
                        let index = i;
                        let nextElement = element.next();
                        let nextElementEq = $('.prosess-cointainer').eq(index+6);

                        let text_prosses = $(this).find('.text-prosess');
                        text_prosses.prop('Counter',0).delay( index * duration ).animate({
                            Counter: 100
                        }, {
                            duration: duration,
                            easing: 'swing',
                            // step: function (now) { },
                            step: function (now) { if(runAnimation) $(this).text(Math.ceil(now)+'%'); },
                            complete: function () {
                                if(runAnimation) {
                                    if(length - 6 > index) {
                                        setTimeoutProses = setTimeout(() => {
                                            element.toggle('flex');
                                        }, 1000);
                                    }
                                    element.find('.icon-finish-prosess').toggle();
                                    if (!element.find('.icon-on-prosess').is(':hidden')) 
                                        element.find('.icon-on-prosess').toggle()
                                    if (!element.find('.icon-waiting-prosess').is(':hidden')) 
                                        element.find('.icon-waiting-prosess').toggle()
                                    element.find('.text-prosess').text('Selesai');
                                    nextElement.find('.icon-on-prosess').toggle()
                                    nextElement.find('.icon-waiting-prosess').toggle()
                                    nextElement.find('.text-prosess').text('Dalam proses');
                                    nextElementEq.toggle('flex');                       
                                }
                            }
                        });
                    }
                });
            })


            var resSubmit = ApiService.submit_form('.submit-calculation-payroll', (data) => { 
                let length = $('.prosess-cointainer').length;
                runAnimation = false;
                clearTimeout(setTimeoutProses);
                $('.prosess-cointainer').hide();
                $('.prosess-cointainer').each(function (i) {
                    $(this).find('.text-prosess').stop();
                    if(length -1 != i) {
                        // $(this).show();
                        $(this).find('.icon-finish-prosess').show();
                        $(this).find('.icon-waiting-prosess').hide()
                        $(this).find('.icon-on-prosess').hide();
                        $(this).find('.text-prosess').text('Selesai');

                        if(length - 8 < i) $(this).show();
                    } else {
                        $(this).show();
                        $(this).find('.icon-waiting-prosess').hide()
                        $(this).find('.icon-on-prosess').show();
                        $(this).find('.text-prosess').prop('Counter',0).animate({
                            Counter: 100
                        }, {
                            duration: 1000,
                            easing: 'swing',
                            step: function (now) { $(this).text(Math.ceil(now)+'%'); },
                            complete: function () {
                                $(this).find('.icon-finish-prosess').show();
                                $(this).find('.icon-waiting-prosess').hide()
                                $(this).find('.icon-on-prosess').hide();
                                $(this).find('.text-prosess').text('Selesai');
                                setTimeout(() => {
                                    $('#content-loading-calculate').hide();
                                    $('#content-finish-calculate').show();
                                }, 1000);
                            }
                        });
                    }
                });
                
            }, { allowLoading: false, allowCloseModal: false});
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