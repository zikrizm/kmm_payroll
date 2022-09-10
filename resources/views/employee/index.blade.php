@extends('layouts.app')
@section('title', 'Users')
@section('css')
<style></style>
@endsection
@section('content')
<div class="h-full flex-1 pb-12 px-8 xs/max:p-4 xs/max:pb-8 overflow-y-auto overflow-x-hidden relative">
    <main class="flex flex-col gap-2 xs/max:gap-4">
        <header class="min-h-[80px] w-full flex justify-between items-center">
            <p class="font-semibold text-2xl text-gray-700 xs/max:text-xl">Employee management</p>
            <div class="flex items-center gap-3">
                <div class="bg-white rounded-10 w-56 h-8 flex items-center relative">
                    <input type="text" placeholder="search .."
                        class="pl-3 pr-10 flex-1 bg-transparent outline-0 font-normal text-sm">
                    <button class="text-gray-500 absolute right-3">
                        <x-icon icon="search" width=16 height=16 viewBox="20 20" />
                    </button>
                </div>
                <button onclick="getModal()"
                    class="flex items-center gap-2 shadow-xs rounded-10 h-8 px-3 text-white text-sm font-normal flex items-center bg-violet-600 xs/max:text-xs xs/max:rounded">
                    <x-icon icon="plus" width=16 height=16 viewBox="20 20" />
                    New employee
                </button>
            </div>
        </header>
        <div class="table-content"></div>
    </main>
</div>
<x-modal-confirmation classSubmit="submit-delete-employee"></x-modal-confirmation>
<script type="application/javascript">
    window.addEventListener('DOMContentLoaded', (event) => {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        $('.select2').select2();
        onInit();
    });

    async function onInit() {
        var res = await Utils.table('/employees', null);
        $('.table-content').html(res);
        $('.select2').select2();
    }

    async function getModal(idEmployee) {
        var URL = (idEmployee) ? '/employees/' + idEmployee + '/edit' : '/employees/create';
        var res = await Utils.modal(URL, null);
        $('.select2').select2();
        onChangeBtnStatus((!idEmployee) ? 'active': '')
        var resSubmit = Utils.submit('.submit-employee', (data) => { 
            onInit();
        });
    }

    function onChangeBtnStatus(defautlValue) {
        if(defautlValue) {
            $('.btn-status').each(function(e) {
                var value = $(this).val();
                if (value.toLowerCase() == defautlValue.toLowerCase()){
                    $(this).toggleClass("bg-gray-100");
                    $('#status-employee').val($(this).val());
                }
            });
        }

        $('.btn-status').on('click', function(e) {
            $('.btn-status').each(function(e) {
                var hasClass = $(this).hasClass("bg-gray-100");
                if (hasClass)
                    $(this).removeClass('bg-gray-100')
            });
            $(this).toggleClass("bg-gray-100");
            $('#status-employee').val($(this).val());
        });
    }
    function openModalConfirm(idEmployee) {
        Utils.modal_confirm('.submit-delete-employee', '/employees/' + idEmployee, null, () => {
            onInit();
        })
    }
</script>
@endsection