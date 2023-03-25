@extends('layouts.app')
@section('title', 'Print | Card Report')
@section('content')
@if (empty($start_date) || empty($end_date) )
<div class="w-full h-screen flex items-center justify-center">
    <div class="flex flex-col items-start justify-start gap-4">
        <span>
            <x-icon icon="alert-triangle" width=50 height=50 viewBox="20 20" />
        </span>
        <div class="text-left">
            <p class="text-2xl font-semibold">Laporan tidak ada</p>
            <p>Karena anda tidak memberikan data-data berikut ini <br> atau format data salah</p>
        </div>
        <div class="h-5"></div>
        <form action="/print/card_report" method="get" class="flex flex-col items-start justify-center gap-4 w-80">
            <div class="flex flex-col gap-2.5 w-full">
                <div class="w-full flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-900">
                        Tanggal hari kerja <span class="text-[8px] text-red-600">*</span>
                    </label>
                    <div class="">
                        <input type="hidden" name="start_date">
                        <input type="hidden" name="end_date">
                        <div class="flex-1">
                            {!! FormCustom::input('', null, [
                            'placeholder' => 'Tanggal hari kerja',
                            'class' => 'date-input date-input-work-day',
                            'readonly' => true,
                            'prefixiconname' => 'calendar',
                            ]) !!}
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="px-10 py-1.5 rounded-lg bg-purple-600 text-white ">
                Muat ulang
            </button>
        </form>
    </div>
