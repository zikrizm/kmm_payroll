@extends('layouts.app')
@section('title', 'Group')
@section('css')
<style></style>
@endsection
@section('content')
<div class="h-full flex-1 pb-12 px-8 xs/max:p-4 xs/max:pb-8 overflow-y-auto overflow-x-hidden relative">
    <main class="flex flex-col gap-2 xs/max:gap-4">
        <header class="min-h-[80px] w-full flex justify-between items-center">
            <p class="font-semibold text-2xl text-gray-700 xs/max:text-xl">Group management</p>
            <div class="flex items-center gap-3">
                <div class="bg-white rounded-10 w-56 h-8 flex items-center relative">
                    <input type="text" placeholder="search .."
                        class="pl-3 pr-10 flex-1 bg-transparent outline-0 font-normal text-sm">
                    <button class="text-gray-500 absolute right-3">
                        <x-icon icon="search" width=16 height=16 viewBox="20 20" />
                    </button>
                </div>
                <button onclick="get_modal()"
                    class="flex items-center gap-2 shadow-xs rounded-10 h-8 px-3 text-white text-sm font-normal flex items-center bg-violet-600 xs/max:text-xs xs/max:rounded">
                    <x-icon icon="plus" width=16 height=16 viewBox="20 20" />
                    New group
                </button>
            </div>
        </header>
        <div class="table-content"></div>
    </main>
</div>
<x-modal-confirmation classSubmit="submit-delete-group"></x-modal-confirmation>
<script type="application/javascript">
    window.addEventListener('DOMContentLoaded', (event) => {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        onInit();
    });

    async function onInit() {
        // **
        // * get table ----->
        // *
        var res = await Utils.table('/groups', null);
        $('.table-content').html(res);
    }

    async function get_modal(idGroup) {
        // **
        // * open modal form ----->
        // *
        var URL = (idGroup) ? '/groups/' + idGroup + '/edit' : '/groups/create';
        var res = await Utils.modal(URL, null);
        $('.select2').select2();
        handletogglebutton((!idGroup) ? 'active': '')

        // **
        // * submit form ----->
        // *
        var resSubmit = Utils.submit('.submit-group', (data) => { 
            onInit();
        });
    }

    function open_modal_confirm(idGroup) {
        // **
        // * open modal confirm ----->
        // *
        Utils.modal_confirm('.submit-delete-group', '/groups/' + idGroup, null, () => {
            onInit();
        })
    }
</script>
@endsection