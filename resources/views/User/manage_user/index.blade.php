@extends('layouts.app')
@section('title', 'User')
@section('css')
    <style></style>
@endsection
@section('content')
    <div class="flex flex-col gap-6 flex-1 h-full overflow-auto bg-white px-8 pt-8 pb-12">
        <header class="flex justify-between items-start">
            <div class="flex flex-col gap-1">
                <p class="text-3xl font-medium text-gray-900">Pengguna</p>
                <p class="text-base font-normal text-gray-500">Di sini untuk mengelola status setiap pengguna.</p>
            </div>
            <div class="">
                <button onclick="get_modal()"
                    class="flex items-center gap-2.5 px-4 py-2 text-gray-500 text-sm font-medium 
                flex items-center border border-gray-200 shadow-sm rounded-lg">
                    <x-icon icon="plus" width=18 height=18 viewBox="20 20" />
                    Tambah pengguna
                </button>
            </div>
        </header>
        <hr>
        <x-ui.search-data placeholder="Cari user" url="{{ route('user.index') }}" />
        <div class="table-content"></div>
        <x-ui.confirm-modal class="submit-delete-user"></x-ui.confirm-modal>
    </div>

    <script type="application/javascript">
    let dataParams = {};

        window.addEventListener('DOMContentLoaded', (event) => {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    
            onInit( { q: $('.search-data-input').val() });

            var resSubmit = ApiService.submit_form('.tess-upload-csv', (data) => { 
                // onInit( { q: $('.search-data-input').val() });
            });

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
            var res = await ApiService.get_table('/user', data);
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
            var URL = (user_id) ? '/user/' + user_id + '/edit' : '/user/create';
            var res = await ApiService.get_modal(URL, null);
            
            $('.select2').select2();
            var toggle = new Toggle();
            var toggleStatus = toggle.button('status', (!user_id) ? 'active': '', {})

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
