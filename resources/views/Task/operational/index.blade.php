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
            <p class="text-base font-normal text-gray-500">Pengaturan operasional karyawan</p>
        </div>
        <div class="">

        </div>
    </header>
    <hr>
    <div class="flex justify-between">
        <div class="flex items-center gap-3">
            <div class="w-72">
                {!! FormCustom::input('header-date', null, [
                'placeholder' => 'Pilih tanggal operasional',
                'class' => 'date_input',
                'readonly' => true,
                'prefixiconname' => 'calendar',
                ]) !!}
            </div>
            <div class="flex items-center gap-3 mb-1">
                <button onclick="get_modal()" class="flex items-center gap-2.5 px-4 py-2 text-gray-500 text-sm font-medium 
                flex items-center border border-gray-300 shadow-sm rounded-lg">
                    <x-icon icon="plus" width=18 height=18 viewBox="20 20" />
                    Tambah operasional
                </button>
                <button onclick="get_modal_task()" class="flex items-center gap-2.5 px-4 py-2 text-gray-500 text-sm font-medium 
                flex items-center border border-gray-300 shadow-sm rounded-lg">
                    <x-icon icon="plus" width=18 height=18 viewBox="20 20" />
                    Tambah penugasan
                </button>
            </div>

        </div>
        <x-ui.search-data placeholder="Cari operasional" url="{{ route('operational.index') }}" />
    </div>
    <div class="table-content"></div>
    <x-ui.confirm-modal class="submit-delete-operational"></x-ui.confirm-modal>
    <x-ui.confirm-modal class="submit-delete-request-task"></x-ui.confirm-modal>
</div>

