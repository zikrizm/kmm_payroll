<form autocomplete="off" action="{{ route('payroll-report.store') }}" method="POST" class="submit-payroll-report">
    @csrf
    <!-- {{ csrf_field() }} -->
    <section
        class="container-modal-calculate flex flex-col gap-8 py-4 w-[400px] bg-white border max-h-[95vh] overflow-y-auto overflow-x-hidden relative rounded-lg duration-300">
        <div class="flex items-center gap-5 px-4 relative">
            <button
                class="close-calculate absolute top-[-5px] right-3 xs/max:top-[-6px] modal-close hover:border-gray-500  border border-transparent text-gray-500 rounded p-0.5">
                <x-icon icon="x" width=16 height=16 viewBox="20 20" />
            </button>
            <input type="hidden" name="date[start_time]">
            <input type="hidden" name="date[end_time]">
            <div class="content-loading-calculate hidden w-full">
                <div class="flex items-center gap-6 w-full">
                    <div class="border-r pr-3 h-full">
                        <img src="{{ asset('assets/images/clock.jpg') }}" alt="" width="260" height="260">
                    </div>
                    <div class="flex flex-col gap-4 flex-1 py-1">
                        @foreach ($departments as $key=> $item)
                        <div class="flex-col w-full prosess-cointainer {{ $key > 3 ? 'hidden': 'flex' }}">
                            <input type="hidden" name="dept_id" value="9">
                            <div class="flex items-start gap-3">
                                <div>
                                    <span
                                        class="rounded-full text-violet-600 icon-waiting-prosess {{ $key == 0 ? 'hidden': '' }}">
                                        <x-icon icon="rounded-border" width=20 height=20 strokeWidth=3
                                            viewBox="20 20" />
                                    </span>
                                    <div class="rounded-full bg-violet-600 text-white p-1 icon-finish-prosess hidden">
                                        <x-icon icon="check" width=12 height=12 strokeWidth=3 viewBox="20 20" />
                                    </div>
                                    <span
                                        class="rounded-full text-violet-600 icon-on-prosess {{ $key != 0 ? 'hidden': '' }}">
                                        <svg class="h-5 w-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </span>
                                </div>
                                <div class="flex-1 flex flex-col gap-1.5">
                                    <div class="flex justify-between items-center">
                                        <p class="text-gray-700 text-sm font-semibold">{{ $item['dept_name'] }}</p>
                                        <p class="text-gray-400 text-[10px] text-prosess">{{ $key != 0 ? 'Menunggu
                                            proses':
                                            'Dalam proses' }}</p>
                                    </div>
                                    <div
                                        class="flex flex-col gap-2 prosess-container-perdates {{ $key != 0 ? 'hidden': '' }}">
                                        @foreach ($dates as $item)
                                        <div class="flex justify-between items-center container-perdate">
                                            <p class="text-gray-500 text-xs font-medium proses-date">
                                                {{ Carbon\Carbon::parse($item)->locale('id')->dayName }}, {{
                                                Carbon\Carbon::parse($item)->format('j F Y'); }}
                                            </p>
                                            <div class="flex items-center justify-center w-7">
                                                <span
                                                    class="icon-date-finish-prosess rounded-full bg-gray-400 text-white p-0.5 h-max hidden">
                                                    <x-icon icon="check" width=8 height=8 strokeWidth=3
                                                        viewBox="20 20" />
                                                </span>
                                                <p class="text-violet-600 text-[10px] font-semibold">
                                                    <span class="percent">0</span>%
                                                </p>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="content-finish-calculate hidden w-full">
                <div class="flex items-center gap-6 w-full">
                    <div class="border-r pr-3 h-full">
                        <img src="{{ asset('assets/images/clock.jpg') }}" alt="" width="260" height="260">
                    </div>
                    <div class="flex flex-col gap-2 flex-1 py-1 justify-center">
                        <div class="flex items-center gap-2 text-violet-600">
                            <x-icon icon="check-circle" width=30 height=30 viewBox="20 20" />
                            <p class="font-medium text-base ">
                                SELESAI</p>
                        </div>
                        <hr>
                        <p class="text-gray-500 font-normal text-xs">
                            Kakulasi penggajian telah selesai, dan data telah tersimpan.</p>
                        <button type="reset"
                            class="modal-close w-max shadow text-gray-500 bg-white hover:bg-gray-100 focus:ring-2 focus:ring-gray-300 rounded-lg xs/max:rounded-md border border-gray-200 text-sm xs/max:text-xs font-medium xs/max:px-4 px-6 xs/max:py-1.5 py-1 hover:text-gray-900 focus:z-10">Tutup</button>
                    </div>
                </div>
            </div>
            <div class="content-confirm-calculate flex items-center gap-5">
                <div class="text-gray-500">
                    <x-icon icon="bullhorn" width=100 height=100 viewBox="20 20" />
                </div>
                <div class="flex flex-col gap-3 flex-1">
                    <div class="flex flex-col gap-1">
                        <p class="text-gray-700 font-bold text-lg">Hei tunggu</p>
                        <p class="text-gray-500 font-normal text-xs">
                            Apakah anda yakin, ingin melakukan kalkulasi penggajian ini?,
                            karena data akan tersimpan.</p>
                    </div>
                    <div class="flex items-center gap-3 flex-1">
                        <button id="kalkulasi"
                            class="text-gray-500 shadow bg-white hover:bg-gray-100 focus:ring-2 focus:ring-gray-300 font-medium rounded-lg xs/max:rounded-md border border-gray-200  text-sm xs/max:text-xs inline-flex items-center xs/max:px-4 px-6 xs/max:py-1.5 py-1 text-center">Ya,
                            kalkulasi</button>
                        <button type="reset"
                            class="modal-close shadow text-gray-500 bg-white hover:bg-gray-100 focus:ring-2 focus:ring-gray-300 rounded-lg xs/max:rounded-md border border-gray-200 text-sm xs/max:text-xs font-medium xs/max:px-4 px-6 xs/max:py-1.5 py-1 hover:text-gray-900 focus:z-10">Batal</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</form>