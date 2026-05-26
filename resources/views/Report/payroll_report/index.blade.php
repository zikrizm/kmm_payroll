@extends('layouts.app')
@section('title', 'Laporan Penggajian')
@section('css')
    <style>
        .select2-payroll-dept-filter .select2-selection__clear {
            display: none !important;
        }
    </style>
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
        <div class="flex item-center justify-between gap-2.5">
            <div class="flex items-center gap-2.5">
                <div class="w-72">
                    {!! FormCustom::input('date', null, [
                        'placeholder' => 'Pilih tanggal penggajian',
                        'class' => 'date_input',
                        'readonly' => true,
                        'prefixiconname' => 'calendar',
                    ]) !!}
                </div>
                <section class="flex flex-col gap-1 min-w-72 max-w-md select2-payroll-dept-filter">
                    <select class="select2-payroll-dept hidden" name="department_codes[]" multiple="multiple"
                        data-placeholder="Pilih bagian (kosong = semua)">
                        @foreach ($department_bios ?? [] as $department)
                            <option value="{{ $department['dept_code'] }}"
                                @selected(in_array($department['dept_code'], $department_codes ?? [], true))>
                                {{ $department['dept_name'] }}</option>
                        @endforeach
                    </select>
                    <label class="font-normal text-xs text-gray-500 parent_dept hint-text">Bisa pilih lebih dari satu bagian</label>
                </section>
                <button onclick="get_modal()"
                    class="truncate flex items-center gap-2.5 px-4 h-[36px] mb-1 text-gray-500 text-sm font-medium  flex items-center border border-gray-200 shadow-sm rounded-lg">
                    <x-icon icon="dollar-sign" width=18 height=18 viewBox="20 20" />
                    <p class="truncate">Hitung penggajian</p>
                </button>
            </div>
            <div class="flex items-center gap-3">
                <x-ui.search-data placeholder="Cari penggajian" url="{{ route('payroll-report.index') }}" />
                <button type="button" onclick="refreshPayrollTable()"
                    class="flex items-center gap-2.5 text-sm px-4 py-1.5 rounded-lg border text-gray-700 ">
                    <x-icon icon="refresh-cw" width=20 height=20 viewBox="20 20" />
                </button>
            </div>
        </div>
        <div class="table-content flex-1"></div>
        <x-ui.confirm-modal class="submit-delete-payroll-report"></x-ui.confirm-modal>
    </div>

    <script type="application/javascript">
    let dataParams = {};
    let departmentFilterPending = false;

    function selectedPayrollDepartmentCodes() {
        return $('.select2-payroll-dept').val() || [];
    }

    function applyPayrollDepartmentFilter() {
        departmentFilterPending = false;
        delete dataParams.page;
        onInit({ department_codes: selectedPayrollDepartmentCodes() });
    }

    function commitPayrollDepartmentFilterOnUnfocus() {
        if (!departmentFilterPending) {
            return;
        }
        const $container = $('.select2-payroll-dept').next('.select2-container');
        if ($container.hasClass('select2-container--open')) {
            return;
        }
        if ($(document.activeElement).closest('.select2-payroll-dept-filter .select2-container').length) {
            return;
        }
        applyPayrollDepartmentFilter();
    }

    function refreshPayrollTable() {
        departmentFilterPending = false;
        delete dataParams.page;
        onInit({
            q: $('.search-data-input').val(),
            department_codes: selectedPayrollDepartmentCodes(),
        });
    }

    function buildQueryString(params) {
        const qs = new URLSearchParams();
        Object.entries(params).forEach(([key, value]) => {
            if (value === null || value === undefined || value === '') return;
            if (Array.isArray(value)) {
                value.filter(Boolean).forEach((item) => qs.append('department_codes[]', item));
                return;
            }
            qs.set(key, value);
        });
        return qs.toString();
    }

        window.addEventListener('DOMContentLoaded', (event) => {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

            const $deptSelect = $('.select2-payroll-dept');
            $deptSelect.select2({
                width: '100%',
                allowClear: false,
                placeholder: 'Pilih bagian (kosong = semua)',
            });
            $deptSelect.show();
            $deptSelect.on('change', function () {
                departmentFilterPending = true;
            });

            $deptSelect.next('.select2-container').on('focusout', function () {
                setTimeout(commitPayrollDepartmentFilterOnUnfocus, 0);
            });

            $(document).on('blur', '.select2-payroll-dept-filter .select2-search__field', function () {
                setTimeout(commitPayrollDepartmentFilterOnUnfocus, 0);
            });

            $(document).on('keydown', '.select2-payroll-dept-filter .select2-search__field', function (e) {
                if (e.key !== 'Enter' || !departmentFilterPending) {
                    return;
                }
                e.preventDefault();
                applyPayrollDepartmentFilter();
            });

            var defaultStartDate = "{{ $start_date ?? '' }}";
            var defaultEndDate = "{{ $end_date ?? '' }}";

            onInit({
                q: $('.search-data-input').val(),
                department_codes: selectedPayrollDepartmentCodes(),
                start_date: convertLocalTimezone(defaultStartDate || moment().startOf('week'), 'DD-MM-YYYY'), 
                end_date: convertLocalTimezone(defaultEndDate || moment().endOf('week'), 'DD-MM-YYYY')
            });

            $('input[name="date"]').daterangepicker({
                locale: { format: 'DD-MM-YYYY' },
                startDate: defaultStartDate ? moment(defaultStartDate, 'DD-MM-YYYY') : moment().startOf('week'),
                endDate: defaultEndDate ? moment(defaultEndDate, 'DD-MM-YYYY') : moment().endOf('week'),
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
                    end_date: convertLocalTimezone(end, dateFormat),
                    department_codes: selectedPayrollDepartmentCodes(),
                });
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
            
            const queryString = buildQueryString(dataParams);

            // Update URL tanpa refresh halaman
            const newUrl = window.location.pathname + "?" + queryString;
            history.replaceState(null, "", newUrl);

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
            var res = await ApiService.get_modal('/payroll-report/create', { 
                start_date: start_date.format('DD-MM-YYYY'),
                end_date: end_date.format('DD-MM-YYYY'),
                department_codes: selectedPayrollDepartmentCodes()
            });

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


            var resSubmit = ApiService.submit_form('.submit-calculation-payroll', (_response) => { 
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
                }
            }, { allowLoading: false, allowCloseModal: false});
        }

        async function re_calculation(id) {
            // **
            // * open modal form ----->
            // *
            let runAnimation = true,
                setTimeoutProses;
            var res = await ApiService.get_modal('/salary-archive/re-calculate', {
                salary_id: id
            });

            $('#kalkulasi').on('click', function (e) {
                $('#container-modal-calculate').removeClass('w-[440px]');
                $('#container-modal-calculate').addClass('w-[650px]');
                $('#x-icon-close').hide();
                $('#content-confirm-calculate').hide();
                $('#content-loading-calculate').show();
                let duration = 1000;
                let length = $('.prosess-cointainer').length;
                $('.prosess-cointainer').each(function (i) {
                    if (length - 1 != i) {
                        let element = $(this);
                        let index = i;
                        let nextElement = element.next();
                        let nextElementEq = $('.prosess-cointainer').eq(index + 6);

                        let text_prosses = $(this).find('.text-prosess');
                        text_prosses.prop('Counter', 0).delay(index * duration).animate({
                            Counter: 100
                        }, {
                            duration: duration,
                            easing: 'swing',
                            // step: function (now) { },
                            step: function (now) {
                                if (runAnimation) $(this).text(Math.ceil(now) + '%');
                            },
                            complete: function () {
                                if (runAnimation) {
                                    if (length - 6 > index) {
                                        setTimeoutProses = setTimeout(() => {
                                            element.toggle('flex');
                                        }, 1000);
                                    }
                                    element.find('.icon-finish-prosess').toggle();
                                    if (!element.find('.icon-on-prosess').is(':hidden'))
                                        element.find('.icon-on-prosess').toggle()
                                    if (!element.find('.icon-waiting-prosess').is(
                                        ':hidden'))
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
                        if (length - 1 != i) {
                            // $(this).show();
                            $(this).find('.icon-finish-prosess').show();
                            $(this).find('.icon-waiting-prosess').hide()
                            $(this).find('.icon-on-prosess').hide();
                            $(this).find('.text-prosess').text('Selesai');

                            if (length - 8 < i) $(this).show();
                        } else {
                            $(this).show();
                            $(this).find('.icon-waiting-prosess').hide()
                            $(this).find('.icon-on-prosess').show();
                            $(this).find('.text-prosess').prop('Counter', 0).animate({
                                Counter: 100
                            }, {
                                duration: 1000,
                                easing: 'swing',
                                step: function (now) {
                                    $(this).text(Math.ceil(now) + '%');
                                },
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

                    onInit({
                        q: $('.search-data-input').val()
                    });
                }
            }, {
                allowLoading: false,
                allowCloseModal: false
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