</div>
@else
<style media="print" type="text/css">
    .grand-total-bg-color {
        background-color: rgb(229 231 235) !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    .break-card {
        page-break-before: always;
    }
</style>

<div class="flex flex-col items-center w-full h-screen overflow-auto py-8">
    <div class="w-full flex justify-center items-center">
        <button type="button" onclick="print()"
            class="text-white shadow bg-violet-600 hover:bg-violet-700 focus:ring-2 focus:ring-violet-700 font-medium rounded-lg xs/max:rounded-md text-sm xs/max:text-xs inline-flex items-center xs/max:px-4 px-6 xs/max:py-1.5 py-2 text-center">Cetak</button>
    </div>
    <div id="print-card-report" class="w-full flex justify-center">
        <style>
            @media print {
                .page-break {
                    page-break-before: always;
                }
            }
        </style>
        <div class="h-full w-full flex flex-col items-center">
            @foreach ($datas as $data)
            @foreach ($data['attendance_reports'] as $key => $item)
            <div class="page-break h-max w-full max-w-[350px]">
                <div class="flex flex-col gap-1 p-2.5 w-full">
                    <div class="flex flex-col mt-2">
                        <div class="flex items-center gap-2 text-xs">
                            <div class="flex items-center justify-between gap-2">
                                <p>NIK</p>
                                <p>:</p>
                            </div>
                            <p>{{ $item['employee']['emp_code'] }}</p>
                        </div>
                        <div class="flex items-center gap-2 text-xs">
                            <div class="flex items-center justify-between gap-2">
                                <p>Nama</p>
                                <p>:</p>
                            </div>
                            <p>
                                {{ $item['employee']['first_name'] ?? '-' }}
                                {{ $item['employee']['last_name'] ?? '' }}
                            </p>
                        </div>
                    </div>
                    <hr class="w-full border-1 border-black">
                    <hr class="w-full border-1 border-black">
                    <div class="flex justify-end w-full pt-4">
                        <div class="flex flex-col max-w-[200px] w-full">
                            <div class="flex items-center justify-between w-full gap-2 text-xs">
                                <div class="flex items-center justify-between gap-2">
                                    <p>Gaji</p>
                                    <p>:</p>
                                </div>
                                <p>
                                    @if (isset($item['salary_pay_value']))
                                    @convertnorp($item['salary_pay_value'])
                                    @else
                                    -
                                    @endif
                                </p>
                            </div>
                            <div class="flex items-center justify-between w-full gap-2 text-xs">
                                <div class="flex items-center justify-between gap-2">
                                    <p>Kasbon</p>
                                    <p>:</p>
                                </div>
                                <p>
                                    @if (isset($item['kasbon_pay_value']))
                                    @convertnorp($item['kasbon_pay_value'])
                                    @else
                                    -
                                    @endif
                                </p>
                            </div>
                            <div class="flex items-center justify-between w-full gap-2 text-xs">
                                <div class="flex items-center justify-between gap-2">
                                    <p>Lembur</p>
                                    <p>:</p>
                                </div>
                                <p>
                                    @if (isset($item['overtime_pay_value']))
                                    @convertnorp($item['overtime_pay_value'])
                                    @else
                                    -
                                    @endif
                                </p>
                            </div>
                            <div class="flex items-center justify-between w-full gap-2 text-xs">
                                <div class="flex items-center justify-between gap-2">
                                    <p>Rbhn+U.Libur</p>
                                    <p>:</p>
                                </div>
                                <p>
                                    @if (isset($item['tbhn_u_libur_pay_value']))
                                    @convertnorp($item['tbhn_u_libur_pay_value'])
                                    @else
                                    -
                                    @endif
                                </p>
                            </div>
                            <div class="flex items-center justify-between w-full gap-2 text-xs">
                                <div class="flex items-center justify-between gap-2">
                                    <p>Jabatan</p>
                                    <p>:</p>
                                </div>
                                <p>
                                    @if (isset($item['tbhn_u_position_pay_value']))
                                    @convertnorp($item['tbhn_u_position_pay_value'])
                                    @else
                                    -
                                    @endif
                                </p>
                            </div>
                            <hr class="w-full border-1 border-black my-1">
                            <div class="flex items-center justify-between w-full gap-2 text-xs">
                                <div class="flex items-center justify-between gap-2">
                                    <p>Sisa kasbon</p>
                                    <p>:</p>
                                </div>
                                <p>
                                    @if (isset($item['remaining_kasbon_pay_value']))
                                    @convertnorp($item['remaining_kasbon_pay_value'])
                                    @else
                                    -
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    <hr class="w-full border-1 border-black">
                    <div class="flex w-full justify-end">
                        <div class="flex items-center justify-between gap-1 text-sm font-semibold">
                            <p class="truncate text-right">Total: </p>
                            <p class="truncate text-right">
                                @if (isset($item['total_pay_value']))
                                @convertnorp($item['total_pay_value'])
                                @else
                                -
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            @endforeach
        </div>
    </div>
</div>
@endif
<script>
    function print(params) {
        var originalContents, popupWin, printContents;
        return printContents = document.getElementById(
                "print-card-report").innerHTML,
            originalContents = document.body.innerHTML,
            headContents = document.head.innerHTML,
            popupWin = window.open(),
            popupWin.document.open(),
            popupWin.document.write(
                '<html><head>' + headContents +
                '<body onload="window.print()">' +
                printContents + "</html>"),
            popupWin.document.close()
    }
    window.addEventListener('DOMContentLoaded', (event) => {
        $('.date-input').daterangepicker({
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(
                        1, 'days'), moment()
                    .subtract(1, 'days')
                ],
                'Last 7 Days': [moment()
                    .subtract(6, 'days'),
                    moment()
                ],
                'Last 30 Days': [moment()
                    .subtract(29, 'days'),
                    moment()
                ],
                'This Month': [moment().startOf(
                        'month'), moment()
                    .endOf('month')
                ],
                'Last Month': [moment()
                    .subtract(1, 'month')
                    .startOf('month'),
                    moment().subtract(1,
                        'month').endOf(
                        'month')
                ]
            },
            autoUpdateInput: false,
            alwaysShowCalendars: true,
            showCustomRangeLabel: false,
            showDropdowns: true,
            minYear: 2000,
            drops: "auto",
            maxYear: parseInt(moment().format(
                'YYYY'), 10)
        }, function (start, end, label) {
            console.log($(this.element).val())
        });

        $('.date-input-work-day').on(
            'apply.daterangepicker',
            function (ev, picker) {
                $('input[name="start_date"]').val(
                    picker.startDate.format(
                        'DD-MM-YYYY'));
                $('input[name="end_date"]').val(
                    picker.endDate.format(
                        'DD-MM-YYYY'));
                $(this).val(picker.startDate.format(
                        'DD-MM-YYYY') + ' - ' +
                    picker.endDate.format(
                        'DD-MM-YYYY'));
            });

        $('.date_input').on('cancel.daterangepicker',
            function (ev, picker) {
                $(this).val('');
            });
    });

</script>
@endsection