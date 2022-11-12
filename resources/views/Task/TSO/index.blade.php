@extends('layouts.app')
@section('title', 'Request task')
@section('css')
<style></style>
@endsection
@section('content')
<div class="flex flex-col gap-6 flex-1 h-full overflow-auto bg-white px-8 pt-8 pb-12">
    <header class="flex justify-between items-start">
        <div class="flex flex-col gap-1">
            <p class="text-3xl font-medium text-gray-900">Karyawan TSO <span class="text-xl">(Tidak Sesuai Operational)</span></p>
            <p class="text-base font-normal text-gray-500">Daftar karyawan yang tidak sesuai managemen operational.
            </p>
        </div>
    </header>
    <hr>
    <div class="flex justify-between">
        <div class="w-72">
            {!! FormCustom::input('date', null, [
            'placeholder' => 'Pilih tanggal penugasan',
            'class' => 'date_input',
            'readonly' => true,
            'prefixiconname' => 'calendar',
            ]) !!}
        </div>
        <x-ui.search-data placeholder="Cari penugasan" url="{{ route('TSO.index') }}" />
    </div>
    <div class="table-content"></div>
</div>

<script type="application/javascript">
    let dataParams = {};

        window.addEventListener('DOMContentLoaded', (event) => {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            
            onInit({ 
                q: $('.search-data-input').val(),
                date: { 
                    start_time:convertLocalTimezone(moment().subtract(6, 'days'),'YYYY-MM-DD'), 
                    end_time: convertLocalTimezone(moment(),'YYYY-MM-DD')
                }
            });

            $('input[name="date"]').daterangepicker({
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
            },function(start, end, label) {
                var dateFormat = 'YYYY-MM-DD';
                $('.search-data-input').val('');
                delete dataParams.page;
                onInit({ 
                    date: { 
                        start_time: convertLocalTimezone(start, dateFormat), 
                        end_time: convertLocalTimezone(end, dateFormat)
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
            console.log(data)
            // **
            // * Build data params table ----->
            // *
            dataParams = { ...dataParams, ...data };
        
            // **
            // * get table ----->
            // *
            var res = await ApiService.get_table('/TSO', dataParams);
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
    
        async function get_modal(id) {
            // **
            // * open modal form ----->
            // *
            var URL = (id) ? '/request-task/' + id + '/edit' : '/request-task/create';
            var res = await ApiService.get_modal(URL, null);
            $(".select2-position").select2();
            if(!id) {
                $('.select2-position').on('select2:select', async function (e) {
                    if ($('#selected-employee-content').is(':hidden')) {
                        $('#selected-employee-content').toggle('hidden');
                    }
                });
            }
            select2_employee();
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

        async function open_modal_confirm(id) {
        // **
        // * open modal confirm ----->
        // *
        await ApiService.get_confirm('.submit-delete-request-task', '/request-task/' + id, null, () => {
            onInit();
        })
    }
</script>
@endsection