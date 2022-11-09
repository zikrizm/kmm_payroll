@extends('layouts.app')
@section('title', 'Department')
@section('css')
<style></style>
@endsection
@section('content')
<div class="flex flex-col gap-6 flex-1 h-full overflow-auto bg-white px-8 pt-8 pb-12">
    <header class="flex justify-between items-start">
        <div class="flex flex-col gap-1">
            <p class="text-3xl font-medium text-gray-900">Bagian</p>
            <p class="text-base font-normal text-gray-500">Pengaturan bagian atau unit usaha didalam perusahaan.</p>
        </div>
        <div class="">
            <button onclick="get_modal()" class="flex items-center gap-2.5 px-4 py-2 text-gray-500 text-sm font-medium 
                flex items-center border border-gray-200 shadow-sm rounded-lg">
                <x-icon icon="plus" width=18 height=18 viewBox="20 20" />
                Tambah bagian
            </button>
        </div>
    </header>
    <hr>
    <x-ui.search-data placeholder="Cari bagian" url="{{ route('department.index') }}" />
    <div class="table-content"></div>
    <x-ui.confirm-modal class="submit-delete-department"></x-ui.confirm-modal>
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
            var res = await ApiService.get_table('/department', dataParams);
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
    
        async function get_modal(dept_id) {
            // **
            // * open modal form ----->
            // *
            var URL = (dept_id) ? '/department/' + dept_id + '/edit' : '/department/create';
            var res = await ApiService.get_modal(URL, null);
            $('.select2').select2();

            var anElement = new AutoNumeric.multiple('.number',{decimalPlaces:0,minimumValue: 0,decimalCharacter: ',', digitGroupSeparator : "."});
            $('input[name="sitting_money_check"]').on('change', function(e) {
                $('#sitting-money-content').toggle('hidden');
            })

            // **
            // * submit form ----->
            // *
            var resSubmit = await ApiService.submit_form('.submit-department', (data) => { 
                onInit($('.search-data-input').val());
            });
        }

    async function open_modal_confirm(dept_id) {
        // **
        // * open modal confirm ----->
        // *
        await ApiService.get_confirm('.submit-delete-department', '/department/' + dept_id, null, () => {
            onInit($('.search-data-input').val());
        })
    }
</script>
@endsection