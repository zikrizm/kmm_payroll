@extends('layouts.app')
@section('title', 'Users')
@section('css')
<style></style>
@endsection
@section('content')
<div class="h-full flex-1 pb-12 px-8 xs/max:p-4 xs/max:pb-8 overflow-y-auto overflow-x-hidden relative">
    <main class="flex flex-col gap-2 xs/max:gap-4 h-full">
        <header class="min-h-[80px] w-full flex justify-between items-center">
            <p class="font-semibold text-2xl text-gray-700 xs/max:text-xl">User management</p>
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
                    New user
                </button>
            </div>
        </header>
        <div class="table-content flex flex-col bg-white rounded-lg p-3">
            <table class="table table-bordered yajra-datatable">
                <thead>
                    <tr>
                        <th>username</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </main>
</div>
<x-modal-confirmation classSubmit="submit-delete-user"> </x-modal-confirmation>
<script type="module">
    $(function () {
                $('.select2').select2();
            var table = $('.yajra-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('users.index') }}",
                columns: [
                    {data: 'username', name: 'username'},
                    // {data: 'email', name: 'email'},
                    // {data: 'username', name: 'username'},
                    // {data: 'phone', name: 'phone'},
                    // {data: 'dob', name: 'dob'},
                    // {
                    //     data: 'action', 
                    //     name: 'action', 
                    //     orderable: true, 
                    //     searchable: true
                    // },
                ]
            });
            
        });
</script>
<script type="application/javascript">
    // window.addEventListener('DOMContentLoaded', (event) => {
    //     $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    //     $('.select2').select2();

    //     // $(".search-input").on('keyup', debounce(function() {
    //     //     onInit(null, $(this).val());
    //     // }, 250));

    //     onInit()
    // });

    async function onInit(page, q = '') {
        // **
        // * get table ----->
        // *
        var res = await Utils.table('/users?'+(new URLSearchParams({ page, q}).toString()), null);
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

    async function get_modal(idUser) {
        // **
        // * open modal form ----->
        // *
        var URL = (idUser) ? '/users/' + idUser + '/edit' : '/users/create';
        var res = await Utils.modal(URL, null);
        $('.select2').select2();
        var toggle = new Toggle();
        var toggleStatus = toggle.button('status', (!idUser) ? 'active': '', {})

        // **
        // * submit form ----->
        // *
        var resSubmit = Utils.submit('.submit-user', (data) => { 
            onInit();
        });
    }

    function open_modal_confirm(idUser) {
        // **
        // * open modal confirm ----->
        // *
        Utils.modal_confirm('.submit-delete-user', '/users/' + idUser, null, () => {
            onInit();
        })
    }
</script>
@endsection