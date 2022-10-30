@extends('layouts.app')
@section('title', 'Access-control')
@section('css')
<style></style>
@endsection
@section('content')
<div class="h-full flex-1 pb-12 px-8 xs/max:p-4 xs/max:pb-8 overflow-y-auto overflow-x-hidden relative">
    <main class="flex flex-col gap-2 xs/max:gap-4 h-full">
        <header class="min-h-[80px] w-full flex justify-between items-center">
            <p class="font-semibold text-2xl text-gray-700 xs/max:text-xl">Access control management</p>
            <div class="flex items-center gap-3">
                <div class="bg-white rounded-10 w-56 h-8 flex items-center relative">
                    <input type="text" placeholder="search .."
                        class="search-input pl-3 pr-10 flex-1 bg-transparent outline-0 font-normal text-sm">
                    <button class="text-gray-500 absolute right-3">
                        <x-icon icon="search" width=16 height=16 viewBox="20 20" />
                    </button>
                </div>
                <button onclick="get_modal()"
                    class="flex items-center gap-2 shadow-xs rounded-10 h-8 px-3 text-white text-sm font-normal flex items-center bg-violet-600 xs/max:text-xs xs/max:rounded">
                    <x-icon icon="plus" width=16 height=16 viewBox="20 20" />
                    New role
                </button>
            </div>
        </header>
        <div class="table-content flex-1 flex flex-col"></div>
    </main>
</div>
<x-modal-confirmation classSubmit='submit-delete-role'></x-modal-confirmation>

<script type="application/javascript">
    window.addEventListener('DOMContentLoaded', (event) => {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        
        
        $(".search-input").on('keyup', debounce(function() {
            onInit(null, $(this).val());
        }, 250));

        onInit()
    });

    async function onInit(page, q = '') {
         // **
        // * get table ----->
        // *
        var res = await utils.table('/access-controls?'+(new URLSearchParams({ page, q}).toString()), null);
        $('.table-content').html(res);

        // **
        // * pagination ----->
        // *
        $( ".pagination-custom a" ).bind( "click",async function(e) {
            e.preventDefault();

            var _page = $(this).attr('href').split('page=')[1];
            onInit(_page)
        });
    }

    async function get_modal(idRole) {
        // **
        // * open modal form ----->
        // *
        var URL = (idRole) ? '/access-controls/' + idRole + '/edit' : '/access-controls/create';
        var res = await utils.modal(URL, null);

        // **
        // * submit form ----->
        // *
        var resSubmit = utils.submit('.submit-role', (data) => {
            onInit();
         });
    }

    async function open_modal_confirm(idRole) {
        // **
        // * open modal confirm ----->
        // *
        utils.modal_confirm('.submit-delete-role', '/access-controls/' + idRole, null, () => {
            onInit();
        })
    }
</script>
@endsection