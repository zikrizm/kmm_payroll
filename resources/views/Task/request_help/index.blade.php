@extends('layouts.app')
@section('title', 'Request help')
@section('css')
    <style></style>
@endsection
@section('content')
    <div class="flex flex-col gap-6 flex-1 h-full overflow-auto bg-white px-8 pt-8 pb-12">
        <header class="flex justify-between items-start">
            <div class="flex flex-col gap-1">
                <p class="text-3xl font-medium text-gray-900">Tenaga Tambahan</p>
                <p class="text-base font-normal text-gray-500">Disini untuk memanggil karyawan untuk membantu bagian lain.
                </p>
            </div>
            <div class="">
                <button onclick="get_modal()"
                    class="flex items-center gap-2.5 px-4 py-2 text-gray-500 text-sm font-medium 
                flex items-center border border-gray-200 shadow-sm rounded-lg">
                    <x-icon icon="plus" width=18 height=18 viewBox="20 20" />
                    Tambah tenaga tambahan
                </button>
            </div>
        </header>
        <hr>
        <div class="flex justify-between">
            <div class="w-72">
                {!! FormCustom::input('date', null, [
                    'placeholder' => 'Pilih tanggal tenaga tambahan',
                    'readonly' => true,
                    'prefixiconname' => 'calendar',
                ]) !!}
            </div>
            <x-ui.search-data placeholder="Cari tenaga karyawan tambahan" url="{{ route('request-help.index') }}" />
        </div>
        <div class="table-content"></div>
        <x-ui.confirm-modal class="submit-delete-request-help"></x-ui.confirm-modal>
    </div>

    <script type="application/javascript">
    let dataParams = {};

        window.addEventListener('DOMContentLoaded', (event) => {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            
            onInit( { 
                q: $('.search-data-input').val(),
                date: { 
                    start_date: convertLocalTimezone(moment().subtract(6, 'days')), 
                    end_date: convertLocalTimezone(moment())
                }
            });

            $('input[name="date"]').daterangepicker({
                locale: { format: 'YYYY-MM-DD' },
                startDate: moment().subtract(6, 'days'),
                endDate: moment(),
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                alwaysShowCalendars: true,
                showCustomRangeLabel: false,
                showDropdowns: true,
                minYear: 2000,
                drops: "auto",
                maxYear: parseInt(moment().format('YYYY'), 10)
            },function(start, end, label) {
                var dateFormat = 'YYYY-MM-DD';
                $('.search-data-input').val('');
                delete dataParams.page;
                onInit({ 
                    date: { 
                        start_date: convertLocalTimezone(start, dateFormat), 
                        end_date: convertLocalTimezone(end, dateFormat)
                    } 
                });
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
            var res = await ApiService.get_table('/request-help', data);
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
    
        async function get_modal(id, operational_id) {
            // **
            // * open modal form ----->
            // *
            var URL = (id) ? '/request-help/' + id + '/edit' : '/request-help/create';
            var res = await ApiService.get_modal(URL, null);

            if(operational_id) {
                $(".select2-dept").select2();
                select2_employee_off_in_dept({operational_id});
            }
            $('.request-help-date').daterangepicker({
                locale: { format: 'YYYY-MM-DD' },
                startDate: moment().subtract(6, 'days'),
                endDate: moment(),
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                alwaysShowCalendars: true,
                showCustomRangeLabel: false,
                showDropdowns: true,
                minYear: 2000,
                drops: "auto",
                maxYear: parseInt(moment().format('YYYY'), 10)
            });

            select2_operational(function(e) { 
                try {
                    var select_operational = $(e.currentTarget).find(":selected").data("operational");
                    var select_group = $(e.currentTarget).find(":selected").data("group");
                    var select_groups = select_operational.operational_groups;
                    // $(".select2-dept").html('').select2({
                    //     data: [{id: '', text: 'Silahkan pilih', selected: true, disabled: true}, ...select_groups.map((e) => {
                    //         return {id: e.dept_id, text: e.dept_name}
                    //     })]}
                    // );

                    $(".select2-employee").html('');
                    select2_employee_off_in_dept({operational_id: select_operational.id});

                    if(select_operational) {
                        if ($('#request-help-content').is(':hidden')) {
                            $('#request-help-content').toggle('hidden');
                        }
                        if($('.request-help-date').length) {
                            $('.request-help-date').data('daterangepicker').startDate = moment(select_group.start_date);
                            $('.request-help-date').data('daterangepicker').endDate = moment(select_group.end_date);
                            $('.request-help-date').data('daterangepicker').minDate = moment(select_group.start_date);
                            $('.request-help-date').data('daterangepicker').maxDate = moment(select_group.end_date);
                        }
                    }
                } catch (error) { 
                    console.log(error)
                }
            });
            
            // **
            // * submit form ----->
            // *
            ApiService.submit_form('.submit-request-help', (_response) => { 
                if (_response.response < 200 || _response.response >= 300) {
                    // * SET NOTIFICATION MESSAGE REQUIRED ----->
                } else {
                    onInit( { q: $('.search-data-input').val() });
                }
            });
        }

        async function open_modal_confirm(id) {
        // **
        // * open modal confirm ----->
        // *
        await ApiService.get_confirm('.submit-delete-request-help', '/request-help/' + id, null, () => {
            onInit();
        })
    }
</script>
@endsection
