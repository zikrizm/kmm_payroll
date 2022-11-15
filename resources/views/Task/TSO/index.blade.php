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
            <div class="w-72">
                {!! FormCustom::input('date', null, [
                'placeholder' => 'Pilih tanggal penugasan',
                'class' => 'date_input',
                'readonly' => true,
                'prefixiconname' => 'calendar',
                ]) !!}
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
            
            get_table('tso',{ 
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
                var dateFormat = 'YYYY-MM-DD';
                delete dataParams.page;
                let _idContent = $('.active-sub-menu').data('ref-class-content');
                var buildData = { 
                    q: $('.search-data-input').val(),
                    date: { 
                        start_time: convertLocalTimezone(start, dateFormat), 
                        end_time: convertLocalTimezone(end, dateFormat)
                    } 
                };
                switch (_idContent) {
                    case 'tso-content':
                        get_table('tso', buildData);
                        break;
                    case 'lb-content' :
                        get_table('not-given-lb', buildData);
                        break;
                    default:
                        break;
                }
            });

            $(".search-data-input").on('keyup', debounce(function(e) {
                if(e.key == 'Shift') return 0;
                delete dataParams.page;
                get_table('tso', { q: this.value });
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
                switch (_idContent) {
                    case 'tso-content':
                        get_table('tso', {});
                        break;
                    case 'lb-content' :
                        get_table('not-given-lb', {});
                        break;
                    default:
                        break;
                }
            })
        });

        async function get_table(type, data) {
            $('#loading-block-document').show();
            dataParams = { ...dataParams, ...data };
            // **
            // * get table ----->
            // *
            var url = (type == 'tso') ? '/table-tso': '/table-not-given-lb';
            var res = await ApiService.get_table(url, dataParams);
            $('#loading-block-document').hide();
            if (type == 'tso') {
                $('.table-content-tso').html(res);
            } else {
                $('.table-content-lb').html(res);
            }
            $('.select2-page').select2({ minimumResultsForSearch: -1 });  
            $('.select2-page').on('select2:select', function (e) {
                delete dataParams.page;

                get_table(type, {page_size: $(this).val()})
            });

            // **
            // * pagination table ----->
            // *
            $('.pagination-button').on('click', function() {
                get_table(type, {...dataParams, page: parseInt($(this).data('pagination-page'))})
            })
            
        }
    
        async function get_modal_approve_tso(data_tso) {
            // **
            // * open modal form ----->
            // *
            var URL = '/approved-tso';
            if(data_tso.slug && data_tso.emp_id && data_tso.tso_date && data_tso.dept_id) {
                var res = await ApiService.get_modal(URL, {...data_tso});
                $(".select2-modal").select2();
                if(data_tso.slug == 'plusmn')  $('input[name=dept_id]').val(data_tso.dept_id);
                $('input[name=emp_id]').val(data_tso.emp_id);
                $('input[name=tso_date]').val(data_tso.tso_date);
                
                // **
                // * submit form ----->
                // *
                var resSubmit = ApiService.submit_form('.submit-approve-tso', (_response) => { 
                    if (_response.response < 200 || _response.response >= 300) {
                        // * SET NOTIFICATION MESSAGE REQUIRED ----->
                    } else {
                        get_table('tso', { q: $('.search-data-input').val() });
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
            $('input[name=emp_id]').val(emp_id);
            $('input[name=lb_date]').val(lb_date);
            
            // **
            // * submit form ----->
            // *
            var resSubmit = ApiService.submit_form('.submit-not-given-lb', (_response) => { 
                if (_response.response < 200 || _response.response >= 300) {
                    // * SET NOTIFICATION MESSAGE REQUIRED ----->
                } else {
                    get_table('not-given-lb', { q: $('.search-data-input').val() });
                }
            });
        }
</script>
@endsection