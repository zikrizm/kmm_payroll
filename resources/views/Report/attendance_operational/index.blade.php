@extends('layouts.app')
@section('title', 'Kartu absensi')
@section('css')
<style></style>
@endsection
@section('content')
<div class="flex flex-col gap-6 flex-1 h-full overflow-auto bg-white px-8 pt-8 pb-12">
    <header class="flex justify-between items-start">
        <div class="flex flex-col gap-1">
            <p class="text-3xl font-medium text-gray-900">Kartu absensi Operational</p>
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
            <section class="flex flex-col gap-1 w-72">
                <select class="select2-dept hidden" name="dept">
                    <option value="all" selected>All bagian</option>
                    @foreach ($dept_bios['data'] as $item)
                    <option value="{{ $item['id'] }}">{{ $item['dept_name'] }}</option>
                    @endforeach
                </select>
                <label class="font-normal text-xs text-red-500 xs/max:text-xs parent_dept hint-text"></label>
            </section>
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

                onInit({dept_id: $(this).val()})
            });

            onInit({
                q: $(".search-data-input").val(),
                date: { 
                    start_time: convertLocalTimezone(moment().startOf('week'), 'YYYY-MM-DD HH:mm:ss'), 
                    end_time: convertLocalTimezone(moment().endOf('week'), 'YYYY-MM-DD HH:mm:ss')
                }
            });

            $('input[name="date"]').daterangepicker({
                locale: { format: 'YYYY-MM-DD' },
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
                var dateFormat = 'YYYY-MM-DD HH:mm:ss';

                delete dataParams.page;
                onInit({ 
                    q: $(".search-data-input").val(),
                    date: { 
                        start_time: convertLocalTimezone(start, dateFormat), 
                        end_time: convertLocalTimezone(end, dateFormat)
                    } 
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
        
            // **
            // * get table ----->
            // *
            var res = await ApiService.get_table('/attendance-operational', dataParams);
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