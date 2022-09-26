@extends('layouts.app')
@section('title', 'User')
@section('css')
    <style></style>
@endsection
@section('content')
    <div class="flex flex-col gap-6 flex-1 h-full overflow-auto bg-white px-8 pt-8 pb-12">
        <header class="flex justify-between items-start">
            <div class="flex flex-col gap-1">
                <p class="text-3xl font-medium text-gray-900">Manage user</p>
                <p class="text-base font-normal text-gray-500">Here to manage the status of each users.</p>
            </div>
            <div class="">
                <button onclick="get_modal()"
                    class="flex items-center gap-2.5 px-4 py-2 text-gray-500 text-sm font-medium 
                flex items-center border border-gray-200 shadow-sm rounded-lg">
                    <x-icon icon="plus" width=18 height=18 viewBox="20 20" />
                    Add user
                </button>
            </div>
        </header>
        <hr>
        <x-ui.search-data placeholder="Search for user" url="{{ route('user.index') }}" />
        <div class="table-content"></div>
    </div>

    <script type="application/javascript">
        window.addEventListener('DOMContentLoaded', (event) => {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    
            onInit();
        });
    
        async function onInit(page, q = '') {
            // **
            // * get table ----->
            // *
            var res = await ApiService.get_table('/user?'+(new URLSearchParams({ page, q}).toString()), null);
            $('.table-content').html(res);
            // **
            // * pagination ----->
            // *
            // $( ".pagination-custom a" ).bind( "click",async function(e) {
            //     e.preventDefault();
    
            //     var _page = $(this).attr('href').split('page=')[1];
            //     onInit(_page)
            // });
        }
    
        async function get_modal(idUser) {
            // **
            // * open modal form ----->
            // *
            var URL = (idUser) ? '/user/' + idUser + '/edit' : '/user/create';
            var res = await ApiService.get_modal(URL, null);
            
            $('.select2').select2();
            var toggle = new Toggle();
            var toggleStatus = toggle.button('status', (!idUser) ? 'active': '', {})

            // **
            // * submit form ----->
            // *
            // var resSubmit = Utils.submit('.submit-user', (data) => { 
            //     onInit();
            // });
        }

    function open_modal_confirm(idUser) {
        // **
        // * open modal confirm ----->
        // *
        Utils.modal_confirm('.submit-delete-user', '/user/' + idUser, null, () => {
            onInit();
        })
    }
    </script>
@endsection
