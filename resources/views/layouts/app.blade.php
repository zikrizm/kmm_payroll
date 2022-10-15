<!doctype html>
<html lang="str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styling -->
    <link href="{{ asset('plugins/Daterangepicker/daterangepicker.css') }}" rel="stylesheet">

    <!-- Scripts -->
    @vite([
    'resources/js/app.js',
    'resources/plugins/Select2/css/select2.css',
    'resources/plugins/Select2/js/select2.full.min.js',
    'resources/plugins/Toastr/toastr.css',
    'resources/plugins/Toastr/toastr.js',
    // 'resources/plugins/TwElements/css/index.min.css',
    // 'resources/plugins/TwElements/js/index.min.js',
    'resources/css/app.css',
    'resources/css/custom.css',
    ])

    <script src="{{ asset('plugins/JIC/JIC.js') }}" type="text/javascript"></script>
    <script src="{{ asset('plugins/Moment/moment.js') }}" type="text/javascript"></script>
    <script src="{{ asset('plugins/AutoNumeric/autoNumeric.js') }}" type="text/javascript"></script>
    <script src="{{ asset('plugins/Daterangepicker/daterangepicker.js') }}" type="module"></script>
    <script src="{{ asset('plugins/Dropzone/dropzone.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/Ui/DropdownSelect2.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/Remote/networkUtils.js') }}"></script>
    <script src="{{ asset('js/Remote/apiService.js') }}"></script>
    <script src="{{ asset('js/Helper/helper.js') }}"></script>

</head>

