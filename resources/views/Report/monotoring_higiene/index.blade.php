@extends('layouts.app')
@section('title', 'Monitoring higiene dan sanitasi karyawan')
@section('css')
    <style></style>
@endsection
@section('content')
    <div class="flex flex-col gap-6 flex-1 h-full overflow-auto bg-white px-8 pt-8 pb-12">
        <header class="flex justify-between items-start">
            <div class="flex flex-col gap-1">
                <p class="text-3xl font-medium text-gray-900">Monitoring higiene dan sanitasi karyawan</p>
                <p class="text-base font-normal text-gray-500">Lorem ipsum dolor sit amet consectetur adipisicing elit.
                    Nesciunt nobis eaque nemo iusto. Assumenda, aut!
                </p>
            </div>
        </header>
        <hr>
        <div class="flex items-center justify-between gap-2.5">
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
                        <option value="" disabled>Semua bagian</option>
                        @foreach ($department_bios ?? [] as $department)
                            <option value="{{ $department['dept_code'] }}" @selected($department['dept_code'] === $department_code)>
                                {{ $department['dept_name'] }}</option>
                        @endforeach
                    </select>
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs parent_dept hint-text"></label>
                </section>
                <div
                    class="truncate flex items-center gap-2.5 px-4 h-[36px] mb-1 text-gray-500 text-sm font-medium  flex items-center border border-gray-200 shadow-sm rounded-lg">
                    <input type="checkbox" class="accent-green-500 employee-check" id="check-all">
                    <p class="truncate">Checklist Semua</p>
                </div>
            </div>
            <a id="monitoring-report"
                class="flex items-center gap-2.5 text-sm px-4 py-1.5 rounded-lg border text-gray-700 ">
                <x-icon icon="printer" width=20 height=20 viewBox="20 20" />
                Cetak
            </a>
        </div>
        <div class="table-content"></div>
    </div>

    <script type="application/javascript">
    let dataParams = {};

    window.addEventListener('DOMContentLoaded', (event) => {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        $('.select2-department').select2();  
        $('.select2-department').show();
        $('.select2-department').on('select2:select', function (e) {
            delete dataParams.page;
            onInit({department_code: this.value});
        });

        var defaultStartDate = "{{ $start_date ?? '' }}";
        var defaultEndDate = "{{ $end_date ?? '' }}";

        onInit({
            department_code: $('.select2-department').val(),
            start_date: convertLocalTimezone(defaultStartDate || moment(), 'DD-MM-YYYY'), 
            end_date: convertLocalTimezone(defaultEndDate || moment().add(1, 'days'), 'DD-MM-YYYY'),
            check_all: 0,
        });

        $('input[name="date"]').daterangepicker({
            locale: { format: 'DD-MM-YYYY' },
            startDate: defaultStartDate ? moment(defaultStartDate, 'DD-MM-YYYY') : moment(),
            endDate: defaultEndDate ? moment(defaultEndDate, 'DD-MM-YYYY') : moment().add(1, 'days'),
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 2 Days': [moment().subtract(1, 'days'), moment()],
            },
            maxSpan: {
              days: 1
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
                end_date: convertLocalTimezone(end, dateFormat)
            });
        });

        $("#check-all").on('change', function(e) {
            onInit({ check_all: e.target.checked ? 1 : 0 });
        });
        // $(".search-data-input").on('keyup', debounce(function(e) {
        //     if(e.key == 'Shift') return 0;
        //     delete dataParams.page;
        //     onInit( { q: this.value });
        // }, 250));
    });

    async function onInit(data) {
        // **
        // * Build data params table ----->
        // *
        dataParams = { ...dataParams, ...data };
        
        const queryString = new URLSearchParams(dataParams).toString();

        // Update URL tanpa refresh halaman
        const newUrl = window.location.pathname + "?" + queryString;
        history.replaceState(null, "", newUrl);
    
        $('#monitoring-report').attr('href',
          `/print/monitoring-giniene-report?start_date=${convertLocalTimezone(dataParams.start_date, 'DD-MM-YYYY')}&end_date=${convertLocalTimezone(dataParams.end_date, 'DD-MM-YYYY')}&department_code=${dataParams.department_code}&check_all=${dataParams.check_all}`
        );
        // **
        // * get table ----->
        // *
        var res = await ApiService.get_table('/monitoring-higiene', dataParams);
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

    // async function show(id) {
    //     var res = await ApiService.get_modal('/monitoring-higiene/'+id, null);
    // }
    // function colapse(e) {
    //     console.log($(e).closest('.date-box').find('.emps-box'))
    //     $(e).closest('.date-box').find('.emps-box').toggle('hidden');
    // }
</script>
@endsection
