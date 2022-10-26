@extends('layouts.app')
@section('title', 'Jadwal')
@section('css')
<style></style>
@endsection
@section('content')
<div class="flex flex-col gap-6 flex-1 h-full overflow-auto bg-white px-8 pt-8 pb-12">
    <header class="flex justify-between items-start">
        <div class="flex flex-col gap-1">
            <p class="text-3xl font-medium text-gray-900">Shift Bagian</p>
            <p class="text-base font-normal text-gray-500">Di sini untuk mengelola status setiap shift bagian.</p>
        </div>
        <div class="">
            <button onclick="get_modal()" class="flex items-center gap-2.5 px-4 py-2 text-gray-500 text-sm font-medium 
                flex items-center border border-gray-200 shadow-sm rounded-lg">
                <x-icon icon="plus" width=18 height=18 viewBox="20 20" />
                Tambah shift
            </button>
        </div>
    </header>
    <hr>
    <x-ui.search-data placeholder="Cari shift" url="{{ route('shift.index') }}" />
    <div class="table-content"></div>
    <x-ui.confirm-modal class="submit-delete-shift"></x-ui.confirm-modal>
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
            var res = await ApiService.get_table('/shift', dataParams);
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
    
        async function get_modal(shift_id) {
            // **
            // * open modal form ----->
            // *
            var URL = (shift_id) ? '/shift/' + shift_id + '/edit' : '/shift/create';
            var res = await ApiService.get_modal(URL, null);
            $('.select2').select2();
            select2_timetable();

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

            $('#add-overtime-shift').on('click', function(e) {
                let _valid = true;
                let _index = $("#overtime-contents").children().length;
                let _messsage = {};
                let _listTimeOvertimes = [];

                $('#overtime-contents').children().each(function () {
                    let _indexChild = $(this).index();
                    console.log('_indexChild',_indexChild)
                    _listTimeOvertimes.push({});

                    $(`input[name^="overtime[${_indexChild}]"]`).each(function () {
                        let _name = $(this).attr('name');
                        let _value = $(this).val();
                        let _specificName = _name.split('["')[1].split('"]')[0];

                        if(!_value.trim()) {
                            _valid = false;
                            $(this).val('');
                            _messsage[`overtime-${_indexChild}-${_specificName}`] = [`Field is required.`]
                            
                            return;
                        }

                        if(_specificName != 'name') {
                            _listTimeOvertimes[_indexChild][_specificName] = _value;
                        } 
                    });
                });

                _listTimeOvertimes.forEach((e, i) => {
                    if(moment(e.hrs_from, 'hh:mm') > moment(e.hrs_to, 'hh:mm') ) {
                        _valid = false;
                        _messsage[`overtime-${i}-hrs_from`] = [`Field is invalid.`];
                        _messsage[`overtime-${i}-hrs_to`] = [`Field is invalid.`];

                        return;
                    }

                    if(i != 0) {
                        if(moment(_listTimeOvertimes[i - 1].hrs_to, 'hh:mm') > moment(e.hrs_from, 'hh:mm') ) {
                            _valid = false;
                            _messsage[`overtime-${i}-hrs_from`] = [`Field is invalid.`];
                            _messsage[`overtime-${i}-hrs_to`] = [`Field is invalid.`];

                            return;
                        }
                    }
                });

                if(_valid) {
                    $('#overtime-contents').append(
                        `<div class="flex items-start gap-3">
                            <section class="flex flex-col gap-1 flex-2">
                                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">
                                    Nama aturan lembur*</label>
                                {!! FormCustom::input('overtime[${_index}]["name"]', null,
                                [ "placeholder" => 'Enter new your name aturan lembur', "hintclass" => 'overtime-${_index}-name'])
                                !!}
                            </section>
                            <div class="flex-1 flex flex-col gap-1 flex-1">
                                <label class="text-sm font-normal text-gray-500">Awal durasi*</label>
                                {!! FormCustom::input('overtime[${_index}]["hrs_from"]', null, [ "placeholder" => '-', 'type'
                                => 'time',  "hintclass" => 'overtime-${_index}-hrs_from']) !!}
                            </div>
                            <div class="flex-1 flex flex-col gap-1 flex-1">
                                <label class="text-sm font-normal text-gray-500">Akhir durasi*</label>
                                {!! FormCustom::input('overtime[${_index}]["hrs_to"]', null, [ "placeholder" => '-', 'type' =>
                                'time',  "hintclass" => 'overtime-${_index}-hrs_to']) !!}
                            </div>
                        </div>`
                    );
                    setErorrsformInputs('valid')
                } else {
                    setErorrsformInputs(_messsage);
                }
            })
            
            // **
            // * submit form ----->
            // *
            var resSubmit = ApiService.submit_form('.submit-shift', (data) => { 
                onInit( { q: $('.search-data-input').val() });
            });
        }

        async function open_modal_confirm(shift_id) {
        // **
        // * open modal confirm ----->
        // *
        await ApiService.get_confirm('.submit-delete-shift', '/shift/' + shift_id, null, () => {
            onInit();
        })
    }
</script>
@endsection