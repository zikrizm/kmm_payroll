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
                <div id="approve-all-tso" class="hidden">
                    <button onclick="approve_all_tso()"
                        class="mb-1 flex items-center gap-2.5 px-4 py-[7px] text-gray-500 text-sm font-medium border border-gray-200 shadow-sm rounded-lg">
                        <x-icon icon="check" width=18 height=18 viewBox="20 20" />
                        <p class="truncate">Setujui semua</p>
                    </button>
                </div>
                <div id="cancel-approve-all-tso" class="hidden">
                    <button onclick="cancel_approve_all_tso()"
                        class="mb-1 flex items-center gap-2.5 px-4 py-[7px] text-gray-500 text-sm font-medium border border-gray-200 shadow-sm rounded-lg">
                        <x-icon icon="check" width=18 height=18 viewBox="20 20" />
                        <p class="truncate">Batal setujui semua</p>
                    </button>
                </div>
                {{-- <div id="cancel-all-lb" class="hidden">
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
                </div> --}}
            </div>
            <x-ui.search-data placeholder="Cari kehadiran" />
        </div>
    </header>
    <div class="table-content">
        @include('task.tso.table')
    </div>
</div>
{{-- <div class="flex justify-center items-center fixed inset-0 min-h-screen duration-300  main-modal"
    aria-labelledby="modal-title" role="dialog" aria-modal="true" style="z-index: 99">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-50 transition-opacity inner-modal" aria-hidden="true"></div>

    <div class="flex justify-center items-center w-full h-full z-10 relative">
        <div class="content-main-modal  bg-white max-h-[95vh] overflow-y-auto overflow-x-hidden relative rounded-lg">
            <section
                class="flex flex-col gap-8 pt-4 w-[375px] bg-white max-h-[95vh] overflow-y-auto overflow-x-hidden relative rounded-lg">
                <header class="px-4 flex flex-col gap-5 pt-4 xs/max:gap-3 relative">
                    <button
                        class="absolute top-[-5px] right-3 xs/max:top-[-6px] modal-close hover:bg-gray-100 text-red rounded p-2">
                        <x-icon icon="x" width=16 height=16 viewBox="20 20" />
                    </button>
                    <div class="flex flex-col gap-1">
                        <div class="flex items-start gap-2">
                            <div
                                class="rounded-full bg-violet-100 p-1.5 border-[4px] border-violet-50 box-border mr-2 text-violet-800">
                                <x-icon icon="activity" width=18 height=18 viewBox="20 20" />
                            </div>
                            <div>
                                <p class="text-xl font-semibold text-gray-900">Detail dari TSO</p>
                                <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                                    Informasi tentang detail absensi karyawan yang tidak sesuai.
                                </p>
                            </div>
                        </div>
                    </div>
                    <hr>
                </header>
                <div>
                    <main class="px-4 flex flex-col gap-2.5 xs/max:gap-3 mb-8">

                    </main>
                    <hr>
                    <footer class="flex justify-end items-center gap-3 p-4  pb-6">
                        <button type="reset"
                            class="modal-close shadow text-gray-500 bg-white hover:bg-gray-100 focus:ring-2 focus:ring-gray-300 rounded-lg xs/max:rounded-md border border-gray-200 text-sm xs/max:text-xs font-medium xs/max:px-4 px-6 xs/max:py-1.5 py-2 hover:text-gray-900 focus:z-10">
                            Cancel</button>
                        <button type="submit"
                            class="text-white shadow bg-violet-600 hover:bg-violet-700 focus:ring-2 focus:ring-violet-700 font-medium rounded-lg xs/max:rounded-md text-sm xs/max:text-xs inline-flex items-center xs/max:px-4 px-6 xs/max:py-1.5 py-2 text-center">Ya,
                            setujui</button>
                    </footer>
                </div>
            </section>
        </div>
    </div>
</div> --}}

