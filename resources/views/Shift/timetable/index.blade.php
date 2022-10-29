@extends('layouts.app')
@section('title', 'Jadwal')
@section('css')
<style></style>
@endsection
@section('content')
<div class="flex flex-col gap-6 flex-1 h-full overflow-auto bg-white px-8 pt-8 pb-12">
    <header class="flex justify-between items-start">
        <div class="flex flex-col gap-1">
            <p class="text-3xl font-medium text-gray-900">Jadwal Shift</p>
            <p class="text-base font-normal text-gray-500">Pengaturan jadwal shift masuk dan keluar.</p>
        </div>
        <div class="">
            <button onclick="get_modal()" class="flex items-center gap-2.5 px-4 py-2 text-gray-500 text-sm font-medium 
                flex items-center border border-gray-200 shadow-sm rounded-lg">
                <x-icon icon="plus" width=18 height=18 viewBox="20 20" />
                Tambah tabel waktu
            </button>
        </div>
    </header>
    <hr>
    <x-ui.search-data placeholder="Cari table waktu" url="{{ route('timetable.index') }}" />
    <div class="table-content"></div>
    <x-ui.confirm-modal class="submit-delete-timetable"></x-ui.confirm-modal>
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
            var res = await ApiService.get_table('/timetable', dataParams);
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
    
        async function get_modal(timetable_id) {
            // **
            // * open modal form ----->
            // *
            var URL = (timetable_id) ? '/timetable/' + timetable_id + '/edit' : '/timetable/create';
            var res = await ApiService.get_modal(URL, null);
            $('.select2').select2();
            select2_break_time();
            var anElement = new AutoNumeric.multiple('.number', {decimalPlaces:0, minimumValue: 0, decimalCharacter: ',', digitGroupSeparator : "."});
            var anElement2 = new AutoNumeric.multiple('.plus-minus', {vMax: 60, decimalPlaces:0, minimumValue: 0, decimalCharacter: ',', digitGroupSeparator : ""});

            $('*[data-ref-class-content]').on('click', function(e) {
                let _idContent = $(this).data('ref-class-content');
                $('*[data-ref-class-content]').each(function () {
                    let _idContent = $(this).data('ref-class-content');
                    $(this).removeClass('border-b-2 text-violet-700');
                    $('#'+_idContent).addClass('hidden');
                });

                $(this).addClass('border-b-2 text-violet-700');
                $('#'+_idContent).removeClass('hidden');
            })

            $('input[name="is_ot_rounding"]').on('change', function(e) {
                $('#overtime-rounded-content').toggle('hidden');
            })
            $('input[name="is_ot"]').on('change', function(e) {
                $('#overtime-content').toggle('hidden');
            })
            $('input[name="is_ot_rice"]').on('change', function(e) {
                $('#rice-overtime-content').toggle('hidden');
            })

            $('#add-info').on('click', function(e) {
                $('.add-info-icon').toggleClass('rotate-180');
                $('#add-info-content').toggle('hidden');
            })

            // **
            // * submit form ----->
            // *
            var resSubmit = ApiService.submit_form('.submit-timetable', (data) => { 
                onInit( { q: $('.search-data-input').val() });
            });
        }

        async function open_modal_confirm(timetable_id) {
        // **
        // * open modal confirm ----->
        // *
        await ApiService.get_confirm('.submit-delete-timetable', '/timetable/' + timetable_id, null, () => {
            onInit();
        })
    }
</script>
@endsection