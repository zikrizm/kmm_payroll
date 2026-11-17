@extends('layouts.app')
@section('title', 'Laporan absensi')
@section('css')
<style>
    .select2-dept-filter .select2-selection__clear {
        display: none !important;
    }
</style>
@endsection
@section('content')
<div class="flex flex-col gap-6 flex-1 h-full overflow-auto bg-white px-8 pt-8 pb-12">
    <header class="flex justify-between items-start">
        <div class="flex flex-col gap-1">
            <p class="text-3xl font-medium text-gray-900">Laporan absensi</p>
            <p class="text-base font-normal text-gray-500">Disini untuk melihat status laporan absensi.</p>
        </div>
        <div class="">
            {{-- <button onclick="get_modal()" class="flex items-center gap-2.5 px-4 py-2 text-gray-500 text-sm font-medium 
                flex items-center border border-gray-200 shadow-sm rounded-lg">
                <x-icon icon="file-text" width=18 height=18 viewBox="20 20" />
                Kartu absen
            </button> --}}
        </div>
    </header>
    <hr>
    <div class="flex justify-between">
        <div class="flex items-start gap-3">
            <div class="w-72">
                {!! FormCustom::input('attendance-report-date', null, [
                'placeholder' => 'Pilih tanggal absensi',
                'class' => 'date_input',
                'readonly' => true,
                'prefixiconname' => 'calendar',
                ]) !!}
            </div>
            <section class="flex flex-col gap-1 min-w-72 max-w-md select2-dept-filter">
                <select class="select2-dept hidden" name="department_codes[]" multiple="multiple"
                    data-placeholder="Pilih bagian">
                    @foreach ($department_bios ?? [] as $department)
                        <option value="{{ $department['dept_code'] }}"
                            @selected(in_array($department['dept_code'], $department_codes ?? [], true))>
                            {{ $department['dept_name'] }}</option>
                    @endforeach
                </select>
                <label class="font-normal text-xs text-gray-500 parent_dept hint-text">Pilih bagian (boleh lebih dari satu)</label>
            </section>
        </div>
        <x-ui.search-data placeholder="Cari absensi" url="{{ route('attendance-report.index') }}" />
    </div>
    <div class="table-content"></div>
    <x-ui.confirm-modal class="submit-delete-attendance-report"></x-ui.confirm-modal>
</div>

<script type="application/javascript">
    let dataParams = {};
    let departmentFilterPending = false;

    function selectedDepartmentCodes() {
        const val = $('.select2-dept').val();
        if (val === null || val === undefined) {
            return [];
        }
        return Array.isArray(val) ? val : [val];
    }

    function syncDepartmentSelectFromUrl() {
        const $deptSelect = $('.select2-dept');
        const deptCodesFromUrl = new URLSearchParams(window.location.search)
            .getAll('department_codes[]')
            .filter(Boolean);
        $deptSelect.val(deptCodesFromUrl.length ? deptCodesFromUrl : []).trigger('change');
        departmentFilterPending = false;
    }

    function buildAttendanceReportRequestParams(override = {}) {
        const params = { ...dataParams, ...override };
        delete params.department_codes;
        delete params.attendance_report_date;

        let codes = [];
        if (Object.prototype.hasOwnProperty.call(override, 'department_codes')) {
            codes = override.department_codes ?? [];
        } else {
            codes = selectedDepartmentCodes();
        }
        codes = (Array.isArray(codes) ? codes : [codes]).filter(Boolean);

        if (codes.length) {
            params.department_codes = codes;
        }

        return params;
    }

    function applyDepartmentFilter() {
        departmentFilterPending = false;
        delete dataParams.page;
        onInit({ department_codes: selectedDepartmentCodes() });
    }

    function commitDepartmentFilterOnUnfocus() {
        if (!departmentFilterPending) {
            return;
        }
        const $container = $('.select2-dept').next('.select2-container');
        if ($container.hasClass('select2-container--open')) {
            return;
        }
        if ($(document.activeElement).closest('.select2-dept-filter .select2-container').length) {
            return;
        }
        applyDepartmentFilter();
    }

    function buildQueryString(params) {
        const qs = new URLSearchParams();
        Object.entries(params).forEach(([key, value]) => {
            if (value === null || value === undefined || value === '') return;
            if (key === 'page' || key === 'q') return;
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

        const $deptSelect = $('.select2-dept');
        $deptSelect.select2({
            width: '100%',
            allowClear: false,
            closeOnSelect: false,
            placeholder: 'Pilih bagian',
        });
        $deptSelect.show();
        syncDepartmentSelectFromUrl();
        $deptSelect.on('change', function () {
            departmentFilterPending = true;
        });

        $deptSelect.next('.select2-container').on('focusout', function () {
            setTimeout(commitDepartmentFilterOnUnfocus, 0);
        });

        $(document).on('blur', '.select2-dept-filter .select2-search__field', function () {
            setTimeout(commitDepartmentFilterOnUnfocus, 0);
        });

        $(document).on('keydown', '.select2-dept-filter .select2-search__field', function (e) {
            if (e.key !== 'Enter' || !departmentFilterPending) {
                return;
            }
            e.preventDefault();
            applyDepartmentFilter();
        });

        var defaultStartDate = "{{ $start_date ?? '' }}";
        var defaultEndDate = "{{ $end_date ?? '' }}";

        onInit({
            q: $('.search-data-input').val(),
            start_date: convertLocalTimezone(defaultStartDate || moment().startOf('week'), 'DD-MM-YYYY'),
            end_date: convertLocalTimezone(defaultEndDate || moment().endOf('week'), 'DD-MM-YYYY'),
            department_codes: selectedDepartmentCodes(),
        });

        $('input[name="attendance-report-date"]').daterangepicker({
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

            $('.search-data-input').val('');
            delete dataParams.page;
            onInit({
                start_date: convertLocalTimezone(start, dateFormat),
                end_date: convertLocalTimezone(end, dateFormat),
                q: '',
            });
        });

        $(".search-data-input").on('keyup', debounce(function(e) {
            if(e.key == 'Shift') return 0;
            delete dataParams.page;
            onInit({ ...dataParams, q: this.value });
        }, 250));
    });

    async function onInit(data) {
        const requestParams = buildAttendanceReportRequestParams(data);
        dataParams = { ...requestParams };

        const queryString = buildQueryString({
            start_date: requestParams.start_date,
            end_date: requestParams.end_date,
            department_codes: requestParams.department_codes || [],
        });
        const newUrl = queryString
            ? `${window.location.pathname}?${queryString}`
            : window.location.pathname;
        history.replaceState(null, '', newUrl);

        var res = await ApiService.get_table('/attendance-report', requestParams);
        $('.table-content').html(res);

        $('.pagination-button').on('click', function() {
            onInit({...dataParams, page: parseInt($(this).data('pagination-page'))})
        })
    }

    async function get_modal(kasbon_id) {
        var URL = '/attendance-report/attendance-card';
        var res = await ApiService.get_modal(URL, null);

        select2_employee({},'emp_code');
        $('input[name="date"]').daterangepicker({
            locale: { format: 'DD-MM-YYYY' },
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
        });

        var resSubmit = ApiService.submit_form('.submit-attendance-report', (data) => {
            if(data.status  == 'success') {
                $('#content-attendance-card').html(data.data);
            }
        });
    }

    async function open_modal_confirm(kasbon_id) {
        await ApiService.get_confirm('.submit-delete-kasbon', '/kasbon/' + kasbon_id, null, () => {
            onInit();
        })
    }
</script>
@endsection
