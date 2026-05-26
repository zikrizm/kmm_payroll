@extends('layouts.app')
@section('title', 'Kartu absensi')
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
                <p class="text-3xl font-medium text-gray-900">Kartu absensi</p>
                <p class="text-base font-normal text-gray-500">Daftar kartu absensi karyawan.</p>
            </div>
            <div class="">

            </div>
        </header>
        <hr>
        <div class="flex item-center justify-between">
            <div class="flex items-start gap-3">
                <div class="w-72">
                    {!! FormCustom::input('date', null, [
                        'placeholder' => 'Pilih tanggal absensi',
                        'class' => 'date_input',
                        'readonly' => true,
                        'prefixiconname' => 'calendar',
                    ]) !!}
                </div>
                <section class="flex flex-col gap-1 min-w-72 max-w-md select2-dept-filter">
                    <select class="select2-dept hidden" name="department_codes[]" multiple="multiple"
                        data-placeholder="Pilih bagian (kosong = semua)">
                        @foreach ($department_bios ?? [] as $department)
                            <option value="{{ $department['dept_code'] }}"
                                @selected(in_array($department['dept_code'], $department_codes ?? [], true))>
                                {{ $department['dept_name'] }}</option>
                        @endforeach
                    </select>
                    <label class="font-normal text-xs text-gray-500 parent_dept hint-text">Bisa pilih lebih dari satu bagian</label>
                </section>
                <a id="card-attendance" target="_blank"
                    class="flex items-center gap-2.5 text-sm px-4 py-1.5 rounded-lg border text-gray-700 ">
                    <x-icon icon="printer" width=20 height=20 viewBox="20 20" />
                    Cetak kartu absen
                </a>
            </div>
            <div class="flex items-center gap-3">
                <x-ui.search-data placeholder="Cari absensi" url="{{ route('attendance-report.index') }}" />
                <button type="button" onclick="refreshAttendanceTable()"
                    class="flex items-center gap-2.5 text-sm px-4 py-1.5 rounded-lg border text-gray-700 ">
                    <x-icon icon="refresh-cw" width=20 height=20 viewBox="20 20" />
                </button>
            </div>
        </div>
        <div class="table-content"></div>
        <x-ui.confirm-modal class="submit-delete-attendance-report"></x-ui.confirm-modal>
    </div>

    <script type="application/javascript">
    let dataParams = {};
    let departmentFilterPending = false;

    function selectedDepartmentCodes() {
        return $('.select2-dept').val() || [];
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

    function refreshAttendanceTable() {
        departmentFilterPending = false;
        delete dataParams.page;
        onInit({
            q: $('.search-data-input').val(),
            department_codes: selectedDepartmentCodes(),
        });
    }

    window.addEventListener('DOMContentLoaded', (event) => {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });    

        $('.select2-dept').select2({
            width: '100%',
            allowClear: false,
            placeholder: 'Pilih bagian (kosong = semua)',
        });
        $('.select2-dept').show();
        const $deptSelect = $('.select2-dept');
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
            department_codes: selectedDepartmentCodes(),
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
                start_date: convertLocalTimezone(start, dateFormat),
                end_date: convertLocalTimezone(end, dateFormat),
                department_codes: selectedDepartmentCodes(),
            });
        });


        $(".search-data-input").on('keyup', debounce(function(e) {
            if(e.key == 'Shift') return 0;
            delete dataParams.page;
            onInit( {...dataParams, q: this.value });
        }, 250));
    });
    
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

    async function onInit(data) {
        $('#loading-block-document').show();

        // **
        // * Build data params table ----->
        // *
        dataParams = { ...dataParams, ...data };
        $('#card-attendance').attr('href',
            '/print/card-attendance?' + buildQueryString({
                department_codes: dataParams.department_codes || [],
                start_date: dataParams.start_date,
                end_date: dataParams.end_date,
            })
        );

        const queryString = buildQueryString(dataParams);

        // Update URL tanpa refresh halaman
        const newUrl = window.location.pathname + "?" + queryString;
        history.replaceState(null, "", newUrl);
    
        // **
        // * get table ----->
        // *
        var res = await ApiService.get_table('/attendance-card', dataParams);
        $('.table-content').html(res);

        $('#loading-block-document').hide();
        // **
        // * pagination table ----->
        // *
        $('.pagination-button').on('click', function() {
            onInit({...dataParams, page: parseInt($(this).data('pagination-page'))})
        })
    }
</script>
@endsection