<script type="application/javascript">
    let dataParams = {};

        window.addEventListener('DOMContentLoaded', (event) => {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            var daynow= moment().day();
            onInit({ 
                q: $('.search-data-input').val(),
                date: { 
                    start_date:convertLocalTimezone(moment().subtract(6, 'days'),'YYYY-MM-DD'), 
                    end_date: convertLocalTimezone(moment(),'YYYY-MM-DD')
                }
            });

            $('input[name="header-date"]').daterangepicker({
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
            }, function(start, end, label) {
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
            var res = await ApiService.get_table('/operational', dataParams);
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
    
        async function get_modal(id, data_operasional) {
            // **
            // * open modal form ----->
            // *
            var URL = (id) ? '/operational/' + id + '/edit' : '/operational/create';
            var res = await ApiService.get_modal(URL, { ...data_operasional});
            // var anElement = new AutoNumeric.multiple('.number',{decimalPlaces:0,minimumValue: 0,decimalCharacter: ',', digitGroupSeparator : ""});
            var anElement = new AutoNumeric.multiple('.number',{decimalCharacterAlternative:'.',modifyValueOnWheel: false, minimumValue: 0,decimalCharacter: ',', digitGroupSeparator : ""});
            
            if(!data_operasional) {
                $('.operational_date').daterangepicker({
                    locale: { format: 'YYYY-MM-DD' },
                    startDate: moment(),
                    endDate: moment().add(6, 'days'),
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
                }, function(start, end, label) {
                    if($('.select2-department').val()) {
                        var date = start.format('YYYY-MM-DD')+' - '+ end.format('YYYY-MM-DD');
                        get_card_operational(true, date, $('.select2-department').val());
                    }
                });
            }

            $('.select2-department').select2();
            if(id) {
                $('.select2-department').select2().attr("disabled", true)
                $('input[name="select_all_timetable"]').on('change', function(e) {
                    var isChecked = $(this).is(':checked');
                    $('.timetable_status').prop('checked', isChecked);
                    $('.ot-limit-content').each(function(e) {
                        if(isChecked) {
                            if ($(this).is(':hidden')) $(this).toggle('hidden');
                        } else {
                            if (!$(this).is(':hidden')) $(this).toggle('hidden');
                        }
                        
                    })
                })
                $('.timetable_status').on('change', function(e) {
                    $(this).closest('tr').find('.ot-limit-content').toggle('hidden');
                })
            };
            
            $('.select2-department').on('select2:select', async function (e) {
                var date = null;
                if(!data_operasional) date = $('.operational_date').val();
                else date = moment($('.operational_date').val()).format('YYYY-MM-DD');
                get_card_operational((!data_operasional), date, this.value);

                // $('#next-day').on('click', function(e) {
                //     counter_day++;
                //     $('#prev-day').removeClass('hidden');
                //     $('*[data-timetable-day]').each(function(e) {
                //         if($(this).data('timetable-day') == counter_day) $(this).removeClass('hidden');
                //         else $(this).addClass('hidden');
                //     });
                //     if(timetableDayContentChildLen-1 == counter_day) $(this).addClass('hidden');
                //     set_pagination_name_timetable(counter_day)
                // });
                // $('#prev-day').on('click', function(e) {
                //     counter_day--;
                //     $('#next-day').removeClass('hidden');
                //     $('*[data-timetable-day]').each(function(e) {
                //         if($(this).data('timetable-day') == counter_day) $(this).removeClass('hidden');
                //         else $(this).addClass('hidden');
                //     });
                //     if(counter_day == 0) $(this).addClass('hidden');
                //     set_pagination_name_timetable(counter_day)
                // });

                // set_pagination_name_timetable(counter_day)

                // $('.select2-status').select2();
                // $('.specific_date').daterangepicker({
                //     locale: { format: 'YYYY-MM-DD' },
                //     startDate: $('.operational_date').data('daterangepicker').startDate._d,
                //     endDate: $('.operational_date').data('daterangepicker').endDate._d ,
                //     minDate: $('.operational_date').data('daterangepicker').startDate._d,
                //     maxDate: $('.operational_date').data('daterangepicker').endDate._d,
                //     ranges: {
                //         'Today': [moment(), moment()],
                //         'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                //         'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                //         'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                //         'This Month': [moment().startOf('month'), moment().endOf('month')],
                //         'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                //     },
                //     alwaysShowCalendars: true,
                //     showCustomRangeLabel: false,
                //     showDropdowns: true,
                //     minYear: 2000,
                //     drops: "auto",
                //     maxYear: parseInt(moment().format('YYYY'), 10)
                // });
                
                // $("input[name*='group']").each(function(e) {
                //     var subname= this.name.split('[').pop().split(']').shift();
                //     if(subname == 'date') {
                //         var a = moment($('.operational_date').data('daterangepicker').startDate._d);
                //         var b = moment($('.operational_date').data('daterangepicker').endDate._d).add(1, 'd');
                //         $(this).parent().parent().children('.hint-text').removeClass('text-red-500');
                //         $(this).parent().parent().children('.hint-text').addClass('text-gray-300');
                //         $(this).parent().parent().children('.hint-text').html(`Range tanggal oprasional-nya: <span class="font-bold">${b.diff(a, 'days')}</span>`);
                //         $(this).on('apply.daterangepicker', function(ev, picker) {
                //             var a = moment(picker.startDate);
                //             var b = moment(picker.endDate).add(1, 'd');
                //             $(this).parent().parent().children('.hint-text').removeClass('text-red-500');
                //             $(this).parent().parent().children('.hint-text').addClass('text-gray-300');
                //             $(this).parent().parent().children('.hint-text').html(`Range tanggal oprasional-nya: <span class="font-bold">${b.diff(a, 'days')}</span>`);
                //         });

                //         $(this).on('cancel.daterangepicker', function(ev, picker) {
                //             $(this).val('');
                //         });
                //     } else if(subname == 'status') {
                //         $(this).on('change', function(e) {
                //             $(this).parent().parent().parent().parent().toggle('hidden');
                //             setTimeout(() => {
                //                 $(this).parent().parent().parent().parent().parent().toggle('hidden');
                //             }, 200);
                //         })
                //     }
                // })
            });

            


               // $('.operational_date').on('change', function(){
            //     if($('.specific_date').length) {
            //         $('.specific_date').each(function(e) {
            //             $(this).data('daterangepicker').minDate = moment($('.operational_date').data('daterangepicker').startDate._d);
            //             $(this).data('daterangepicker').maxDate = moment($('.operational_date').data('daterangepicker').endDate._d);
            //         })
            //     }
            // });π

            // $('.select2-status').select2();
            // $('.specific_date').daterangepicker({
            //     locale: { format: 'YYYY-MM-DD' },
            //     startDate: $('.operational_date').data('daterangepicker').startDate._d,
            //     endDate: $('.operational_date').data('daterangepicker').endDate._d ,
            //     minDate: $('.operational_date').data('daterangepicker').startDate._d,
            //     maxDate: $('.operational_date').data('daterangepicker').endDate._d,
            //     ranges: {
            //         'Today': [moment(), moment()],
            //         'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            //         'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            //         'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            //         'This Month': [moment().startOf('month'), moment().endOf('month')],
            //         'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            //     },
            //     alwaysShowCalendars: true,
            //     showCustomRangeLabel: false,
            //     showDropdowns: true,
            //     minYear: 2000,
            //     drops: "auto",
            //     maxYear: parseInt(moment().format('YYYY'), 10)
            // });
            // $("input[name*='group']").each(function(e) {
            //     var subname= this.name.split('[').pop().split(']').shift();
            //     if(subname == 'date') {
            //         var a = moment($('.operational_date').data('daterangepicker').startDate._d);
            //         var b = moment($('.operational_date').data('daterangepicker').endDate._d).add(1, 'd');
            //         $(this).parent().parent().children('.hint-text').removeClass('text-red-500');
            //         $(this).parent().parent().children('.hint-text').addClass('text-gray-300');
            //         $(this).parent().parent().children('.hint-text').html(`Range tanggal oprasional-nya: <span class="font-bold">${b.diff(a, 'days')}</span>`);
            //         $(this).on('apply.daterangepicker', function(ev, picker) {
            //             var a = moment(picker.startDate);
            //             var b = moment(picker.endDate).add(1, 'd');
            //             $(this).parent().parent().children('.hint-text').removeClass('text-red-500');
            //             $(this).parent().parent().children('.hint-text').addClass('text-gray-300');
            //             $(this).parent().parent().children('.hint-text').html(`Range tanggal oprasional-nya: <span class="font-bold">${b.diff(a, 'days')}</span>`);
            //         });

            //         $(this).on('cancel.daterangepicker', function(ev, picker) {
            //             $(this).val('');
            //         });
            //     } else if(subname == 'status') {
            //         $(this).on('change', function(e) {
            //             $(this).parent().parent().parent().parent().toggle('hidden');
            //             setTimeout(() => {
            //                 $(this).parent().parent().parent().parent().parent().toggle('hidden');
            //             }, 200);
            //         })
            //     }
            // })
           
            // **
            // * submit form ----->
            // *
            var resSubmit = ApiService.submit_form('.submit-operational', (_response) => { 
                if (_response.response < 200 || _response.response >= 300) {
                    // * SET NOTIFICATION MESSAGE REQUIRED ----->
                    console.log(_response)
                } else {
                    onInit( { q: $('.search-data-input').val() });
                }
            });
        }

        async function get_modal_task(id, date_task) {
            // **
            // * open modal form ----->
            // *
            var URL = (id) ? '/request-task/' + id + '/edit' : '/request-task/create';
            var res = await ApiService.get_modal(URL, { ...date_task});
            $(".select2-position").select2();
            if(!id) {
                $('.select2-position').on('select2:select', async function (e) {
                    select2_employee_with_position({position: this.value, date:$('.request-task-date').val() })
                    if ($('#selected-employee-content').is(':hidden')) {
                        $('#selected-employee-content').toggle('hidden');
                    }
                });
            }else {
                select2_employee_with_position({position: $("input[name=position]").val(), date:$('.request-task-date').val() })
            }
            if(!date_task) {
                $('.request-task-date').daterangepicker({
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
            }
            
            // **
            // * submit form ----->
            // *
            var resSubmit = ApiService.submit_form('.submit-request-task', (_response) => { 
                if (_response.response < 200 || _response.response >= 300) {
                    // * SET NOTIFICATION MESSAGE REQUIRED ----->
                } else {
                    onInit( { q: $('.search-data-input').val() });
                }
            });
        }

        // function set_pagination_name_timetable(counter_day) {
        //     let textNextDay = $('#timetable-day-content').children().eq(counter_day+1).find('.dayname').text();
        //     let textPrevDay =(counter_day > 0) ? $('#timetable-day-content').children().eq(counter_day-1).find('.dayname').text(): '';
        //     $('#next-day p').text(textNextDay);
        //     $('#prev-day p').text(textPrevDay);
        // }

        async function open_modal_confirm(e, id) {
            // **
            // * open modal confirm ----->
            // *
            var parent_container_remove_btn = $(e).parent().parent();
            parent_container_remove_btn.addClass('bg-red-50 border-red-200');
            $('.modal-close').on('click', function(e){
                parent_container_remove_btn.removeClass('bg-red-50 border-red-200');
            });
            await ApiService.get_confirm('.submit-delete-operational', '/operational/' + id, null, () => {
                onInit();
            })
        }
        async function open_modal_confirm_task(e, id) {
            // **
            // * open modal confirm ----->
            // *
            var parent_container_remove_btn = $(e).parent().parent();
            parent_container_remove_btn.addClass('bg-red-50 border-red-200');
            $('.modal-close').on('click', function(e){
                parent_container_remove_btn.removeClass('bg-red-50 border-red-200');
            });
            await ApiService.get_confirm('.submit-delete-request-task', '/request-task/' + id, null, () => {
                onInit();
            })
        }

        async function get_card_operational(isRange,date, dept_id) {
            console.log("is running")
            let _response = await (new NetworkUtils()).emitter('GET', '/get-operational-timetable-card', {isRange, dept_id, date}, {})
            hideAllHintText();
            if (_response.response < 200 || _response.response >= 300) {
                // * SHOW NOTIFICATION ----->
                if ($('#note-warning-content').is(':hidden')) {
                    if (!$('#note-content').is(':hidden')) $('#note-content').toggle('hidden');
                    if (!$('#timetable-content').is(':hidden')) $('#timetable-content').toggle('hidden');
                    $('#note-warning-content').toggle('hidden');
                }
                $('#text-error-operation').text(_response.msg.error[0])
            } else {
                $('#timetable-content').html(_response.data);
                var anElement = new AutoNumeric.multiple('.number',{decimalPlaces:0,minimumValue: 0,decimalCharacter: ',', digitGroupSeparator : ""});
                if ($('#timetable-content').is(':hidden')) {
                    if (!$('#note-warning-content').is(':hidden')) $('#note-warning-content').toggle('hidden');
                    else $('#note-content').toggle('hidden');
                    $('#timetable-content').toggle('hidden');
                }

                $('input[name="select_all_timetable"]').on('change', function(e) {
                    var isChecked = $(this).is(':checked');
                    $(this).closest('table').find('.timetable_status').prop('checked', isChecked)
                    $(this).closest('table').find('.ot-limit-content').each(function(e) {
                        if(isChecked) {
                            if ($(this).is(':hidden')) $(this).toggle('hidden');
                        } else {
                            if (!$(this).is(':hidden')) $(this).toggle('hidden');
                        }
                        
                    })
                })
                $('.timetable_status').on('change', function(e) {
                    $(this).closest('tr').find('.ot-limit-content').toggle('hidden');
                })
            }
        }
</script>
@endsection