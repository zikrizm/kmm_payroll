@extends('layouts.app')
@section('title', 'Tagihan nasi-lembur')
@section('css')
<style></style>
@endsection
@section('content')
<div class="flex flex-col gap-6 flex-1 h-full overflow-auto bg-white px-8 pt-8 pb-12">
    <header class="flex justify-between items-start">
        <div class="flex flex-col gap-1">
            <p class="text-3xl font-medium text-gray-900">Tagihan nasi lembur</p>
            <p class="text-base font-normal text-gray-500">Di sini untuk melihat tagihan nasi lembur yang sudah
                ter-kalkulasi.
            </p>
        </div>
    </header>
    <hr>
    <div class="flex items-center justify-between gap-2.5">
        <div class="w-72">
            {!! FormCustom::input('selected_date', null, [
            'placeholder' => 'Pilih tanggal tagihan',
            'class' => 'date_input',
            'readonly' => true,
            'prefixiconname' => 'calendar',
            ]) !!}
        </div>
        <x-ui.search-data placeholder="Cari tagihan" url="{{ route('ot-rice-bill.index') }}" />
    </div>
    <div class="table-content"></div>
</div>

<script type="application/javascript">
    let dataParams = {};

    window.addEventListener('DOMContentLoaded', (event) => {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        onInit({
            q: $('.search-data-input').val(),
            start_date: convertLocalTimezone(moment().startOf('week'), 'YYYY-MM-DD'), 
            end_date: convertLocalTimezone(moment().endOf('week'), 'YYYY-MM-DD')
        });

        $('input[name="selected_date"]').daterangepicker({
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
            delete dataParams.page;
            onInit({ 
                q: $('.search-data-input').val(),
                start_date: convertLocalTimezone(start, dateFormat), 
                end_Date: convertLocalTimezone(end, dateFormat),
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
        var res = await ApiService.get_table('/ot-rice-bill', data);
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

    function resetSortTable() {
        $('.sort-table').each(function(e) {
            $(this).removeClass('active');
            $(this).children('.sort-icon').removeClass('rotate-180');
        })
    }

    function sort_data(event) {
        let sortKey = $(event).data('sort-key');
        let sortUrl = $(event).data('sort-url');
        let isActive = $(event).hasClass('active');

        // Reset sort table
        resetSortTable();
        // Build Data sort table
        let field = { q: $('.search-data-input').val(), };
        field.sort = { name: sortKey, order: (isActive) ? 'ASC': 'DESC'}
        console.log(field)
        // Get Data sort table
        onInit(field)
    }
</script>
@endsection