@extends('layouts.app')
@section('title', 'User')
@section('css')
<style></style>
@endsection
@section('content')
<div class="flex flex-col gap-6 flex-1 h-full overflow-auto bg-white px-8 pt-8 pb-12">
    <header class="flex justify-between items-start">
        <div class="flex flex-col gap-1">
            <p class="text-3xl font-medium text-gray-900">Break time</p>
            <p class="text-base font-normal text-gray-500">Here to manage the status of each break time.</p>
        </div>
        <div class="">
            <button onclick="get_modal()" class="flex items-center gap-2.5 px-4 py-2 text-gray-500 text-sm font-medium 
                flex items-center border border-gray-200 shadow-sm rounded-lg">
                <x-icon icon="plus" width=18 height=18 viewBox="20 20" />
                Add break time
            </button>
        </div>
    </header>
    <hr>
    {{-- <div class="flex justify-center">
        <div class="timepicker relative form-floating mb-3 xl:w-96" data-mdb-with-icon="false" id="input-toggle-timepicker">
          <input type="text"
            class="form-control block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
            placeholder="Select a date" data-mdb-toggle="input-toggle-timepicker" />
          <label for="floatingInput" class="text-gray-700">Select a time</label>
        </div>
      </div><div class="flex items-center justify-center">
        <div class="datepicker relative form-floating mb-3 xl:w-96" data-mdb-toggle-button="false">
          <input type="text"
            class="form-control block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
            placeholder="Select a date" data-mdb-toggle="datepicker" />
          <label for="floatingInput" class="text-gray-700">Select a date</label>
        </div>
      </div> --}}
    <x-ui.search-data placeholder="Search for break time" url="{{ route('break-time.index') }}" />
    <div class="table-content"></div>
    <x-ui.confirm-modal class="submit-delete-break-time"></x-ui.confirm-modal>
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
            var res = await ApiService.get_table('/break-time', data);
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
    
        async function get_modal(break_time_id) {
            // **
            // * open modal form ----->
            // *
            console.log(break_time_id)
            var URL = (break_time_id) ? '/break-time/' + break_time_id + '/edit' : '/break-time/create';
            var res = await ApiService.get_modal(URL, null);
            
            // **
            // * submit form ----->
            // *
            var resSubmit = ApiService.submit_form('.submit-break-time', (data) => { 
                onInit( { q: $('.search-data-input').val() });
            });
        }

        async function open_modal_confirm(break_time_id) {
        // **
        // * open modal confirm ----->
        // *
        await ApiService.get_confirm('.submit-delete-break-time', '/break-time/' + break_time_id, null, () => {
            onInit();
        })
    }
</script>
@endsection