@extends('layouts.app')
@section('title', 'Request task')
@section('css')
<style></style>
@endsection
@section('content')
<div class="flex flex-col gap-6 flex-1 h-full overflow-auto bg-white px-8 pt-8 pb-12">
    <header class="w-full flex flex-col gap-6 justify-center">
        <div class="flex flex-col gap-1">
            <p class="text-3xl font-medium text-gray-900">Karyawan TSO <span class="text-xl">(Tidak Sesuai
                    Operational)</span></p>
            <p class="text-base font-normal text-gray-500">Daftar karyawan yang tidak sesuai managemen operational. </p>
        </div>

        <ul class="flex border-b">
            <li>
                <button data-ref-class-content="tso-content"
                    class="active-sub-menu text-gray-500 text-violet-700 border-b-2 mr-4 pt px-1 pb-[16px] border-violet-700 text-sm font-medium">
                    Jam Kerja Operasional
                </button>
            </li>
            <li>
                <button data-ref-class-content="lb-content"
                    class="text-gray-500 mr-4 pt px-1 pb-[16px] border-violet-700 text-sm font-medium">
                    Libur Operasional
                </button>
            </li>
        </ul>
        <div class="flex justify-between">
            <div class="flex itemsc-center gap-2.5">
                <div class="w-72">
                    {!! FormCustom::input('date', null, [
                    'placeholder' => 'Pilih tanggal penugasan',
                    'class' => 'date_input',
                    'readonly' => true,
                    'prefixiconname' => 'calendar',
                    ]) !!}
                </div>
                <section class="flex flex-col gap-1">
                    <select class="select2-department hidden" name="">
                        <option value="" selected>Semua bagian</option>
                        @foreach ($department_bios ?? [] as $department)
                        <option value="{{ $department['id'] }}">{{ $department['dept_name'] }}</option>
                        @endforeach
                    </select>
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs parent_dept hint-text"></label>
                </section>
            </div>
            <x-ui.search-data placeholder="Cari penugasan" url="{{ route('TSO.index') }}" />
        </div>
    </header>
    <div class="table-content-tso" id="tso-content"></div>
    <div class="table-content-lb" id="lb-content"></div>
</div>

