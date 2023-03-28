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
        var res = await ApiService.get_table('/salary-archive', dataParams);
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
            let runAnimation = true , setTimeoutProses;
            var res = await ApiService.get_modal('/salary-archive/re-calculate', {  salary_id: id});

            $('#kalkulasi').on('click', function(e) {
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

            var resSubmit = ApiService.submit_form('.submit-re-calculation', (_response) => { 
                if (_response.response < 200 || _response.response >= 300) {
                    // * SET NOTIFICATION MESSAGE REQUIRED ----->
                } else {
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

                    onInit( { q: $('.search-data-input').val() });
                }
            }, { allowLoading: false, allowCloseModal: false});
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