<body class="">
    {{-- Loading elemnt --}}
    <div id="loading-block-document" style="display: none;">
        <div class="fixed flex items-center justify-center z-[999] h-screen w-full">
            <div class="w-full h-full absolute" style="background: rgba(0, 0, 0, 0.2);"></div>
            <x-icon icon="loader" class="animate-spin" width=25 height=25 viewBox="20 20" />
        </div>
    </div>
    <div class="w-full h-screen flex">
        @if (request()->segment(2) != 'register' && request()->segment(1) != 'employee-photo')
        @if (Auth::user())
        <aside
            class="w-[260px] h-full bg-white border-r border-gray-200 flex flex-col justify-between gap-4 px-3 overflow-auto no-scrollbar">
            <div class="flex-1">
                <header class="h-32 w-full flex items-center justify-center flex-col gap-1">
                    <div class="text-gray-700 w-10 h-10 rounded-full overflow-hidden">
                        <img src="{{ Auth::user()->business->logo }}" alt="" class="w-full h-full object-cover">
                        {{--
                        <x-icon icon="figma" width=30 height=30 viewBox="20 20" /> --}}
                    </div>
                    <p class="text-gray-700 font-medium">{{ Auth::user()->business->name }}</p>
                </header>
                <main class="flex flex-col gap-2">
                    <a href="{{ route('home.index') }}" class="flex items-center justify-between p-2.5 rounded-lg w-full 
                                @activemenu('home') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu">
                        <div class="flex items-center gap-2.5">
                            <span class="text-gray-600">
                                <x-icon icon="home" width=18 height=18 viewBox="20 20" />
                            </span>
                            <p class="text-sm font-medium text-gray-600">Beranda</p>
                        </div>
                    </a>

                    @canany(['user.view', 'role.view'])
                    <section class="my-dropdown-menu flex items-center justify-between p-2.5 rounded-lg w-full 
                                        @activemenu('users') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu"
                        data-dropdown-toggle="dropdown-menu-user">
                        <div class="flex items-center gap-2.5">
                            <span class="text-gray-600">
                                <x-icon icon="User" width=18 height=18 viewBox="20 20" />
                            </span>
                            <p class="text-sm font-medium text-gray-600">Pengaturan Pengguna</p>
                        </div>
                        <span class="text-gray-500 chevron-icon duration-300">
                            <x-icon icon="chevron-down" width=18 height=18 viewBox="20 20" />
                        </span>
                    </section>
                    <div id="dropdown-menu-user"
                        class="flex flex-col gap-1 @activemenu('users') @else hidden @endactivemenu">
                        @can('user.view')
                        <a href="{{ route('user.index') }}"
                            class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                            @activemenu('user') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="w-5"></div>
                                <p class="text-sm font-medium text-gray-600">Pengguna</p>
                            </div>
                        </a>
                        @endcan
                        @can('role.view')
                        <a href="{{ route('role.index') }}"
                            class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500
                                            @activemenu('role') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="w-5"></div>
                                <p class="text-sm font-medium text-gray-600">Wewenang</p>
                            </div>
                        </a>
                        @endcan
                    </div>
                    @endcanany

                    @canany(['employee.view', 'resign.view', 'kasbon.view'])
                    <section class="my-dropdown-menu flex items-center justify-between p-2.5 rounded-lg w-full 
                                    @activemenu('employees') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu"
                        data-dropdown-toggle="dropdown-menu-employee">
                        <div class="flex items-center gap-2.5">
                            <span class="text-gray-600">
                                <x-icon icon="users" width=18 height=18 viewBox="20 20" />
                            </span>
                            <p class="text-sm font-medium text-gray-600">Pengaturan Karyawan</p>
                        </div>
                        <span class="text-gray-500 chevron-icon duration-300">
                            <x-icon icon="chevron-down" width=18 height=18 viewBox="20 20" />
                        </span>
                    </section>
                    <div id="dropdown-menu-employee" class="flex flex-col gap-1 
                                    @activemenu('employees') @else hidden @endactivemenu">
                        @can('employee.view')
                        <a href="{{ route('employee.index') }}"
                            class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                            @activemenu('employee') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="w-5"></div>
                                <p class="text-sm font-medium text-gray-600">Karyawan</p>
                            </div>
                        </a>
                        @endcan
                        @can('kasbon.view')
                        <a href="{{ route('kasbon.index') }}"
                            class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                            @activemenu('kasbon') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="w-5"></div>
                                <p class="text-sm font-medium text-gray-600">Kasbon</p>
                            </div>
                        </a>
                        @endcan
                        @can('resign.view')
                        <a href="{{ route('resign.index') }}"
                            class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                            @activemenu('resign') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="w-5"></div>
                                <p class="text-sm font-medium text-gray-600">Mengundurkan diri</p>
                            </div>
                        </a>
                        @endcan
                    </div>
                    @endcanany

                    @canany(['break-time.view', 'timetable.view', 'shift.view', 'holiday.view'])
                    <section class="my-dropdown-menu flex items-center justify-between p-2.5 rounded-lg w-full
                                    @activemenu('shifts') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu"
                        data-dropdown-toggle="dropdown-menu-shift">
                        <div class="flex items-center gap-2.5">
                            <span class="text-gray-600">
                                <x-icon icon="clock" width=18 height=18 viewBox="20 20" />
                            </span>
                            <p class="text-sm font-medium text-gray-600">Pengaturan Waktu</p>
                        </div>
                        <span class="text-gray-500 chevron-icon duration-300">
                            <x-icon icon="chevron-down" width=18 height=18 viewBox="20 20" />
                        </span>
                    </section>
                    <div id="dropdown-menu-shift" class="flex flex-col gap-1 
                                    @activemenu('shifts') @else hidden @endactivemenu">
                        @can('break-time.view')
                        <a href="{{ route('break-time.index') }}"
                            class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500
                                            @activemenu('break-time') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="w-5"></div>
                                <p class="text-sm font-medium text-gray-600">Istirahat</p>
                            </div>
                        </a>
                        @endcan
                        @can('timetable.view')
                        <a href="{{ route('timetable.index') }}"
                            class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                            @activemenu('timetable') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="w-5"></div>
                                <p class="text-sm font-medium text-gray-600">Jadwal</p>
                            </div>
                        </a>
                        @endcan
                        @can('shift.view')
                        <a href="{{ route('shift.index') }}"
                            class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                            @activemenu('shift') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="w-5"></div>
                                <p class="text-sm font-medium text-gray-600">Shift</p>
                            </div>
                        </a>
                        @endcan
                        @can('holiday.view')
                        <a href="{{ route('holiday.index') }}"
                            class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                            @activemenu('holiday') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="w-5"></div>
                                <p class="text-sm font-medium text-gray-600">Libur Nasional</p>
                            </div>
                        </a>
                        @endcan
                    </div>
                    @endcanany
                    @canany(['transaction.view'])
                    <section
                        class="my-dropdown-menu flex items-center justify-between p-2.5 rounded-lg w-full
                                    @activemenu('attendances') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu"
                        data-dropdown-toggle="dropdown-menu-attendance">
                        <div class="flex items-center gap-2.5">
                            <span class="text-gray-600">
                                <x-icon icon="smartphone" width=18 height=18 viewBox="20 20" />
                            </span>
                            <p class="text-sm font-medium text-gray-600">Perangkat</p>
                        </div>
                        <span class="text-gray-500 chevron-icon duration-300">
                            <x-icon icon="chevron-down" width=18 height=18 viewBox="20 20" />
                        </span>
                    </section>
                    <div id="dropdown-menu-attendance" class="flex flex-col gap-1 
                                    @activemenu('attendances') @else hidden @endactivemenu">
                        <a href="{{ route('device.index') }}"
                            class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                        @activemenu('device') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="w-5"></div>
                                <p class="text-sm font-medium text-gray-600">Perangkat</p>
                            </div>
                        </a>
                        <a href="{{ route('transaction.index') }}"
                            class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                        @activemenu('transaction') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="w-5"></div>
                                <p class="text-sm font-medium text-gray-600">Absensi</p>
                            </div>
                        </a>
                        <a href="{{ route('transaction.index') }}"
                            class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500
                                        @activemenu('transaction-report') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="w-5"></div>
                                <p class="text-sm font-medium text-gray-600">Laporan Absensi</p>
                            </div>
                        </a>
                    </div>
                    @endcanany
                    @canany(['department.view', 'position.view', 'area.view'])
                    <section
                        class="my-dropdown-menu flex items-center justify-between p-2.5 rounded-lg w-full
                                    @activemenu('organizations') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu"
                        data-dropdown-toggle="dropdown-menu-organization">
                        <div class="flex items-center gap-2.5">
                            <span class="text-gray-600">
                                <x-icon icon="building" width=18 height=18 viewBox="20 20" strokeWidth="3" />
                            </span>
                            <p class="text-sm font-medium text-gray-600">Pengaturan Perusahaan</p>
                        </div>
                        <span class="text-gray-500 chevron-icon duration-300">
                            <x-icon icon="chevron-down" width=18 height=18 viewBox="20 20" />
                        </span>
                    </section>
                    <div id="dropdown-menu-organization" class="flex flex-col gap-1
                                    @activemenu('organizations') @else hidden @endactivemenu">
                        <a href="{{ route('business.index.settings') }}"
                            class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                        @activemenu('setting') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="w-5"></div>
                                <p class="text-sm font-medium text-gray-600">Bisnis</p>
                            </div>
                        </a>
                        @can('department.view')
                        <a href="{{ route('department.index') }}"
                            class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500
                                            @activemenu('department') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="w-5"></div>
                                <p class="text-sm font-medium text-gray-600">Bagian</p>
                            </div>
                        </a>
                        @endcan
                        @can('position.view')
                        <a href="{{ route('position.index') }}"
                            class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500
                                            @activemenu('position') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="w-5"></div>
                                <p class="text-sm font-medium text-gray-600">Jabatan</p>
                            </div>
                        </a>
                        @endcan
                        @can('area.view')
                        <a href="{{ route('area.index') }}"
                            class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500
                                            @activemenu('area') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="w-5"></div>
                                <p class="text-sm font-medium text-gray-600">Area</p>
                            </div>
                        </a>
                        @endcan
                    </div>
                    @endcanany
                    @canany(['attendance-report.view', 'payroll-report.view', 'overtime-rice-report.view'])
                    <section class="my-dropdown-menu flex items-center justify-between p-2.5 rounded-lg w-full
                                    @activemenu('reports') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu"
                        data-dropdown-toggle="dropdown-menu-reports">
                        <div class="flex items-center gap-2.5">
                            <span class="text-gray-600">
                                <x-icon icon="file" width=18 height=18 viewBox="20 20" />
                            </span>
                            <p class="text-sm font-medium text-gray-600">Laporan</p>
                        </div>
                        <span class="text-gray-500 chevron-icon duration-300">
                            <x-icon icon="chevron-down" width=18 height=18 viewBox="20 20" />
                        </span>
                    </section>
                    <div id="dropdown-menu-reports" class="flex flex-col gap-1
                                    @activemenu('reports') @else hidden @endactivemenu">
                        @can('attendance-report.view')
                        <a href="{{ route('attendance-report.index') }}"
                            class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                    @activemenu('attendance-report') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="w-5"></div>
                                <p class="text-sm font-medium text-gray-600">Absensi</p>
                            </div>
                        </a>
                        @endcan
                        @can('payroll-report.view')
                        <a href="{{ route('payroll-report.index') }}"
                            class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500
                                            @activemenu('payroll-report') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="w-5"></div>
                                <p class="text-sm font-medium text-gray-600">Penggajian</p>
                            </div>
                        </a>
                        @endcan
                        @can('overtime-rice-report.view')
                        <a href="{{ route('overtime-rice-report.index') }}"
                            class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500
                                            @activemenu('overtime-rice-report') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="w-5"></div>
                                <p class="text-sm font-medium text-gray-600">Tagihan nasi lembur</p>
                            </div>
                        </a>
                        @endcan
                    </div>
                    @endcanany
                    @canany(['business_settings.access'])
                    <section class="my-dropdown-menu flex items-center justify-between p-2.5 rounded-lg w-full 
                                    @activemenu('settings') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu"
                        data-dropdown-toggle="dropdown-menu-setting">
                        <div class="flex items-center gap-2.5">
                            <span class="text-gray-600">
                                <x-icon icon="settings" width=18 height=18 viewBox="20 20" />
                            </span>
                            <p class="text-sm font-medium text-gray-600">Pengaturan</p>
                        </div>
                        <span class="text-gray-500 chevron-icon duration-300">
                            <x-icon icon="chevron-down" width=18 height=18 viewBox="20 20" />
                        </span>
                    </section>
                    <div id="dropdown-menu-setting" class="flex flex-col gap-1 
                                    @activemenu('settings') @else hidden @endactivemenu">
                        <a href="{{ route('locations.index') }}"
                            class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500
                                        @activemenu('location') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="w-5"></div>
                                <p class="text-sm font-medium text-gray-600">Lokasi</p>
                            </div>
                        </a>
                    </div>
                    @endcanany
                </main>
            </div>
            <hr>
            <footer class="flex items-center justify-between pb-8 w-full">
                <div class="flex items-center gap-2 ">
                    <div class="w-10 h-10 rounded-full bg-gray-100 overflow-hidden">
                        <img src="{{ Auth::user()->photo ? asset('storage/profiles/' . Auth::user()->photo . '') : '' }}"
                            alt="" class="w-full h-full object-cover">
                    </div>
                    <div class="flex flex-col justify-center text-sm">
                        <p class="text-gray-900 font-medium text-sm truncate w-28">{{ Auth::user()->surname }}
                            {{ Auth::user()->first_name }} {{ Auth::user()->last_name }} </p>
                        <p class="text-gray-500 runcate w-28 text-sm">{{ Auth::user()->username }}</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}">
                    {{ csrf_field() }}
                    <button class="text-gray-500 mt-2.5 cursor-pointer">
                        <x-icon icon="log-out" width=16 height=16 viewBox="20 20" />
                    </button>
                </form>
            </footer>
        </aside>
        @endif
        @endif
        @yield('content')
    </div>
    <x-ui.main-modal></x-ui.main-modal>
</body>
@stack('script')
<script type="application/javascript">
    const API = "{{ config('constants.api') }}";
    console.log()
    window.addEventListener('DOMContentLoaded', (event) => {
        $('.my-dropdown-menu').each(function (e) {
            $(this).on('click', function(e) {
                let currentSubMenuClass = $(this).data('dropdown-toggle');
                $('#'+ currentSubMenuClass).toggle('hidden');
                if($(this).hasClass('active')) {
                    $(this).removeClass('active bg-gray-100');
                    $(this).children('.chevron-icon').removeClass('rotate-180')
                } else {
                    $(this).addClass('active bg-gray-100');
                    $(this).children('.chevron-icon').addClass('rotate-180')
                }
            })
        });
    });

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
        if(!isActive) {
            field.sort = { name: sortKey, order: (isActive) ? 'DESC': 'ASC'}
        }
        // Get Data sort table
        get_data_table(sortUrl, field);
    }

    async function get_data_table(url, data) {
        var res = await ApiService.get_table(url, data );
        $('.table-content').html(res);
    }


   
</script>

</html>