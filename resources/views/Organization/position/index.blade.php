@extends('layouts.app')
@section('title', 'Position')
@section('css')
    <style></style>
@endsection
@section('content')
    <div class="flex flex-col gap-6 flex-1 h-full overflow-auto bg-white px-8 pt-8 pb-12">
        <header class="flex justify-between items-start">
            <div class="flex flex-col gap-1">
                <p class="text-3xl font-medium text-gray-900">Jabatan</p>
                <p class="text-base font-normal text-gray-500">Pengaturan gaji setiap jabatan.</p>
            </div>
            <div class="">
                <button onclick="get_modal()"
                    class="flex items-center gap-2.5 px-4 py-2 text-gray-500 text-sm font-medium 
                flex items-center border border-gray-200 shadow-sm rounded-lg">
                    <x-icon icon="plus" width=18 height=18 viewBox="20 20" />
                    Tambah jabatan
                </button>
            </div>
        </header>
        <hr>
        <x-ui.search-data placeholder="Cari jabatan" url="{{ route('position.index') }}" />
        <div class="table-content"></div>
        <x-ui.confirm-modal class="submit-delete-position"></x-ui.confirm-modal>
    </div>

    <script type="application/javascript">
    let dataParams = {};

    window.addEventListener('DOMContentLoaded', (event) => {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    
            onInit($('.search-data-input').val());
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
            var res = await ApiService.get_table('/position', data);
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
    
        async function get_modal(position_id) {
            // **
            // * open modal form ----->
            // *
            var URL = (position_id) ? '/position/' + position_id + '/edit' : '/position/create';
            var res = await ApiService.get_modal(URL, null);
            $('.select2').select2();

            var anElement = new AutoNumeric.multiple('.number',{decimalPlaces:0,minimumValue: 0,decimalCharacter: ',', digitGroupSeparator : "."});
            $('input[name="extra_pay_check"]').on('change', function(e) {
                $('#extra-pay-content').toggle('hidden');
            })
            $('input[name="enable_extra_break_time"]').on('change', function(e) {
                $('#extra-break-time-content').toggle('hidden');
            })

            $('#add-info').on('click', function(e) {
                $('.add-info-icon').toggleClass('rotate-180');
                $('#add-info-content').toggle('hidden');
            });

            // **
            // * submit form ----->
            // *
            var resSubmit = await ApiService.submit_form('.submit-position', (data) => { 
                onInit($('.search-data-input').val());
            });
        }

    async function open_modal_confirm(position_id) {
        // **
        // * open modal confirm ----->
        // *
        await ApiService.get_confirm('.submit-delete-position', '/position/' + position_id, null, () => {
            onInit($('.search-data-input').val());
        })
    }
</script>
@endsection
