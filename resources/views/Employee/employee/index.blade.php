@extends('layouts.app')
@section('title', 'User')
@section('css')
<style></style>
@endsection
@section('content')
<div class="flex flex-col gap-6 flex-1 h-full overflow-auto bg-white px-8 pt-8 pb-12">
    <header class="flex justify-between items-start">
        <div class="flex flex-col gap-1">
            <p class="text-3xl font-medium text-gray-900">Employee</p>
            <p class="text-base font-normal text-gray-500">Here to manage the status of each employee.</p>
        </div>
        <div class="">
            <button onclick="get_modal()" class="flex items-center gap-2.5 px-4 py-2 text-gray-500 text-sm font-medium 
                flex items-center border border-gray-200 shadow-sm rounded-lg">
                <x-icon icon="plus" width=18 height=18 viewBox="20 20" />
                Add employee
            </button>
        </div>
    </header>
    <form action="http://192.168.2.20/vlRegister/" class="submit-photo" method="post" enctype="multipart/form-data">
        @csrf
        <div class="flex justify-center items-center flex-1">
            <label for="contained-button-file" class="flex items-center cursor-pointer bg-gray-50">
                <input name="user_capture" accept="image/*" id="contained-button-file"
                    class="hidden" type="file"
                    onchange="loadPic('#photo', 'photo_preview', '#remove-img')" />
                <span class='cursor-pointer flex w-32 h-32 border border-dashed p-2'>
                    <img src='@zkPhoto(files/nophoto.gif)'
                        class='object-contain h-full w-full overflow-hidden' id="photo_preview">
                </span>
            </label>
            <input type="hidden" name="employee_code" value="50">
            <input type='hidden' name='csrfmiddlewaretoken' value='kWzBZE2gqQKGWEkBw6jAl0qUW2njpHOnYeweyHg98Jq3YiPVuZJHWG6unkVjrc2W' />
            <input type="hidden" name="remark">
        </div>
        <button class="">submit</button>
    </form>
    <hr>
    <x-ui.search-data placeholder="Search for employee" url="{{ route('employee.index') }}" />
    <div class="table-content"></div>
    <x-ui.confirm-modal class="submit-delete-employee"></x-ui.confirm-modal>
</div>

<script type="application/javascript">
    window.addEventListener('DOMContentLoaded', (event) => {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        onInit($('.search-data-input').val());
        var resSubmit =  ApiService.submit_form('.submit-photo', (data) => {
        });
    });

    async function onInit(q = '') {
        // **
        // * get table ----->
        // *
        var res = await ApiService.get_table('/employee?'+(new URLSearchParams({q}).toString()), null);
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

    async function get_modal(employee_code) {
        // **
        // * open modal form ----->
        // *
        var URL = (employee_code) ? '/employee/' + employee_code + '/edit' : '/employee/create';
        var res = await ApiService.get_modal(URL, null);
        $('.select2').select2();
        $('.date_input').daterangepicker({
            locale: { format: 'YYYY-MM-DD' },
            singleDatePicker: true,
            showDropdowns: true,
            minYear: 2000,
            drops: "auto",
            maxYear: parseInt(moment().format('YYYY'), 10)
        });

        // **
        // * submit form ----->
        // *
        var resSubmit = await ApiService.submit_form('.submit-employee', (data) => {
            onInit($('.search-data-input').val());
        });
    }

    async function open_modal_confirm(employee_id) {
        // **
        // * open modal confirm ----->
        // *
        await ApiService.get_confirm('.submit-delete-employee', '/employee/' + employee_id, null, () => {
            onInit($('.search-data-input').val());
        })
    }
</script>
@endsection