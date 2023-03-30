@extends('layouts.app')
@section('title', 'Request task')
@section('css')
<style></style>
@endsection
@section('content')
<div class="flex flex-col gap-6 flex-1 h-full overflow-auto bg-white px-8 pt-8 pb-12">
    <header class="w-full flex flex-col gap-6 justify-center">
        <div class="flex flex-col gap-1">
            <p class="text-3xl font-medium text-gray-900">Karyawan TSO <span class="text-xl">(Tidak Sesuai
                    Operational)</span></p>
            <p class="text-base font-normal text-gray-500">Daftar karyawan yang tidak sesuai managemen operational. </p>
        </div>
        <hr>
        <div class="flex justify-between gap-2.5 overflow-auto">
            <div class="flex items-center gap-2.5">
                <div class="w-72">
                    {!! FormCustom::input('date', null, [
                    'placeholder' => 'Pilih tanggal penugasan',
                    'class' => 'date_input',
                    'readonly' => true,
                    'prefixiconname' => 'calendar',
                    ]) !!}
                </div>
                <section class="flex flex-col gap-1">
                    <select class="select2-department hidden" name="">
                        <option value="" selected>Semua bagian</option>
                        @foreach ($department_bios ?? [] as $department)
                        <option value="{{ $department['id'] }}">{{ $department['dept_name'] }}</option>
                        @endforeach
                    </select>
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs parent_dept hint-text"></label>
                </section>
                <div id="approved-all-tso" class="hidden">
                    <button onclick="approvedAllTso()"
                        class="mb-1 flex items-center gap-2.5 px-4 py-[7px] text-gray-500 text-sm font-medium border border-gray-200 shadow-sm rounded-lg">
                        <x-icon icon="check" width=18 height=18 viewBox="20 20" />
                        <p class="truncate">Disetujui semua</p>
                    </button>
                </div>
                <div id="cancel-all-lb" class="hidden">
                    <button onclick="changeAllStatusLb('cancel')"
                        class="mb-1 flex items-center gap-2.5 px-4 py-[7px] text-gray-500 text-sm font-medium border border-gray-200 shadow-sm rounded-lg">
                        <x-icon icon="x" width=18 height=18 viewBox="20 20" />
                        <p class="truncate">Batalkan LB semua</p>
                    </button>
                </div>
                <div id="accept-all-lb" class="hidden">
                    <button onclick="changeAllStatusLb('accept')"
                        class="mb-1 flex items-center gap-2.5 px-4 py-[7px] text-gray-500 text-sm font-medium border border-gray-200 shadow-sm rounded-lg">
                        <x-icon icon="check" width=18 height=18 viewBox="20 20" />
                        <p class="truncate">Dapat LB semua</p>
                    </button>
                </div>
            </div>
            <x-ui.search-data placeholder="Cari kehadiran" />
        </div>
    </header>
    <div class="table-content">
        @include('task.tso.table')
    </div>
</div>

