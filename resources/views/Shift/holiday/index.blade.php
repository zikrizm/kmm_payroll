@extends('layouts.app')
@section('title', 'User')
@section('css')
<style></style>
@endsection
@section('content')
<div class="flex flex-col gap-6 flex-1 h-full overflow-auto bg-white px-8 pt-8 pb-12">
    <header class="flex justify-between items-start">
        <div class="flex flex-col gap-1">
            <p class="text-3xl font-medium text-gray-900">Libur</p>
            <p class="text-base font-normal text-gray-500">Disini untuk mengatur status setiap libur.</p>
        </div>
        <div class="">
            <button onclick="get_modal()" class="flex items-center gap-2.5 px-4 py-2 text-gray-500 text-sm font-medium 
                flex items-center border border-gray-200 shadow-sm rounded-lg">
                <x-icon icon="plus" width=18 height=18 viewBox="20 20" />
                Tambah libur
            </button>
        </div>
    </header>
    <hr>
    <x-ui.search-data placeholder="Cari libur" url="{{ route('holiday.index') }}" />
    <div class="table-content"></div>
    <x-ui.confirm-modal class="submit-delete-holiday"></x-ui.confirm-modal>
</div>

<script type="application/javascript">
    let dataParams = {};

        window.addEventListener('DOMContentLoaded', (event) => {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            
            onInit( { q: $('.search-data-input').val() });

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
            var res = await ApiService.get_table('/holiday', data);
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
    
        async function get_modal(holiday_id) {
            // **
            // * open modal form ----->
            // *
            console.log(holiday_id)
            var URL = (holiday_id) ? '/holiday/' + holiday_id + '/edit' : '/holiday/create';
            var res = await ApiService.get_modal(URL, null);
            $('input[name="holiday_date"]').daterangepicker({
                locale: { format: 'DD-MM-YYYY' },
                showDropdowns: true,
                minYear: 2000,
                drops: "auto",
                maxYear: parseInt(moment().format('YYYY'), 10)
            });

            // **
            // * submit form ----->
            // *
            var resSubmit = ApiService.submit_form('.submit-holiday', (data) => { 
                onInit( { q: $('.search-data-input').val() });
            });
        }

        async function open_modal_confirm(holiday_id) {
        // **
        // * open modal confirm ----->
        // *
        await ApiService.get_confirm('.submit-delete-holiday', '/holiday/' + holiday_id, null, () => {
            onInit();
        })
    }
</script>
@endsection