<script type="application/javascript">
    let dataParams = {};

        window.addEventListener('DOMContentLoaded', (event) => {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            
            get_table({ 
                q: $('.search-data-input').val(),
                date: { 
                    start_time:convertLocalTimezone(moment().subtract(6, 'days'),'YYYY-MM-DD'), 
                    end_time: convertLocalTimezone(moment(),'YYYY-MM-DD')
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
                delete dataParams.page;
                get_table({ 
                    q: $('.search-data-input').val(),
                    date: { start_time: convertLocalTimezone(start, 'YYYY-MM-DD'),  end_time: convertLocalTimezone(end, 'YYYY-MM-DD') } 
                });
            });
            
            $('.select2-department').select2({ minimumResultsForSearch: -1 });  
            $('.select2-department').show();
            $('.select2-department').on('select2:select', function (e) {
                delete dataParams.page;

                get_table({ department_id: this.value });
            });

            $(".search-data-input").on('keyup', debounce(function(e) {
                if(e.key == 'Shift') return 0;
                delete dataParams.page;
                get_table({ q: this.value });
            }, 250));

            $('*[data-ref-class-content]').on('click', function(e) {
                let _idContent = $(this).data('ref-class-content');
                $('*[data-ref-class-content]').each(function () {
                    let _idContent = $(this).data('ref-class-content');
                    $(this).removeClass('border-b-2 text-violet-700 active-sub-menu');
                    $('#'+_idContent).addClass('hidden');
                });

                $(this).addClass('border-b-2 text-violet-700 active-sub-menu');
                $('#'+_idContent).removeClass('hidden');
                get_table({});
            })
        });

        async function get_table(data) {
            let type = '';
            switch ($('.active-sub-menu').data('ref-class-content')) {
                case 'tso-content': type = 'table-tso'; break;
                case 'lb-content' : type = 'table-not-given-lb'; break;
            }

            $('#loading-block-document').show();
            dataParams = { ...dataParams, ...data };
            // **
            // * get table ----->
            // *
            var url = (type == 'table-tso') ? '/table-tso': '/table-not-given-lb';
            var res = await ApiService.get_table(url, dataParams);
            // console.log(res);
            if (type == 'table-tso') {
                $('.table-content-tso').html(res);
                build_dropdown_action('table-tso', element_dropdown_tso);
            } else {
                $('.table-content-lb').html(res);
                build_dropdown_action('table-not-given-lb');
            }
            $('#loading-block-document').hide();
            $('.select2-page').select2({ minimumResultsForSearch: -1 });  
            $('.select2-page').on('select2:select', function (e) {
                delete dataParams.page;

                get_table({page_size: $(this).val()})
            });

            
            // **
            // * pagination table ----->
            // *
            $('.pagination-button').on('click', function() {
                get_table({...dataParams, page: parseInt($(this).data('pagination-page'))})
            })
            
        }

        function build_dropdown_action(table_class, element_build) {
            $(`.${table_class} .select_all_card`).off('change');
            $(`.${table_class} .select_all_card`).on('change', function(e) {
                var isChecked = $(this).is(':checked');
                $('.select_card').prop('checked', isChecked);
            });

            $(`.${table_class} tr`).contextmenu(function(e) {
                e.preventDefault();
                let checkbox_element = $(this).find('.select_card');
                if(checkbox_element.length) {
                    $('#dropdown-action').css({display: 'flex', top: e.pageY, left: e.pageX});
                    let htmlElement = element_build(checkbox_element, $('.select_card:checked').length);
                    $('#dropdown-action').html(htmlElement)
                    $(document).off('click');
                    $(document).on("click",function(e){
                        if ($(e.target).closest('#dropdown-action').length) return;
                        close_dropdown_action();
                    });
                }
            });

        }

        function element_dropdown_tso(selected_element, is_multi_select) {
            let is_selected = $(selected_element).is(':checked');
            return $(`
                ${is_multi_select > 1 && is_selected ? `<button type="button" onclick="uncheck_all()" class="flex item-center gap-3 px-4 py-2.5 hover:bg-gray-50">
                    <span class="min-h-[14px] min-w-[14px] w-3.5 h-3.5 border border-gray-400 rounded"></span>
                    <p class="text-xs text-gray-500 font-medium">Unselect All</p>
                </button>`: ''}
                <button type="button" onclick="approved_all()" class="flex item-center gap-3 px-4 py-2.5 hover:bg-gray-50">
                    <x-icon icon="check" class="text-gray-500" width=16 height=16 viewBox="20 20" />
                    <p class="text-xs text-gray-500 font-medium">${is_multi_select > 1 && is_selected? 'Disetujui semua': 'Disetujui'}</p>
                </button>
            `);
        }

        function uncheck_all() {
            $('.select_all_card').prop('checked', false);
            $('.select_card').prop('checked', false);
            close_dropdown_action()
        }

        async function approved_all() {
            let datas = [];
            $('.select_card:checked').each(function(e) {
                let tr = $(this).closest('tr');
                let emp_id = tr.find('input[name="emp_id"]').val();
                let tso_date = tr.find('input[name="tso_date"]').val();
                let dept_id = tr.find('input[name="dept_id"]').val();
                datas.push({emp_id, tso_date,dept_id})
            })
            
            var res = await ApiService.store("{{ route('approved-tso.store') }}", {tso_datas: datas});
            close_dropdown_action();
            get_table({});
        }

        function close_dropdown_action() {
            $('#dropdown-action').css({display: 'none', top: 0, left: 0});
            $('#dropdown-action').html('');
        }
    
        async function get_modal_approve_tso(data_tso) {
            // **
            // * open modal form ----->
            // *
            var URL = '/approved-tso';
            if(data_tso.slug && data_tso.emp_id && data_tso.tso_date && data_tso.dept_id) {
                var res = await ApiService.get_modal(URL, {...data_tso});
                $(".select2-modal").select2();
                if(data_tso.slug == 'plusmn') $('input[name="tso_datas[0][dept_id]"]').val(data_tso.dept_id);
                $('input[name="tso_datas[0][emp_id]"]').val(data_tso.emp_id);
                $('input[name="tso_datas[0][tso_date]"]').val(data_tso.tso_date);
                
                // **
                // * submit form ----->
                // *
                var resSubmit = ApiService.submit_form('.submit-approve-tso', (_response) => { 
                    if (_response.response < 200 || _response.response >= 300) {
                        // * SET NOTIFICATION MESSAGE REQUIRED ----->
                    } else {
                        get_table({ q: $('.search-data-input').val() });
                    }
                });
            }
        }

        async function get_modal_approve_not_given_lb(emp_id, lb_date) {
            // **
            // * open modal form ----->
            // *
            var URL = '/approved-not-given-lb';
            var res = await ApiService.get_modal(URL, null);
            $(".select2-modal").select2();
            $('input[name="tso_datas[0][emp_id]"]').val(emp_id);
            $('input[name="tso_datas[0][lb_date]"]').val(lb_date);
            
            // **
            // * submit form ----->
            // *
            var resSubmit = ApiService.submit_form('.submit-not-given-lb', (_response) => { 
                if (_response.response < 200 || _response.response >= 300) {
                    // * SET NOTIFICATION MESSAGE REQUIRED ----->
                } else {
                    get_table( { q: $('.search-data-input').val() });
                }
            });
        }

        class MyDropdwonTable {
            constructor(tableContainer, dropdownElement) {
                this.tableContainer = tableContainer;
                this.typeListener = 'contextmenu';
                this.dropdownElement = dropdownElement;
                listenerShowDropdown();
            }

            select_all_card() {
                // $(`.${table_class} .select_all_card`).off('change');
                // $(`.${table_class} .select_all_card`).on('change', function(e) {
                //     var isChecked = $(this).is(':checked');
                //     $('.select_card').prop('checked', isChecked);
                // });
            }

            listenerShowDropdown(params) {
                $(`.table-tso tr`).on('contextmenu', function(e) {
                    e.preventDefault();
                    console.log("Sdfsdf");
                    // let checkbox_element = $(this).find('.select_card');
                    // if(checkbox_element.length) {
                    //     $('#dropdown-action').css({display: 'flex', top: e.pageY, left: e.pageX});
                    //     let htmlElement = element_build(checkbox_element, $('.select_card:checked').length);
                    //     $('#dropdown-action').html(htmlElement)
                    //     $(document).off('click');
                    //     $(document).on("click",function(e){
                    //         if ($(e.target).closest('#dropdown-action').length) return;
                    //         close_dropdown_action();
                    //     });
                    // }
                });
            }
        }
</script>
@endsection