<script type="application/javascript">
    let dataParams = {};

    window.addEventListener('DOMContentLoaded', (event) => {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        get_table({
            q: $('.search-data-input').val(),
            start_date: convertLocalTimezone(moment().startOf('week'), 'DD-MM-YYYY'),
            end_date: convertLocalTimezone(moment().endOf('week'), 'DD-MM-YYYY')
        });

        $('input[name="date"]').daterangepicker({
            locale: {
                format: 'DD-MM-YYYY'
            },
            startDate: moment().startOf('week'),
            endDate: moment().endOf('week'),
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                    'month').endOf('month')]
            },
            alwaysShowCalendars: true,
            showCustomRangeLabel: false,
            showDropdowns: true,
            minYear: 2000,
            drops: "auto",
            maxYear: parseInt(moment().format('YYYY'), 10)
        }, function (start, end, label) {
            delete dataParams.page;
            get_table({
                q: $('.search-data-input').val(),
                start_date: convertLocalTimezone(start, 'DD-MM-YYYY'),
                end_date: convertLocalTimezone(end, 'DD-MM-YYYY')
            });
        });

        $('.select2-department').select2({
            minimumResultsForSearch: -1
        });
        $('.select2-department').show();
        $('.select2-department').on('select2:select', function (e) {
            delete dataParams.page;

            get_table({
                department_id: this.value
            });
        });

        $(".search-data-input").on('keyup', debounce(function (e) {
            if (e.key == 'Shift') return 0;
            delete dataParams.page;
            get_table({
                q: this.value
            });
        }, 250));

        // $('*[data-ref-class-content]').on('click', function(e) {
        //     let _idContent = $(this).data('ref-class-content');
        //     $('*[data-ref-class-content]').each(function () {
        //         let _idContent = $(this).data('ref-class-content');
        //         $(this).removeClass('border-b-2 text-violet-700 active-sub-menu');
        //         $('#'+_idContent).addClass('hidden');
        //         $('.table-tso .select-all-card').prop('checked', false);
        //         $('.table-tso .select-card').prop('checked', false);
        //         $('.table-not-given-lb .select-all-card').prop('checked', false);
        //         $('.table-not-given-lb .select-card').prop('checked', false);
        //         if (!$('#approved-all-tso').is(':hidden')) {
        //             $('#approved-all-tso').toggle();
        //         }
        //         if (!$('#cancel-all-lb').is(':hidden')) {
        //             $('#cancel-all-lb').toggle();
        //         }
        //     });

        //     $(this).addClass('border-b-2 text-violet-700 active-sub-menu');
        //     $('#'+_idContent).removeClass('hidden');
        //     get_table({});
        // })
    });

    async function get_table(data) {
        $('#loading-block-document').show();
        dataParams = {
            ...dataParams,
            ...data
        };
        console.log(dataParams);
        var _response = await ApiService.post_data('GET', "{{ route('TSO.index') }}", dataParams);
        if (_response.response < 200 || _response.response >= 300) {
            // * SHOW NOTIFICATION ----->
        } else {
            _response.data.forEach((element, i) => {
                $('#tbody-tso').append(elementHTML(element, i));
            });
        }

        console.log(_response);
        // $('.table-content').html(res);
        // $('.table-tso .select-all-card').off('change');
        // $('.table-tso .select-all-card').on('change', function (e) {
        //     var showApproveAllTSO = false,
        //         showGivenAllLB = false,
        //         showNotGivenAllLB = false;
        //     var isChecked = $(this).is(':checked');
        //     $('.table-tso input[name="for"]').each(function (e) {
        //         let value = $(this).val();
        //         if (value == 'approved-tso') showApproveAllTSO = true;
        //         if (value == 'approved-LB') showGivenAllLB = true;
        //         if (value == 'cancel-LB') showNotGivenAllLB = true;
        //     });
        //     $('.table-tso .select-card').prop('checked', isChecked);
        //     if ($('.table-tso .select-card:checked').length) {
        //         if (showApproveAllTSO) {
        //             if ($('#approved-all-tso').is(':hidden')) {
        //                 $('#approved-all-tso').toggle();
        //             } else {
        //                 if (!isChecked) $('#approved-all-tso').toggle();
        //             }
        //         }
        //         if (showGivenAllLB) {
        //             if ($('#given-all-lb').is(':hidden')) {
        //                 $('#given-all-lb').toggle();
        //             } else {
        //                 if (!isChecked) $('#given-all-lb').toggle();
        //             }
        //         }
        //         if (showNotGivenAllLB) {
        //             if ($('#cancel-all-lb').is(':hidden')) {
        //                 $('#cancel-all-lb').toggle();
        //             } else {
        //                 if (!isChecked) $('#cancel-all-lb').toggle();
        //             }
        //         }

        //     } else {
        //         if (!$('#approved-all-tso').is(':hidden')) $('#approved-all-tso').toggle()
        //         if (!$('#given-all-lb').is(':hidden')) $('#given-all-lb').toggle()
        //         if (!$('#cancel-all-lb').is(':hidden')) $('#cancel-all-lb').toggle()
        //     }
        // });
        // $('.table-tso .select-card').on('change', function (e) {
        //     let value = $(this).closest('td').find('input[name="for"]').val();
        //     var showApproveAllTSO = false,
        //         showGivenAllLB = false,
        //         showNotGivenAllLB = false;
        //     if (value == 'approved-tso') showApproveAllTSO = true;
        //     if (value == 'approved-LB') showGivenAllLB = true;
        //     if (value == 'cancel-LB') showNotGivenAllLB = true;
        //     if ($('.table-tso .select-card:checked').length) {
        //         if (showApproveAllTSO && $('#approved-all-tso').is(':hidden')) {
        //             $('#approved-all-tso').toggle();
        //         }
        //         if (showGivenAllLB && $('#given-all-lb').is(':hidden')) {
        //             $('#given-all-lb').toggle();
        //         }
        //         if (showNotGivenAllLB && $('#cancel-all-lb').is(':hidden')) {
        //             $('#cancel-all-lb').toggle();
        //         }

        //     } else {
        //         $('.table-tso .select-all-card').prop('checked', false)
        //         if (showApproveAllTSO) $('#approved-all-tso').toggle();
        //         if (showGivenAllLB) $('#given-all-lb').toggle();
        //         if (showNotGivenAllLB) $('#cancel-all-lb').toggle();
        //     }
        // });
        $('#loading-block-document').hide();
        $('.select2-page').select2({
            minimumResultsForSearch: -1
        });
        $('.select2-page').on('select2:select', function (e) {
            delete dataParams.page;

            get_table({
                page_size: $(this).val()
            })
        });

        // **
        // * pagination table ----->
        // *
        $('.pagination-button').on('click', function () {
            get_table({
                ...dataParams,
                page: parseInt($(this).data('pagination-page'))
            })
        })

    }

    var selectedListAttendanceTso = [];
    var selectedListAttendanceLb = [];

    function selectAllAttendance(event) {
        if ($(event).is(':checked')) {
            $('[data-checkbox-tso-item]').prop('checked', true);
            // buildSelectedAttendance();
            // if (selectedListAttendanceTso.length) {
            //     if ($('#approved-all-tso').is(':hidden')) {
            //         $('#approved-all-tso').toggle();
            //     }
            // }
            // if (selectedListAttendanceLb.length && selectedListAttendanceLb.findIndex((e) => e.lb_status == 'accept') !=
            //     -1) {
            //     if ($('#accept-all-lb').is(':hidden')) {
            //         $('#accept-all-lb').toggle();
            //     }
            // }
            // if (selectedListAttendanceLb.length && selectedListAttendanceLb.findIndex((e) => e.lb_status == 'cancel') !=
            //     -1) {
            //     if ($('#cancel-all-lb').is(':hidden')) {
            //         $('#cancel-all-lb').toggle();
            //     }
            // }
        } else {
            selectedListAttendanceTso = [];
            selectedListAttendanceLb = [];
            $('[data-checkbox-tso-item]').prop('checked', false);
        }
    }

    function selectAttendance(event) {}

    function buildSelectedAttendance() {
        $('[data-tso-item]').each(function (e) {
            var action = $(this).find('input[name="action"]').val();
            var emp_id = $(this).find('input[name="emp_id"]').val();
            var dept_id = $(this).find('input[name="dept_id"]').val();
            var date = $(this).find('input[name="date"]').val();
            var first_punch = $(this).find('input[name="first_punch"]').val();
            var last_punch = $(this).find('input[name="last_punch"]').val();
            var operational_id = $(this).find('input[name="operational_id"]').val();
            var timetable_id = $(this).find('input[name="timetable_id"]').val();
            selectedListAttendanceTso = [];
            selectedListAttendanceLb = [];

            if ($(this).find('[data-checkbox-tso-item]').is(':checked')) {
                if (action == 'approved-tso') {
                    selectedListAttendanceTso.push({
                        emp_id: emp_id,
                        dept_id: dept_id,
                        tso_date: date,
                        first_punch: first_punch,
                        last_punch: last_punch,
                        operational_id: operational_id,
                        note: operational_note,
                        timetable_id: timetable_id,
                    });
                } else {
                    selectedListAttendanceLb.push({
                        emp_id: emp_id,
                        dept_id: dept_id,
                        dept_id: dept_id,
                        lb_date: date,
                        lb_status: (action == 'accept-lb') ? 'accept' : 'cancel',
                        first_punch: first_punch,
                        last_punch: last_punch,
                        operational_id: operational_id,
                        note: operational_note,
                        timetable_id: timetable_id,
                    });
                }
            } else {
                //
            }
        });
    }

    function changeAllStatusLb(type) {
        if (selectedListAttendanceLb.length) {
            var tempListLb = selectedListAttendanceLb.filter((e) => {
                e.lb_status == 'type'
            });
        }
    }

    // function build_dropdown_action(table_class, element_build) {
    //     $(`.${table_class} .select_all_card`).off('change');
    //     $(`.${table_class} .select_all_card`).on('change', function (e) {
    //         var isChecked = $(this).is(':checked');
    //         $('.select_card').prop('checked', isChecked);
    //     });

    //     $(`.${table_class} tr`).contextmenu(function (e) {
    //         e.preventDefault();
    //         let checkbox_element = $(this).find('.select_card');
    //         if (checkbox_element.length) {
    //             $('#dropdown-action').css({
    //                 display: 'flex',
    //                 top: e.pageY,
    //                 left: e.pageX
    //             });
    //             let htmlElement = element_build(checkbox_element, $('.select_card:checked').length);
    //             $('#dropdown-action').html(htmlElement)
    //             $(document).off('click');
    //             $(document).on("click", function (e) {
    //                 if ($(e.target).closest('#dropdown-action').length) return;
    //                 close_dropdown_action();
    //             });
    //         }
    //     });

    // }


    // async function get_modal_approve_tso(data_tso) {
    //     // **
    //     // * open modal form ----->
    //     // *
    //     if (data_tso.status && data_tso.emp_id && data_tso.tso_date && data_tso.dept_id) {
    //         var res = await ApiService.get_modal('/approved-tso', {
    //             ...data_tso
    //         });
    //         $(".select2-modal").select2();
    //         if (data_tso.status.slug == 'not-allowed' && data_tso.status.for == 'TSO') {} else {
    //             $('input[name="tso_datas[0][dept_id]"]').val(data_tso.dept_id);
    //         }
    //         $('input[name="tso_datas[0][emp_id]"]').val(data_tso.emp_id);
    //         $('input[name="tso_datas[0][tso_date]"]').val(data_tso.tso_date);

    //         // **
    //         // * submit form ----->
    //         // *
    //         var resSubmit = ApiService.submit_form('.submit-approve-tso', (_response) => {
    //             if (_response.response < 200 || _response.response >= 300) {
    //                 // * SET NOTIFICATION MESSAGE REQUIRED ----->
    //             } else {
    //                 get_table({
    //                     q: $('.search-data-input').val()
    //                 });
    //             }
    //         });
    //     }
    // }

    // async function get_modal_change_status_given_lb(data) {
    //     // **
    //     // * open modal form ----->
    //     // *
    //     if (data.type && data.emp_id && data.date && data.dept_id) {
    //         var res = await ApiService.get_modal('/change-status-given-lb', data);
    //         $('input[name="lb_datas[0][dept_id]"]').val(data.dept_id);
    //         $('input[name="lb_datas[0][emp_id]"]').val(data.emp_id);
    //         $('input[name="lb_datas[0][lb_date]"]').val(data.date);

    //         // **
    //         // * submit form ----->
    //         // *
    //         var resSubmit = ApiService.submit_form('.submit-not-given-lb', (_response) => {
    //             console.log(_response)
    //             if (_response.response < 200 || _response.response >= 300) {
    //                 // * SET NOTIFICATION MESSAGE REQUIRED ----->
    //             } else {
    //                 get_table({
    //                     q: $('.search-data-input').val()
    //                 });
    //             }
    //         });
    //     }
    // }

    // async function approved_all_tso() {
    //     let datas = [];
    //     $('.table-tso .select-card:checked').each(function (e) {
    //         let tr = $(this).closest('tr');
    //         let statusFor = tr.find('input[name="for"]').val();
    //         let emp_id = tr.find('input[name="emp_id"]').val();
    //         let tso_date = tr.find('input[name="date"]').val();
    //         let dept_id = tr.find('input[name="dept_id"]').val();
    //         if (statusFor == 'approved-tso') {
    //             datas.push({
    //                 emp_id,
    //                 tso_date,
    //                 dept_id
    //             })
    //         }
    //     })

    //     // var _response = await ApiService.store("", {
    //     //     tso_datas: datas
    //     // });
    //     // if (_response.response < 200 || _response.response >= 300) {
    //     //     // * SHOW NOTIFICATION ----->
    //     //     handleMessage(_response);
    //     // } else {
    //     //     handleMessage(_response);
    //     //     if (!$('#approved-all-tso').is(':hidden')) $('#approved-all-tso').toggle()
    //     //     if (!$('#given-all-lb').is(':hidden')) $('#given-all-lb').toggle()
    //     //     if (!$('#cancel-all-lb').is(':hidden')) $('#cancel-all-lb').toggle()
    //     //     get_table({});
    //     // }
    // }
    // async function change_all_status_given_lb(type) {
    //     let datas = [];
    //     $('.table-tso .select-card:checked').each(function (e) {
    //         let tr = $(this).closest('tr');
    //         let statusFor = tr.find('input[name="for"]').val();
    //         let emp_id = tr.find('input[name="emp_id"]').val();
    //         let lb_date = tr.find('input[name="date"]').val();
    //         let dept_id = tr.find('input[name="dept_id"]').val();
    //         if (statusFor == 'cancel-LB' && type == 'cancel') {
    //             datas.push({
    //                 emp_id,
    //                 lb_date,
    //                 dept_id,
    //                 type
    //             })
    //         }
    //         if (statusFor == 'approved-LB' && type == 'given') {
    //             datas.push({
    //                 emp_id,
    //                 lb_date,
    //                 dept_id,
    //                 type
    //             })
    //         }
    //     })

    //     var _response = await ApiService.store("", {
    //         lb_datas: datas
    //     });
    //     if (_response.response < 200 || _response.response >= 300) {
    //         // * SHOW NOTIFICATION ----->
    //         handleMessage(_response);
    //     } else {
    //         handleMessage(_response);
    //         if (!$('#approved-all-tso').is(':hidden')) $('#approved-all-tso').toggle()
    //         if (!$('#given-all-lb').is(':hidden')) $('#given-all-lb').toggle()
    //         if (!$('#cancel-all-lb').is(':hidden')) $('#cancel-all-lb').toggle()
    //         get_table({});

    //     }
    // }

    function elementHTMLStatus(element) {
        let html = '';
        if (element.attendance_tso_id || element.operational_status == 'valid') {
            html += `<x-icon icon="check" class="text-green-600" width=12 height=12 viewBox="20 20" />`;
        } else {
            if (element.attendance_lb_status == 'accept') {
                html += '<p class="text-gray-500">LB</p>';
            }
            if (element.operational_status == 'invalid') {
                html += `<x-icon icon="x" class="text-red-500" width=12 height=12 viewBox="20 20" />`;
            }
            if (element.operational_plusm_value)
                html += `<p class="text-gray-500">${ element.operational_plusm_value}</p>`;
        }

        return html;
    }
    function elementHTMLAction(element) {
        let html = '';
        if (element.attendance_lb_id && element.attendance_lb_status == 'cancel') {
            html += `<button disabled
                class="truncate flex items-center gap-2.5 px-2 py-1 text-gray-500 text-sm font-medium flex items-center border border-gray-200 shadow-sm rounded-lg cursor-not-allowed"
                style="opacity: 0.5;">
                <x-icon icon="x" width=16 height=16 viewBox="20 20" />
                Tidak dapat LB
            </button>`;
        }  else if(element.attendance_lb_id && element.attendance_lb_status == 'cancel') {
            html += `<button disabled
                class="truncate flex items-center gap-2.5 px-2 py-1 text-gray-500 text-sm font-medium flex items-center border border-gray-200 shadow-sm rounded-lg cursor-not-allowed"
                style="opacity: 0.5;">
                <x-icon icon="check" width=16 height=16 viewBox="20 20" />
                Dapat LB
            </button>`;
        } else if(element.attendance_lb_status == 'accept') {
            html += ` <button
                class="truncate flex items-center gap-2.5 px-2 py-1 text-gray-500 text-sm font-medium flex items-center border border-gray-200 shadow-sm rounded-lg ">
                <input type="hidden" name="action" value="cancel-lb">
                <x-icon icon="x" width=16 height=16 viewBox="20 20" />
                Tidak dapat LB
            </button>`;
        } else if(element.first_punch) {
            html += `<button
                class="truncate flex items-center gap-2.5 px-2 py-1 text-gray-500 text-sm font-medium flex items-center border border-gray-200 shadow-sm rounded-lg ">
                <input type="hidden" name="action" value="accept-lb">
                <x-icon icon="check" width=16 height=16 viewBox="20 20" />
                Dapat LB
            </button>`;
        }

        if (element.attendance_tso_id) {
            html += `<button disabled
                class="truncate flex items-center gap-2.5 px-2 py-1 text-gray-500 text-sm font-medium flex items-center border border-gray-200 shadow-sm rounded-lg cursor-not-allowed"
                style="opacity: 0.5;">
                <x-icon icon="check" width=16 height=16 viewBox="20 20" />
                Disetujui
            </button>`;
        } else {
            html += `<button
                class="truncate flex items-center gap-2.5 px-2 py-1 text-gray-500 text-sm font-medium flex items-center border border-gray-200 shadow-sm rounded-lg ">
                <input type="hidden" name="action" value="approved-tso">
                <x-icon icon="check" width=16 height=16 viewBox="20 20" />
                Disetujui
            </button>`;
        }

        return html;
    }

    function elementHTML(element, i) {
        return `<tr class='hover:bg-gray-50 border-b border-gray-200' data-tso-item>
            <td class='text-left'>
                <div class="flex items-center">
                    <div class="pl-4 py-2">
                    </div>
                    <div class="flex-1 flex gap-3 items-center pl-6 pr-3 py-3">
                        <div
                            class="flex gap-3 items-center text-sm ${element.is_holiday ? 'text-red-500' :'text-gray-500'}">
                            <x-icon icon="calendar" width=18 height=18 viewBox="20 20" />
                            <p class="flex truncate items-center text-sm">
                                ${moment(element.date).format('DD-MM-YYYY')}
                            </p>
                        </div>
                        <div
                            class="border-l-2 pl-2 flex items-center gap-1 justify-around flex-1 text-sm text-gray-500">
                            <div class="flex items-center justify-center gap-2">
                                ${element.first_punch? moment(element.first_punch).local().format('HH:mm') : '-'}
                            </div>
                            ${element.last_punch ? '<p>-</p>' : ''}
                            <div class="flex items-center justify-center gap-2">
                                ${element.last_punch? moment(element.first_punch).local().format('HH:mm') : '-'}
                            </div>
                        </div>
                    </div>
                </div>
            </td>
            <td class='px-3 py-3 text-gray-500 text-sm'>
                <p class="text-gray-500 text-sm truncate">
                    ${element.employee.first_name ?? '-'} ${element.employee.last_name ?? ''}
                </p>
            </td>
            <td class='px-3 py-3 text-gray-500 text-sm'>
                <p class="text-gray-500 text-sm truncate">
                    ${element.employee.department.dept_name ?? '-'}
                </p>
            </td>
            <td class='px-3 py-3 text-gray-500 text-sm'>
                ${element.timetable?.name ?? '-'}
            </td>
            <td class='px-3 py-3 text-gray-500 text-sm min-w-[240px]'>
                ${element.operational_note}
            </td>
            <td class='px-3 py-3 text-gray-500 text-sm'>
                <div class="flex items-center justify-center gap-1">
                    ${elementHTMLStatus(element)}
                </div>
            </td>
            <td class='px-3 py-3'>
                <div class="flex justify-center items-center gap-2.5 w-full">
                    ${elementHTMLAction(element)}
                </div>
            </td>
        </tr>`;
    }

</script>
@endsection