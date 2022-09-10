@extends('layouts.app')
@section('title', 'Users')
@section('css')
<style></style>
@endsection
@section('content')
<div class="pt-8 h-full flex-1 pb-12 px-8 xs/max:p-4 xs/max:pb-8 overflow-y-auto overflow-x-hidden relative">
    <main class="flex flex-col gap-8 xs/max:gap-4">
        <hgroup class="flex flex-col gap-8 xs/max:gap-4">
            <header>
                <p class="text-3xl text-gray-900 font-semibold xs/max:text-2xl">Business location</p>
                <p class="text-base text-gray-500 font-normal xs/max:text-sm">Lorem ipsum dolor sit amet consectetur
                    adipisicing elit.
                </p>
            </header>
        </hgroup>
        <section class="border rounded-xl shadow-md w-max lg/max:w-full">
            @can('user.create')
            <header class="px-6 py-5 flex items-center gap-3">
                <button onclick="getModal()"
                    class="flex gap-2 shadow-xs rounded-lg py-2 px-3.5 text-white text-sm font-medium flex items-center bg-green-600 xs/max:text-xs xs/max:rounded">
                    <x-icon icon="plus" width=16 height=16 viewBox="20 20" />
                    New location
                </button>
            </header>
            <hr>
            @endcan
            {{-- <section class="px-6 py-5 flex items-center gap-3 border-b border-gray-200">
                <div class="flex-1 flex">
                    <select class="select2 w-full max-w-[200px]" name="role" required>
                        <option value="" selected>All role</option>
                        @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1">
                    <div className='px-6 py-5 w-full'>
                        <div
                            class="rounded-lg xs/max:rounded shadow-sm border border-gray-300 overflow-hidden flex items-center">
                            <span class="text-gray-500 ml-2 ">
                                <x-icon icon="search" width=16 height=16 viewBox="20 20" />
                            </span>
                            <input
                                class="py-1.5 px-2.5 text-sm xs/max:text-xs focus:outline-none focus:ring-0 focus:border-transparent flex-1"
                                type="text" placeholder="Search here" />
                        </div>

                    </div>
                </div>
            </section> --}}
            <div class="table-content"></div>
            {{-- <div class="flex justify-center items-center p-4 loading text-gray-500">
                <x-icon icon="loading" width=25 height=40 viewBox="20 20" />
            </div> --}}
        </section>
    </main>
</div>

<script type="application/javascript">
    window.addEventListener('DOMContentLoaded', (event) => {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        onInit();
    });

    async function onInit() {
        var res = await Utils.table('/business/location', null);
        $('.table-content').html(res);
    }

    async function getModal(idLocation) {
        var URL = (idLocation) ? '/business/location/' + idLocation + '/edit' : '/business/location/create';
        var res = await Utils.modal(URL, null);
        var resSubmit = Utils.submit('.submit-business-location', (data) => { });
    }

</script>
<script type="application/javascript">

</script>
@endsection