<script type="application/javascript">
    let dataParams = {};
    var attendanceTSODatas = [];

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

        // $('.select2-department').select2({
        //     minimumResultsForSearch: -1
        // });
        // $('.select2-department').show();
        // $('.select2-department').on('select2:select', function (e) {
        //     delete dataParams.page;

        //     get_table({
        //         department_id: this.value
        //     });
        // });

        // $(".search-data-input").on('keyup', debounce(function (e) {
        //     if (e.key == 'Shift') return 0;
        //     delete dataParams.page;
        //     get_table({
        //         q: this.value
        //     });
        // }, 250));

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
        dataParams = {
            ...dataParams,
            ...data
        };
        $('#loading-block-document').show();
        var _response = await ApiService.post_data('GET', "{{ route('TSO.index') }}", dataParams);
        $('#loading-block-document').hide();
        if (_response.response < 200 || _response.response >= 300) {
            // * SHOW NOTIFICATION ----->
        } else {
            attendanceTSODatas = _response.data;

            $('#tbody-tso').empty();
            attendanceTSODatas.forEach((element, i) => {
                $('#tbody-tso').append(elementHTML(element, i));
            });
        }

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
        // $('.select2-page').select2({
        //     minimumResultsForSearch: -1
        // });
        // $('.select2-page').on('select2:select', function (e) {
        //     delete dataParams.page;

        //     get_table({
        //         page_size: $(this).val()
        //     })
        // });

        // // **
        // // * pagination table ----->
        // // *
        // $('.pagination-button').on('click', function () {
        //     get_table({
        //         ...dataParams,
        //         page: parseInt($(this).data('pagination-page'))
        //     })
        // })

    }

    var selectedListAttendanceTso = [];
    var selectedListAttendanceLb = [];

    function selectAllAttendance(event) {
        if ($(event).is(':checked')) {
            $('[data-checkbox-tso-item]').prop('checked', true);
            $('[data-checkbox-tso-item]').closest('[data-tso-item]').addClass('bg-gray-50');
            $('#approve-all-tso').removeClass('hidden');
        } else {
            $('#approve-all-tso').addClass('hidden');
            $('[data-checkbox-tso-item]').prop('checked', false);
            $('[data-checkbox-tso-item]').closest('[data-tso-item]').removeClass('bg-gray-50');

        }
    }

    function selectAttendance(event) {
        var isChecked = false;
        $(event).closest('[data-tso-item]').toggleClass('bg-gray-50');
        $('[data-tso-item]').each(function (e) {
            if ($(this).find('[data-checkbox-tso-item]').is(':checked')) {
                isChecked = true;
            }
        });

        if (isChecked) {
            $('#approve-all-tso').removeClass('hidden');
        } else {
            $('#approve-all-tso').addClass('hidden');
        }
    }

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
            // selectedListAttendanceTso = [];
            // selectedListAttendanceLb = [];

            // if ($(this).find('[data-checkbox-tso-item]').is(':checked')) {
            //     if (action == 'approved-tso') {
            //         selectedListAttendanceTso.push({
            //             emp_id: emp_id,
            //             dept_id: dept_id,
            //             tso_date: date,
            //             first_punch: first_punch,
            //             last_punch: last_punch,
            //             operational_id: operational_id,
            //             note: operational_note,
            //             timetable_id: timetable_id,
            //         });
            //     } else {
            //         selectedListAttendanceLb.push({
            //             emp_id: emp_id,
            //             dept_id: dept_id,
            //             dept_id: dept_id,
            //             lb_date: date,
            //             lb_status: (action == 'accept-lb') ? 'accept' : 'cancel',
            //             first_punch: first_punch,
            //             last_punch: last_punch,
            //             operational_id: operational_id,
            //             note: operational_note,
            //             timetable_id: timetable_id,
            //         });
            //     }
            // } else {
            //     //
            // }
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

    async function approve_all_tso() {
        var tso = [];
        $('[data-tso-item]').each(function (e) {
            if ($(this).find('[data-checkbox-tso-item]').is(':checked')) {
                var action = $(this).find('input[name="action"]').val();
                var emp_id = $(this).find('input[name="emp_id"]').val();
                var dept_id = $(this).find('input[name="dept_id"]').val();
                var date = $(this).find('input[name="date"]').val();
                var first_punch = $(this).find('input[name="first_punch"]').val();
                var last_punch = $(this).find('input[name="last_punch"]').val();
                var operational_id = $(this).find('input[name="operational_id"]').val();
                var operational_note = $(this).find('input[name="operational_note"]').val();
                var timetable_id = $(this).find('input[name="timetable_id"]').val();
                console.log(typeof timetable_id);
                tso.push({
                    emp_id: parseInt(emp_id),
                    dept_id: parseInt(dept_id),
                    tso_date: date,
                    first_punch: (first_punch && first_punch != 'undefined' && first_punch != 'null') ? first_punch : null,
                    last_punch: (last_punch && last_punch != 'undefined' && last_punch != 'null') ? last_punch : null,
                    timetable_id: (timetable_id && timetable_id != 'undefined' && timetable_id != 'null') ? parseInt(timetable_id) : null,
                    operational_id: (operational_id && operational_id != 'undefined' && operational_id != 'null') ? parseInt(operational_id) : null,
                    note: operational_note,
                });
            }
        });

        console.log(tso);
        $('#loading-block-document').show();
        var _response = await ApiService.post_data('POST', "{{ route('TSO.store.approve') }}", {tso: tso});
        $('#loading-block-document').hide();

        console.log(_response);
        if (_response.response < 200 || _response.response >= 300) {
            // * SHOW NOTIFICATION ----->
        } else {
            get_table({});
        }

    }

    async function approve_tso(event) {
        var tso = [];
        var node = $(event).closest('[data-tso-item]');
        var action = node.find('input[name="action"]').val();
        var emp_id = node.find('input[name="emp_id"]').val();
        var dept_id = node.find('input[name="dept_id"]').val();
        var date = node.find('input[name="date"]').val();
        var first_punch = node.find('input[name="first_punch"]').val();
        var last_punch = node.find('input[name="last_punch"]').val();
        var operational_id = node.find('input[name="operational_id"]').val();
        var operational_note = node.find('input[name="operational_note"]').val();
        var timetable_id = node.find('input[name="timetable_id"]').val();
        tso.push({
            emp_id: parseInt(emp_id),
            dept_id: parseInt(dept_id),
            tso_date: date,
            first_punch: (first_punch && first_punch != 'undefined' && first_punch != 'null') ? first_punch : null,
            last_punch: (last_punch && last_punch != 'undefined' && last_punch != 'null') ? last_punch : null,
            timetable_id: (timetable_id && timetable_id != 'undefined' && timetable_id != 'null') ? parseInt(timetable_id) : null,
            operational_id: (operational_id && operational_id != 'undefined' && operational_id != 'null') ? parseInt(operational_id) : null,
            note: operational_note,
        });

        $('#loading-block-document').show();
        var _response = await ApiService.post_data('POST', "{{ route('TSO.store.approve') }}", {tso: tso});
        $('#loading-block-document').hide();

        console.log(_response);
        if (_response.response < 200 || _response.response >= 300) {
            // * SHOW NOTIFICATION ----->
        } else {
            get_table({});
        }
    }
    async function cancel_approve_tso(id) {
        $('#loading-block-document').show();
        var _response = await ApiService.post_data('POST', "/TSO/cancel-approve/"+id, null);
        $('#loading-block-document').hide();

        console.log(_response);
        if (_response.response < 200 || _response.response >= 300) {
            // * SHOW NOTIFICATION ----->
        } else {
            get_table({});
        }
    }

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
        if (element.attendance_tso_id) {
            html += `<button onclick="cancel_approve_tso('${element.attendance_tso_id}')"
                class="truncate flex items-center gap-2.5 px-2 py-1 text-gray-500 text-sm font-medium flex items-center border border-gray-200 shadow-sm rounded-lg">
                <x-icon icon="check" width=16 height=16 viewBox="20 20" />
                Batal setujui
            </button>`;
        } else {
            html += `<button onclick="approve_tso(this)"
                class="truncate flex items-center gap-2.5 px-2 py-1 text-gray-500 text-sm font-medium flex items-center border border-gray-200 shadow-sm rounded-lg">
                <x-icon icon="check" width=16 height=16 viewBox="20 20" />
                Setujui
            </button>`;
        }

        return html;
    }

    function elementHTML(element, i) {
        return `<tr class='hover:bg-gray-50 border-b border-gray-200' data-tso-item>
            <td class='text-left'>
                <div class="flex items-center">
                    <div class="pl-4 py-2">
                        ${
                           (element.attendance_tso_id) 
                           ? '<span class="min-h-[16px] min-w-[16px] w-4 h-4 block"></span>' 
                           : ` <input type="hidden" name="emp_id" value="${element.employee.id}">
                            <input type="hidden" name="dept_id" value="${element.employee.department.id}">
                            <input type="hidden" name="date" value="${element.date}">
                            <input type="hidden" name="first_punch" value="${element.first_punch}">
                            <input type="hidden" name="last_punch" value="${element.last_punch}">
                            <input type="hidden" name="operational_id" value="${element.operational_id}">
                            <input type="hidden" name="operational_note" value="${element.operational_note}">
                            <input type="hidden" name="timetable_id" value="${element.timetable?.id}">
                            <div class="flex items-center justify-center relative">
                                <input type='checkbox' onchange="selectAttendance(this)" data-checkbox-tso-item
                                    class="min-h-[16px] min-w-[16px] w-4 h-4 opacity-0 z-10 peer cursor-pointer" />
                                <span
                                    class="absolute min-h-[16px] min-w-[16px] w-4 h-4 border border-gray-300 rounded cursor-pointer
                                    flex items-center justify-center peer-checked:border-violet-600 invisible peer-checked:visible">
                                    <x-icon icon="check" width=12 height=12 viewBox="20 20" />
                                </span>
                                <span class="absolute min-h-[16px] min-w-[16px] w-4 h-4 border border-gray-300 rounded
                                        visible peer-checked:invisible cursor-pointer">
                                </span>
                            </div>
                           `
                        }
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
                                ${element.last_punch? moment(element.last_punch).local().format('HH:mm') : '-'}
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
            <td class='px-3 py-3 text-gray-500 text-sm truncate'>
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