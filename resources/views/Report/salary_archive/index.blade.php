@extends('layouts.app')
@section('title', 'Salary archive')
@section('css')
<style></style>
@endsection
@section('content')
<div class="flex flex-col gap-6 flex-1 h-full overflow-auto bg-white px-8 pt-8 pb-12">
    <header class="flex justify-between items-start">
        <div class="flex flex-col gap-1">
            <p class="text-3xl font-medium text-gray-900">Arsip penggajian</p>
            <p class="text-base font-normal text-gray-500">Di sini untuk melihat penggajjian yang sudah ter-kalkulasi.
            </p>
        </div>
    </header>
    <hr>
    <div class="flex justify-between gap-2.5">
        <div class="w-72">
            {!! FormCustom::input('selected_date', null, [
            'placeholder' => 'Pilih tanggal arsip',
            'class' => 'date_input',
            'readonly' => true,
            'prefixiconname' => 'calendar',
            ]) !!}
        </div>
        <x-ui.search-data placeholder="Cari arsip" url="{{ route('salary-archive.index') }}" />
    </div>
    <div class="table-content"></div>
</div>

<script type="application/javascript">
    let dataParams = {};

    window.addEventListener('DOMContentLoaded', (event) => {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        onInit({ 
            q: $('.search-data-input').val(), 
            start_date: convertLocalTimezone(moment().startOf('week'), 'YYYY-MM-DD'), 
            end_date: convertLocalTimezone(moment().endOf('week'), 'YYYY-MM-DD')
        });
        $('input[name="selected_date"]').daterangepicker({
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
                start_date: convertLocalTimezone(start, dateFormat), 
                end_date: convertLocalTimezone(end, dateFormat)
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
        var res = await ApiService.get_table('/salary-archive', data);
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

    async function re_calculation(id) {
        // **
        // * open modal form ----->
        // *
        var res = await ApiService.get_modal('/salary-archive/create', {calculation_id: id});
        let start_date = $('input[name="selected_date"]').data('daterangepicker').startDate;
        let end_date = $('input[name="selected_date"]').data('daterangepicker').endDate;
        $('#kalkulasi').on('click', function(e) {
            $('.container-modal-calculate').removeClass('w-[440px]');
            $('.container-modal-calculate').addClass('w-[650px]');
            $('.close-calculate').hide();
            $('#content-confirm-calculate').hide();
            $('#content-loading-calculate').show();
            $('input[name="date[start_time]"]').val(start_date.format('YYYY-MM-DD'));
            $('input[name="date[end_time]"]').val(end_date.format('YYYY-MM-DD'));

            let duration = 1000;
            $('.prosess-cointainer').each(function (index) {
                let element = $(this);
                let indexElement = index;
                let nextElement = element.next();
                // let nextElement4 = $('.prosess-cointainer').eq(index+4);
                // let percentElementBeforeLength = 0;
                // if(index > 0) percentElementBeforeLength = $('.prosess-cointainer').eq(index-1).find('.percent').length;

                let text_prosses = $(this).find('.text-prosess');
                text_prosses.each(function (index) {
                    $(this).prop('Counter',0).delay( index * duration ).animate({
                        Counter: 100
                    }, {
                        duration: duration,
                        easing: 'swing',
                        step: function (now) {
                            // $(this).text(Math.ceil(now));
                        },
                        complete: function () {
                            // $(this).parent().toggle();
                            // let iconElement = $(this).closest('.container-perdate').find('.icon-date-finish-prosess');
                            // let dateTextElement = $(this).closest('.container-perdate').find('.proses-date');
                            // iconElement.toggle();
                            // dateTextElement.removeClass('text-gray-500');
                            // dateTextElement.addClass('text-gray-300');

                            // if(percentElement.length -1 == index) {
                            //     thisElement.find('.prosess-container-perdates').toggle('flex')
                            //     thisElement.find('.icon-finish-prosess').toggle();
                            //     thisElement.find('.text-prosess').text('Selesai');
                            //     if (!thisElement.find('.icon-on-prosess').is(':hidden')) 
                            //         thisElement.find('.icon-on-prosess').toggle()
                            //     if (!thisElement.find('.icon-waiting-prosess').is(':hidden')) 
                            //         thisElement.find('.icon-waiting-prosess').toggle()

                            //     if(nextElement.length){
                            //         nextElement.find('.prosess-container-perdates').toggle('flex')
                            //         nextElement.find('.icon-waiting-prosess').toggle();
                            //         nextElement.find('.icon-on-prosess').toggle();
                            //         nextElement.find('.text-prosess').text('Dalam proses');
                            //     }

                            //     let percentNextElementLength = nextElement.find('.percent').length;
                            //     setTimeout(() => {
                            //         setTimeout(() => {
                            //             thisElement.toggle('flex');
                            //         }, 1000);
                            //         if(nextElement4.length) {
                            //             nextElement4.toggle('flex');
                            //         }
                            //     }, percentNextElementLength * duration);
                            // }
                        }
                    });
                });
            });
        })
        // **
        // * submit form ----->
        // *
        var resSubmit = ApiService.submit_form('.submit-calculation-payroll', (data) => { 
            // onInit( { q: $('.search-data-input').val() });
        });
    }

    async function get_detail_salary_modal(id) {
        // **
        // * open modal form ----->
        // *
        var res = await ApiService.get_modal('/salary-archive/'+id, null);

        // **
        // * submit form ----->
        // *
        var resSubmit = ApiService.submit_form('.submit-user', (data) => { 
            onInit( { q: $('.search-data-input').val() });
        });
    }

    async function open_modal_confirm(user_id) {
        // **
        // * open modal confirm ----->
        // *
        await ApiService.get_confirm('.submit-delete-user', '/user/' + user_id, null, () => {
            onInit();
        })
    }


    function resetSortTable() {
        $('.sort-table').each(function(e) {
            $(this).removeClass('active');
            $(this).children('.sort-icon').removeClass('rotate-180');
        })
    }

    function sort_data(event) {
        let sortKey = $(event).data('sort-key');
        let sortUrl = $(event).data('sort-url');
        let isActive = $(event).hasClass('active');

        // Reset sort table
        resetSortTable();
        // Build Data sort table
        let field = { q: $('.search-data-input').val(), };
        field.sort = { name: sortKey, order: (isActive) ? 'ASC': 'DESC'}
        console.log(field)
        // Get Data sort table
        onInit(field)
    }
</script>
@endsection