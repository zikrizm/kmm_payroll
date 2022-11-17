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
            <div class="flex items-center gap-2.5">
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
                <div id="approved-all-tso" class="hidden">
                    <button onclick="approved_all_tso()"
                        class="mb-1 flex items-center gap-2.5 px-4 py-[7px] text-gray-500 text-sm font-medium border border-gray-200 shadow-sm rounded-lg">
                        <x-icon icon="check" width=18 height=18 viewBox="20 20" />
                        <p class="truncate">Disetujui semua</p>
                    </button>
                </div>
                <div id="cancel-all-lb" class="hidden">
                    <button onclick="approved_all_not_given_lb()"
                        class="mb-1 flex items-center gap-2.5 px-4 py-[7px] text-gray-500 text-sm font-medium border border-gray-200 shadow-sm rounded-lg">
                        <x-icon icon="x" width=18 height=18 viewBox="20 20" />
                        <p class="truncate">Batalkan semua</p>
                    </button>
                </div>
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
                    start_time:convertLocalTimezone(moment().startOf('week'),'YYYY-MM-DD'), 
                    end_time: convertLocalTimezone(moment().endOf('week'),'YYYY-MM-DD')
                }
            });

            $('input[name="date"]').daterangepicker({
                locale: { format: 'DD-MM-YYYY' },
                startDate: moment().startOf('week'),
                endDate: moment().endOf('week'),
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
                    $('.table-tso .select-all-card').prop('checked', false);
                    $('.table-tso .select-card').prop('checked', false);
                    $('.table-not-given-lb .select-all-card').prop('checked', false);
                    $('.table-not-given-lb .select-card').prop('checked', false);
                    if (!$('#approved-all-tso').is(':hidden')) {
                        $('#approved-all-tso').toggle();
                    }
                    if (!$('#cancel-all-lb').is(':hidden')) {
                        $('#cancel-all-lb').toggle();
                    }
                });

                $(this).addClass('border-b-2 text-violet-700 active-sub-menu');
                $('#'+_idContent).removeClass('hidden');
                get_table({});
            })
        });

        async function get_table(data) {
            if (!$('#approved-all-tso').is(':hidden')) {
                $('#approved-all-tso').toggle();
            }
            if (!$('#cancel-all-lb').is(':hidden')) {
                $('#cancel-all-lb').toggle();
            }
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
            if (type == 'table-tso') {
                $('.table-content-tso').html(res);
                $('.table-tso .select-all-card').off('change');
                $('.table-tso .select-all-card').on('change', function(e) {
                    var isChecked = $(this).is(':checked');
                    $('.table-tso .select-card').prop('checked', isChecked);
                    if ($('.table-tso .select-card:checked').length) {
                        if ($('#approved-all-tso').is(':hidden')) {
                            $('#approved-all-tso').toggle();
                        } else {
                            if(!isChecked) $('#approved-all-tso').toggle();
                        }
                    }else {
                        if (!$('#approved-all-tso').is(':hidden')) $('#approved-all-tso').toggle()
                    }
                });
                $('.table-tso .select-card').on('change', function(e) {
                    if($('.table-tso .select-card:checked').length) {
                        if ($('#approved-all-tso').is(':hidden')) {
                            $('#approved-all-tso').toggle();
                        }
                    }else {
                        $('.table-tso .select-all-card').prop('checked', false)
                        $('#approved-all-tso').toggle();
                    }
                });
            } else {
                $('.table-content-lb').html(res);
                $('.table-not-given-lb .select-all-card').off('change');
                $('.table-not-given-lb .select-all-card').on('change', function(e) {
                    var isChecked = $(this).is(':checked');
                    $('.table-not-given-lb .select-card').prop('checked', isChecked);
                    if ($('.table-not-given-lb .select-card:checked').length) {
                        if ($('#cancel-all-lb').is(':hidden')) {
                            $('#cancel-all-lb').toggle();
                        } else {
                            if(!isChecked) $('#cancel-all-lb').toggle();
                        }
                    }else {
                        if (!$('#cancel-all-lb').is(':hidden')) $('#cancel-all-lb').toggle()
                    }
                });
                $('.table-not-given-lb .select-card').on('change', function(e) {
                    if($('.table-not-given-lb .select-card:checked').length) {
                        if ($('#cancel-all-lb').is(':hidden')) {
                            $('#cancel-all-lb').toggle();
                        }
                    }else {
                        $('.table-not-given-lb .select-all-card').prop('checked', false)
                        $('#cancel-all-lb').toggle();
                    }
                });
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

        async function get_modal_approve_not_given_lb(emp_id, lb_date, dept_id) {
            // **
            // * open modal form ----->
            // *
            var URL = '/approved-not-given-lb';
            var res = await ApiService.get_modal(URL, null);
            $(".select2-modal").select2();
            $('input[name="lb_datas[0][dept_id]"]').val(dept_id);
            $('input[name="lb_datas[0][emp_id]"]').val(emp_id);
            $('input[name="lb_datas[0][lb_date]"]').val(lb_date);
            
            // **
            // * submit form ----->
            // *
            var resSubmit = ApiService.submit_form('.submit-not-given-lb', (_response) => { 
                console.log(_response)
                if (_response.response < 200 || _response.response >= 300) {
                    // * SET NOTIFICATION MESSAGE REQUIRED ----->
                } else {
                    get_table( { q: $('.search-data-input').val() });
                }
            });
        }

        async function approved_all_tso() {
            let datas = [];
            $('.table-tso .select-card:checked').each(function(e) {
                let tr = $(this).closest('tr');
                let emp_id = tr.find('input[name="emp_id"]').val();
                let tso_date = tr.find('input[name="tso_date"]').val();
                let dept_id = tr.find('input[name="dept_id"]').val();
                datas.push({emp_id, tso_date,dept_id})
            })

            var _response = await ApiService.store("{{ route('approved-tso.store') }}", {tso_datas: datas});
            if (_response.response < 200 || _response.response >= 300) {
                // * SHOW NOTIFICATION ----->
                handleMessage(_response);
            } else {
                handleMessage(_response);
                if (!$('#approved-all-tso').is(':hidden')) $('#approved-all-tso').toggle()
                get_table({});
            }
        }
        async function approved_all_not_given_lb() {
            let datas = [];
            $('.table-not-given-lb .select-card:checked').each(function(e) {
                let tr = $(this).closest('tr');
                let emp_id = tr.find('input[name="emp_id"]').val();
                let lb_date = tr.find('input[name="lb_date"]').val();
                let dept_id = tr.find('input[name="dept_id"]').val();
                datas.push({emp_id, lb_date,dept_id})
            })

            var _response = await ApiService.store("{{ route('approved-not-given-lb.store') }}", {lb_datas: datas});
            if (_response.response < 200 || _response.response >= 300) {
                // * SHOW NOTIFICATION ----->
                handleMessage(_response);
            } else {
                handleMessage(_response);
                if (!$('#cancel-all-lb').is(':hidden')) $('#cancel-all-lb').toggle()
                get_table({});
               
            }
        }
</script>
@endsection