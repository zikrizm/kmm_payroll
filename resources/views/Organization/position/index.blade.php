@extends('layouts.app')
@section('title', 'User')
@section('css')
<style></style>
@endsection
@section('content')
<div class="flex flex-col gap-6 flex-1 h-full overflow-auto bg-white px-8 pt-8 pb-12">
    <header class="flex justify-between items-start">
        <div class="flex flex-col gap-1">
            <p class="text-3xl font-medium text-gray-900">Position</p>
            <p class="text-base font-normal text-gray-500">Here to manage the status of each position.</p>
        </div>
        <div class="">
            <button onclick="get_modal()" class="flex items-center gap-2.5 px-4 py-2 text-gray-500 text-sm font-medium 
                flex items-center border border-gray-200 shadow-sm rounded-lg">
                <x-icon icon="plus" width=18 height=18 viewBox="20 20" />
                Add position
            </button>
        </div>
    </header>
    <hr>
    <x-ui.search-data placeholder="Search for position" url="{{ route('position.index') }}" />
    <div class="table-content"></div>
    <x-ui.confirm-modal class="submit-delete-position"></x-ui.confirm-modal>
</div>

<script type="application/javascript">
    window.addEventListener('DOMContentLoaded', (event) => {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    
            onInit($('.search-data-input').val());
        });
    
        async function onInit(q = '') {
            // **
            // * get table ----->
            // *
            var res = await ApiService.get_table('/position?'+(new URLSearchParams({q}).toString()), null);
            $('.table-content').html(res);

            // **
            // * pagination table ----->
            // *
            $('.pagination-button').on('click', function() {
                let pUrl = $(this).data('pagination-url');
                $('.search-data-input').val();
                get_data_table(pUrl, null);
            })
        }
    
        async function get_modal(position_id) {
            // **
            // * open modal form ----->
            // *
            var URL = (position_id) ? '/position/' + position_id + '/edit' : '/position/create';
            var res = await ApiService.get_modal(URL, null);
            $('.select2').select2();

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