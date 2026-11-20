@extends('layouts.app')
@section('title', 'Absensi Karyawan')
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
                <p class="text-3xl font-medium text-gray-900">Absensi Karyawan</p>
                <p class="text-base font-normal text-gray-500">Pengaturan kehadiran setiap karyawan.</p>
            </div>
            <div>
                <button onclick="get_modal()"
                    class="flex items-center gap-2.5 px-4 py-2 text-gray-500 text-sm font-medium 
                flex items-center border border-gray-200 shadow-sm rounded-lg">
                    <x-icon icon="plus" width=18 height=18 viewBox="20 20" />
                    Tambah absensi
                </button>
            </div>
        </header>
        <hr>
        <div class="flex justify-between">
            <div class="flex items-start gap-3">
                <div class="w-72">
                    {!! FormCustom::input('header-date', null, [
                        'placeholder' => 'Silahkan pilih tanggal absen',
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
                <button id="export" href="{{ route('transaction.transactionExport') }}"
                    class="flex items-center gap-2.5 px-4 py-2 text-gray-500 text-sm font-medium 
                flex items-center border border-gray-200 shadow-sm rounded-lg">
                    <x-icon icon="download" width=18 height=18 viewBox="20 20" />
                    Export
                </button>
            </div>
            <div class="flex items-center gap-2">
                <div class="relative flex items-center h-9 w-full min-w-[180px] max-w-[280px]">
                    <div
                        class="absolute left-0 flex h-full w-9 items-center justify-center text-center text-sm text-gray-500">
                        <span class="">
                            <x-icon icon="search" width="16" height="16" viewBox="20 20" />
                        </span>
                    </div>
                    <input placeholder="Cari absensi karyawan"
                        class="search-data-input focus:shadow-xs/focused(4px-primary) h-full w-full rounded-lg border border-gray-300 pl-9 pr-2.5 text-sm shadow-sm focus:border-violet-300 focus:outline-none focus:ring-0" />
                </div>
                <button id="search-button"
                    class="flex items-center gap-2.5 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-500 shadow-sm hover:bg-gray-50">
                    Cari
                </button>
            </div>
        </div>
        <div class="table-content"></div>
        <x-ui.confirm-modal class="submit-delete-transaction"></x-ui.confirm-modal>
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

    function buildTransactionRequestParams(override = {}) {
        const params = { ...dataParams, ...override };
        delete params.department_codes;
        delete params.date;

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
            if (key === 'page' || key === 'q' || key === 'page_size') return;
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

        $('#export').click(() => {
            const qs = new URLSearchParams();
            if (dataParams.start_date) qs.set('start_date', dataParams.start_date);
            if (dataParams.end_date) qs.set('end_date', dataParams.end_date);
            if (dataParams.q) qs.set('q', dataParams.q);
            (dataParams.department_codes || []).filter(Boolean).forEach((code) => {
                qs.append('department_codes[]', code);
            });

            const queryString = qs.toString();
            const url = "{{ route('transaction.transactionExport') }}" + (queryString ? '?' + queryString : '');
            window.location.href = url;
        });

        $('input[name="header-date"]').daterangepicker({
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

        $('#search-button').on('click', async function() {
            const btn = $(this);
            const originalContent = btn.html();

            btn.prop('disabled', true).html(`
                <svg class="animate-spin text-gray-500" width="18" height="18" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Mencari...
            `);

            try {
                delete dataParams.page;
                await onInit({ q: $('.search-data-input').val() });
            } catch (error) {
                console.error(error);
            } finally {
                btn.prop('disabled', false).html(originalContent);
            }
        });
    });

    async function onInit(data) {
        $('#loading-block-document').show();

        const requestParams = buildTransactionRequestParams(data);
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

        $('.table-content').html(`
            <div class="flex flex-col items-center justify-center py-12">
                <svg class="animate-spin h-8 w-8 text-violet-600 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-gray-500 font-medium">Memuat data...</p>
            </div>
        `);

        var res = await ApiService.get_table('/transaction', requestParams);
        $('.table-content').html(res);
        $('.select2-page').select2({ minimumResultsForSearch: -1 });
        $('.select2-page').on('select2:select', function (e) {
            delete dataParams.page;
            onInit({ page_size: $(this).val() });
        });

        $('.pagination-button').on('click', function() {
            onInit({ ...dataParams, page: parseInt($(this).data('pagination-page')) });
        });

        $('#loading-block-document').hide();
    }

    async function get_modal(id) {
        var URL = (id) ? '/transaction/' + id + '/edit' : '/transaction/create';
        var res = await ApiService.get_modal(URL, null);
        $('.select2-form').select2();
        select2_employee();
        $('input[name="punch_time"]').daterangepicker({
            locale: { format: 'YYYY-MM-DD HH:mm:ss', cancelLabel: 'Clear' },
            singleDatePicker: true,
            showDropdowns: true,
            timePicker: true,
            timePicker24Hour: true,
            timePickerSeconds: true,
            autoUpdateInput: false,
            minYear: 2000,
            drops: "auto",
            maxYear: parseInt(moment().format('YYYY'), 10)
        });

        $('input[name="punch_time"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD HH:mm:ss'));
        });

        $('input[name="punch_time"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        var resSubmit = await ApiService.submit_form('.submit-transaction', (data) => {
            onInit({ ...dataParams, q: $('.search-data-input').val() });
        });
    }

    async function open_modal_confirm(id) {
        await ApiService.get_confirm('.submit-delete-transaction', '/transaction/' + id, null, () => {
            onInit({ ...dataParams, q: $('.search-data-input').val() });
        });
    }
</script>
@endsection
