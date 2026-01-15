@extends('layouts.app')
@section('title', 'Absensi Karyawan')
@section('css')
    <style></style>
@endsection
@section('content')
    <div class="flex flex-col gap-6 flex-1 h-full overflow-auto bg-white px-8 pt-8 pb-12">
        <header class="flex justify-between items-start">
            <div class="flex flex-col gap-1">
                <p class="text-3xl font-medium text-gray-900">Absensi Karyawan</p>
                <p class="text-base font-normal text-gray-500">Pengaturan kehadiran setiap karyawan.</p>
            </div>
            <div>
                <button onclick="get_modal()"
                    class="flex items-center gap-2.5 px-4 py-2 text-gray-500 text-sm font-medium 
                flex items-center border border-gray-200 shadow-sm rounded-lg">
                    <x-icon icon="plus" width=18 height=18 viewBox="20 20" />
                    Tambah absensi
                </button>
            </div>
        </header>
        <hr>
        <div class="flex justify-between">
            <div class="flex items-center gap-2">
                <div class="w-96">
                    {!! FormCustom::input('header-date', null, [
                        'placeholder' => 'Silahkan pilih tanggal absen',
                        'class' => 'date_input',
                        'readonly' => true,
                        'prefixiconname' => 'calendar',
                    ]) !!}
                </div>
                <button id="export" href="{{ route('transaction.transactionExport') }}"
                    class="flex items-center gap-2.5 px-4 py-2 text-gray-500 text-sm font-medium 
                flex items-center border border-gray-200 shadow-sm rounded-lg">
                    <x-icon icon="download" width=18 height=18 viewBox="20 20" />
                    Export
                </button>
            </div>
            <div class="flex items-center gap-2">
                <div class="relative flex items-center h-9 w-full min-w-[180px] max-w-[280px]">
                    <div
                        class="absolute left-0 flex h-full w-9 items-center justify-center text-center text-sm text-gray-500">
                        <span class="">
                            <x-icon icon="search" width="16" height="16" viewBox="20 20" />
                        </span>
                    </div>
                    <input placeholder="Cari absensi karyawan"
                        class="search-data-input focus:shadow-xs/focused(4px-primary) h-full w-full rounded-lg border border-gray-300 pl-9 pr-2.5 text-sm shadow-sm focus:border-violet-300 focus:outline-none focus:ring-0" />
                </div>
                <button id="search-button"
                    class="flex items-center gap-2.5 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-500 shadow-sm hover:bg-gray-50">
                    Cari
                </button>
            </div>
        </div>
        <div class="table-content"></div>
        <x-ui.confirm-modal class="submit-delete-transaction"></x-ui.confirm-modal>
    </div>
    <script type="application/javascript">
    let dataParams = {};

    window.addEventListener('DOMContentLoaded', (event) => {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        onInit({ 
            q: $('.search-data-input').val(),
            date: { 
                start_time:convertLocalTimezone(moment().startOf('week'), 'YYYY-MM-DD'), 
                end_time: convertLocalTimezone(moment().endOf('week'), 'YYYY-MM-DD')
            }
        });

        $('#export').click(() => {
            console.log("jalan ke sini")
            var searchQuery = $('.search-data-input').val(); // q
            var startDate = convertLocalTimezone(moment().startOf('week'), 'YYYY-MM-DD'); // start_time
            var endDate = convertLocalTimezone(moment().endOf('week'), 'YYYY-MM-DD'); // end_time

            // Bangun URL dengan parameter ter-encode
            var url = "{{ route('transaction.transactionExport') }}" + "?q=" + encodeURIComponent(searchQuery) +
                  "&date%5Bstart_time%5D=" + encodeURIComponent(startDate) +
                  "&date%5Bend_time%5D=" + encodeURIComponent(endDate);
                   window.location.href = url;

        })

        $('input[name="header-date"]').daterangepicker({
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
            var dateFormat = 'YYYY-MM-DD';

            $('.search-data-input').val('');
            delete dataParams.page;
            onInit({ 
                date: { 
                    start_time: convertLocalTimezone(start, dateFormat), 
                    end_time: convertLocalTimezone(end, dateFormat)
                } 
            });
        });

        $('#search-button').on('click', async function() {
            const btn = $(this);
            const originalContent = btn.html();

            btn.prop('disabled', true).html(`
                <svg class="animate-spin text-gray-500" width="18" height="18" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Mencari...
            `);

            try {
                delete dataParams.page;
                await onInit({ q: $('.search-data-input').val() });
            } catch (error) {
                console.error(error);
            } finally {
                btn.prop('disabled', false).html(originalContent);
            }
        });
    });

    async function onInit(data) {
        // **
        // * Build data params table ----->
        // *
        dataParams = { ...dataParams, ...data };

        // Show loading state in table
        $('.table-content').html(`
            <div class="flex flex-col items-center justify-center py-12">
                <svg class="animate-spin h-8 w-8 text-violet-600 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-gray-500 font-medium">Memuat data...</p>
            </div>
        `);

        // **
        // * get table ----->
        // *
        var res = await ApiService.get_table('/transaction', dataParams);
        $('.table-content').html(res);
        $('.select2-page').select2({ minimumResultsForSearch: -1 });  
        $('.select2-page').on('select2:select', function (e) {
            delete dataParams.page;

            onInit({page_size: $(this).val()})
        });

        // **
        // * pagination table ----->
        // *
        $('.pagination-button').on('click', function() {
            onInit({...dataParams, page: parseInt($(this).data('pagination-page'))})
        })
    }

    async function get_modal(id) {
        // **
        // * open modal form ----->
        // *
        var URL = (id) ? '/transaction/' + id + '/edit' : '/transaction/create';
        var res = await ApiService.get_modal(URL, null);
        $('.select2-form').select2();
        select2_employee();
        $('input[name="punch_time"]').daterangepicker({
            locale: { format: 'YYYY-MM-DD HH:mm:ss', cancelLabel: 'Clear' },
            singleDatePicker: true,
            showDropdowns: true,
            timePicker: true,
            timePicker24Hour: true,
            timePickerSeconds: true,
            autoUpdateInput: false,
            minYear: 2000,
            drops: "auto",
            maxYear: parseInt(moment().format('YYYY'), 10)
        });

        $('input[name="punch_time"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD HH:mm:ss'));
        });

        $('input[name="punch_time"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });


        // **
        // * submit form ----->
        // *
        var resSubmit = await ApiService.submit_form('.submit-transaction', (data) => {
            onInit( { q: $('.search-data-input').val() });
        });
    }

    async function open_modal_confirm(id) {
        // **
        // * open modal confirm ----->
        // *
        await ApiService.get_confirm('.submit-delete-transaction', '/transaction/' + id, null, () => {
            onInit( { q: $('.search-data-input').val() });
        })
    }
</script>
@endsection
