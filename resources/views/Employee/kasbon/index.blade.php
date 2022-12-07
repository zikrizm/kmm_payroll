@extends('layouts.app')
@section('title', 'Kasbon')
@section('css')
<style></style>
@endsection
@section('content')
<div class="flex flex-col gap-6 flex-1 h-full overflow-auto bg-white px-8 pt-8 pb-12">
    <header class="flex justify-between items-start">
        <div class="flex flex-col gap-1">
            <p class="text-3xl font-medium text-gray-900">Kasbon</p>
            <p class="text-base font-normal text-gray-500">Pengaturan kasbon karyawan.</p>
        </div>
        <div class="">
            <button onclick="get_modal()" class="flex items-center gap-2.5 px-4 py-2 text-gray-500 text-sm font-medium 
                flex items-center border border-gray-200 shadow-sm rounded-lg">
                <x-icon icon="plus" width=18 height=18 viewBox="20 20" />
                Tambah kasbon
            </button>
        </div>
    </header>
    <hr>
    <div class="flex justify-between">
        <div class="w-72">
            {!! FormCustom::input('kasbon_date', null, [
            'placeholder' => 'Pilih tanggal kasbon',
            'class' => 'date_input',
            'readonly' => true,
            'prefixiconname' => 'calendar',
            ]) !!}
        </div>
        <x-ui.search-data placeholder="Cari kasbon" url="{{ route('kasbon.index') }}" />
    </div>
    <div class="table-content"></div>
    <x-ui.confirm-modal class="submit-delete-kasbon"></x-ui.confirm-modal>
</div>

<script type="application/javascript">
    let dataParams = {};

        window.addEventListener('DOMContentLoaded', (event) => {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            
            onInit( { 
                q: $('.search-data-input').val(),
                kasbon_date: { 
                    start_date: convertLocalTimezone(moment().startOf('week'), 'YYYY-MM-DD'), 
                    end_date: convertLocalTimezone(moment().endOf('week'), 'YYYY-MM-DD')
                }
             });

            $('input[name="kasbon_date"]').daterangepicker({
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
                var dateFormat = 'YYYY-MM-DD';

                $('.search-data-input').val('');
                delete dataParams.page;
                onInit({ 
                    kasbon_date: { 
                        start_date: convertLocalTimezone(start, dateFormat), 
                        end_date: convertLocalTimezone(end, dateFormat)
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
            // **
            // * Build data params table ----->
            // *
            dataParams = { ...dataParams, ...data };
        
            // **
            // * get table ----->
            // *
            var res = await ApiService.get_table('/kasbon', dataParams);
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
    
        async function get_detail_kasbon_modal(kasbon_id) {
            // **
            // * open modal form ----->
            // *
            var res = await ApiService.get_modal('kasbon/'+kasbon_id, null);
           
        }

        async function get_modal(kasbon_id) {
            // **
            // * open modal form ----->
            // *
            console.log('kasbon_id',kasbon_id)
            var URL = (kasbon_id) ? '/kasbon/' + kasbon_id + '/edit' : '/kasbon/create';
            var res = await ApiService.get_modal(URL, null);
            select2_employee();

            var anElement = new AutoNumeric.multiple('.number',{decimalPlaces:0,minimumValue: 0,decimalCharacter: ',', digitGroupSeparator : "."});
            $('input[name="date"]').daterangepicker({
                locale: { format: 'DD-MM-YYYY' },
                singleDatePicker: true,
                showDropdowns: true,
                minYear: 2000,
                drops: "auto",
                maxYear: parseInt(moment().format('YYYY'), 10)
            });

            // **
            // * submit form ----->
            // *
            var resSubmit = ApiService.submit_form('.submit-kasbon', (data) => { 
                onInit( { q: $('.search-data-input').val() });
            });
        }

        async function open_modal_confirm(kasbon_id) {
        // **
        // * open modal confirm ----->
        // *
        await ApiService.get_confirm('.submit-delete-kasbon', '/kasbon/' + kasbon_id, null, () => {
            onInit();
        })
    }
</script>
@endsection