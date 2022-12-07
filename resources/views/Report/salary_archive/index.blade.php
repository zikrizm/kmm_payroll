@extends('layouts.app')
@section('title', 'Salary archive')
@section('css')
    <style></style>
@endsection
@section('content')
    <div class="flex flex-col gap-6 flex-1 h-full overflow-auto bg-white px-8 pt-8 pb-12">
        <header class="flex justify-between items-start">
            <div class="flex flex-col gap-1">
                <p class="text-3xl font-medium text-gray-900">Arsip penggajjian</p>
                <p class="text-base font-normal text-gray-500">Di sini untuk melihat penggajjian yang sudah ter-kalkulasi.</p>
            </div>
        </header>
        <hr>
        <x-ui.search-data placeholder="Cari penggajian" url="{{ route('salary-archive.index') }}" />
        <div class="table-content"></div>
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
            var res = await ApiService.get_table('/salary-archive', data);
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
    
        async function get_modal(user_id) {
            // **
            // * open modal form ----->
            // *
            var res = await ApiService.get_modal('/salary-archive/create', null);

            // **
            // * submit form ----->
            // *
            var resSubmit = ApiService.submit_form('.submit-user', (data) => { 
                onInit( { q: $('.search-data-input').val() });
            });
        }

        async function open_modal_confirm(user_id) {
            // **
            // * open modal confirm ----->
            // *
            await ApiService.get_confirm('.submit-delete-user', '/user/' + user_id, null, () => {
                onInit();
            })
        }


    function resetSortTable() {
        $('.sort-table').each(function(e) {
            $(this).removeClass('active');
            $(this).children('.sort-icon').removeClass('rotate-180');
        })
    }

    function sort_data(event) {
        let sortKey = $(event).data('sort-key');
        let sortUrl = $(event).data('sort-url');
        let isActive = $(event).hasClass('active');

        // Reset sort table
        resetSortTable();
        // Build Data sort table
        let field = { q: $('.search-data-input').val(), };
        field.sort = { name: sortKey, order: (isActive) ? 'ASC': 'DESC'}
        console.log(field)
        // Get Data sort table
        onInit(field)
    }
</script>
@endsection
