@extends('layouts.app')
@section('title', 'Kartu absensi')
@section('css')
    <style></style>
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
        <div class="flex justify-between">
            <div class="flex items-start gap-3">
                <div class="w-72">
                    {!! FormCustom::input('date', null, [
                        'placeholder' => 'Pilih tanggal absensi',
                        'class' => 'date_input',
                        'readonly' => true,
                        'prefixiconname' => 'calendar',
                    ]) !!}
                </div>
                <section class="flex flex-col gap-1 w-max">
                    <select class="select2-dept hidden" name="dept">
                        <option value="" disabled>Semua bagian</option>
                        @foreach ($department_bios ?? [] as $department)
                            <option value="{{ $department['dept_code'] }}" @selected($department_bios->first()['dept_code'] == $department['id'])>
                                {{ $department['dept_name'] }}</option>
                        @endforeach
                    </select>
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs parent_dept hint-text"></label>
                </section>
                <a id="card-attendance" target="_blank"
                    class="flex items-center gap-2.5 text-sm px-4 py-1.5 rounded-lg border text-gray-700 ">
                    <x-icon icon="printer" width=20 height=20 viewBox="20 20" />
                    Cetak kartu absen
                </a>
            </div>
            <x-ui.search-data placeholder="Cari absensi" url="{{ route('attendance-report.index') }}" />
        </div>
        <div class="table-content"></div>
        <x-ui.confirm-modal class="submit-delete-attendance-report"></x-ui.confirm-modal>
    </div>

    <script type="application/javascript">
    let dataParams = {};
    window.addEventListener('DOMContentLoaded', (event) => {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });           
        $('.select2-dept').select2();
        $('.select2-dept').show();
        $('.select2-dept').on('select2:select', function (e) {
            delete dataParams.page;

            onInit({department_code: $(this).val()})
        });


        onInit({
            q: $('.search-data-input').val(),
            department_code: $('.select2-dept').val(),
            start_date: convertLocalTimezone( moment().startOf('week'), 'DD-MM-YYYY'), 
            end_date: convertLocalTimezone(moment().endOf('week'), 'DD-MM-YYYY')
        });

        $('input[name="date"]').daterangepicker({
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
            var dateFormat = 'DD-MM-YYYY';

            delete dataParams.page;
            onInit({ 
                start_date: convertLocalTimezone(start, dateFormat),
                end_date: convertLocalTimezone(end, dateFormat)
            });
        });


        $(".search-data-input").on('keyup', debounce(function(e) {
            if(e.key == 'Shift') return 0;
            delete dataParams.page;
            onInit( {...dataParams, q: this.value });
        }, 250));
    });
    
    async function onInit(data) {
        // **
        // * Build data params table ----->
        // *
        dataParams = { ...dataParams, ...data };
        $('#card-attendance').attr('href', 
            `/print/card-attendance?department_code=${dataParams.department_code}&start_date=${dataParams.start_date}&end_date=${dataParams.end_date}` 
        );
    
        // **
        // * get table ----->
        // *
        var res = await ApiService.get_table('/attendance-card', dataParams);
        $('.table-content').html(res);

        // **
        // * pagination table ----->
        // *
        $('.pagination-button').on('click', function() {
            onInit({...dataParams, page: parseInt($(this).data('pagination-page'))})
        })
    }
</script>
@endsection
