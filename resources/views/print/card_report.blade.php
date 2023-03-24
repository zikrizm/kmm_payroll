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

<div class="flex flex-col p-4 w-full h-screen overflow-auto">
    <div id="print-card-report">
        <style>
            @media print {
                .page-break {
                    page-break-before: always;
                }
            }
        </style>
        <div class="flex flex-wrap gap-4 h-full w-full">
            @foreach ($datas as $data)
            <div class="page-break min-h-[400px] max-h-[400px] min-w-[400px] min-w-[400px]">
                <div class="flex flex-col gap-2.5 p-2.5 w-full">
                    <header class="relative">
                        <div>
                            <p class="font-bold text-base">KARTU ABSEN</p>
                            <p class="font-medium text-[10px] ">{{ Auth::user()->business->name }}</p>
                        </div>
                        <div class="flex items-end absolute top-0 w-full">
                            <hr class="flex-1 border-[1.5px] border-black rounded ">
                            <div class="w-14 h-14 bg-white mb-[-10px] rounded-full overflow-hidden">
                                {{-- @if (!empty($item['employee']['photo']))
                                <img class="w-full h-full object-cover" src="@zkPhoto({{ $item['employee']['photo'] }})"
                                    alt="">
                                @else
                                <img class="w-full h-full object-cover" src="@zkPhoto(files/nophoto.gif)" alt="">
                                @endif --}}
                            </div>
                            <hr class="w-10 border-[1.5px] border-black rounded ">
                        </div>
                    </header>
                    <div class="flex flex-col mt-2">
                        <div class="flex items-center gap-2.5">
                            <div class="flex items-center gap-1">
                                <span>
                                    <x-icon icon="calendar" width=10 height=10 viewBox="20 20" />
                                </span>
                                <div class="flex items-center justify-between w-[60px] text-[10px] mt-0.5 pl-0.5">
                                    <p>Tanggal</p>
                                    <p>:</p>
                                </div>
                            </div>
                            <div></div>
                        </div>
                        <div class=" flex items-center gap-2.5">
                            <div class="flex items-center gap-1">
                                <span>
                                    <x-icon icon="user" width=10 height=10 viewBox="20 20" />
                                </span>
                                <div class="flex items-center justify-between w-[60px] text-[10px] mt-0.5 pl-0.5">
                                    <p>Nama</p>
                                    <p>:</p>
                                </div>
                            </div>
                            <div>

                            </div>
                        </div>
                        <div class=" flex items-center gap-2.5">
                            <div class="flex items-center gap-1">
                                <span>
                                    <x-icon icon="briefcase" width=10 height=10 viewBox="20 20" />
                                </span>
                                <div class="flex items-center justify-between w-[60px] text-[10px] mt-0.5 pl-0.5">
                                    <p>Bagian</p>
                                    <p>:</p>
                                </div>
                            </div>
                            <div>

                            </div>
                        </div>
                        <div class=" flex items-center gap-2.5">
                            <div class="flex items-center gap-1">
                                <span>
                                    <x-icon icon="employee-position" width=10 height=10 viewBox="20 20" />
                                </span>
                                <div class="flex items-center justify-between w-[60px] text-[10px] mt-0.5 pl-0.5">
                                    <p>Jabatan</p>
                                    <p>:</p>
                                </div>
                            </div>
                            <div>

                            </div>
                        </div>
                    </div>
                    <table class='table border-collapse w-full border border-black mt-4'>
                        <thead class=''>
                            <tr class='border-t border-black'>
                                <th class='bg-white border  border-t-0 border-l-0 py-1 text-center border-black' rowspan="2">
                                    <p class="text-[8px] font-medium truncate">No.</p>
                                </th>
                                {{-- <th class='bg-white border border-black border-t-0 px-1 py-0.5 text-center'>
                                    <p class="text-[8px] font-medium truncate">Nama</p>
                                </th> --}}
                                @foreach ($data['range_dates'] as $item)
                                <th class="border border-black border-t-0 px-1 py-0.5 text-left">
                                    <p
                                        class="text-[8px] font-medium truncate text-center {{ $item['is_holiday'] ? 'text-red-500' : '' }}">
                                        {{ $item['d'] }}
                                    </p>
                                </th>
                                @endforeach
                                <th class='bg-white border border-black border-t-0 px-1 py-0.5 text-left' rowspan="2">
                                    <p class="text-[8px] font-medium truncate text-center">HK</p>
                                </th>
                                <th class='bg-white border border-black border-t-0 px-1 py-0.5 text-left' rowspan="2">
                                    <p class="text-[8px] font-medium truncate text-center">JL</p>
                                </th>
                                {{-- <th class='bg-white border border-black border-t-0 border-r-0 px-1 py-0.5 text-center'
                                    colspan="6">
                                    <p class="text-[8px] font-medium truncate">Jumlah( Rupiah )</p>
                                </th> --}}
        
                            </tr>
                            <tr class=''>
                                @foreach ($data['range_dates'] as $item)
                                <th class='bg-white border border-black px-1 py-0.5 text-left'>
                                    <div class="tooltip-custom">
                                        <p
                                            class="text-[8px] font-medium truncate text-center {{ $item['is_holiday'] ? 'text-red-500' : '' }}">
                                            {{ $item['key'] }}
                                        </p>
                                    </div>
                                </th>
                                @endforeach
                                {{-- <th class='bg-white border border-black ≈≈ text-center'>
                                    <p class="text-[8px] font-medium truncate">Gaji</p>
                                </th>
                                <th class='bg-white border border-black px-1 py-0.5 text-center'>
                                    <p class="text-[8px] font-medium truncate">Kasbon</p>
                                </th>
                                <th class='bg-white border border-black px-1 py-0.5 text-left'>
                                    <p class="text-[8px] font-medium truncate">Lembur</p>
                                </th>
                                <th class='bg-white border border-black px-1 py-0.5 text-center'>
                                    <p class="text-[8px] font-medium truncate">Rbhn+U.Libur</p>
                                </th>
                                <th class='bg-white border border-black px-1 py-0.5 text-center'>
                                    <p class="text-[8px] font-medium truncate">Jabatan</p>
                                </th>
                                <th class='bg-white border border-black border-r-0 px-1 py-0.5 text-center'>
                                    <p class="text-[8px] font-medium truncate">TOTAL</p> --}}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
        </div>
        <button type=" button" onclick="testPrint()">print</button>
    </div>
</div>
@endif
<script>
    function testPrint(params) {
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