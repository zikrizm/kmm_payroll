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

    <style>
        .dropzone {
            background: white;
            border-radius: 5px;
            border: 2px dashed rgb(0, 135, 247);
            border-image: none;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
    <!-- Styling -->
    <link href="{{ asset('plugins/Daterangepicker/daterangepicker.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/Timepicker/timepicker.css') }}" rel="stylesheet">

    <!-- Scripts -->
    @vite([
    'resources/js/app.js',
    'resources/plugins/Select2/css/select2.css',
    'resources/plugins/Select2/js/select2.full.min.js',
    'resources/plugins/Toastr/toastr.css',
    'resources/plugins/Toastr/toastr.js',
    'resources/plugins/Dropzone/dropzone.css',
    'resources/plugins/Dropzone/dropzone.js',
    'resources/plugins/Jquery-validate/jquery-validate.js',
    'resources/plugins/Jquery-validate/additional-methods.js',
    // 'resources/plugins/TwElements/css/index.min.css',
    // 'resources/plugins/TwElements/js/index.min.js',
    'resources/css/app.css',
    'resources/css/custom.css',
    ])

    <script src="{{ asset('plugins/JIC/JIC.js') }}" type="text/javascript"></script>
    <script src="{{ asset('plugins/Moment/moment.js') }}" type="text/javascript"></script>
    <script src="{{ asset('plugins/AutoNumeric/autoNumeric.js') }}" type="text/javascript"></script>
    <script src="{{ asset('plugins/Daterangepicker/daterangepicker.js') }}" type="module"></script>
    <script src="{{ asset('plugins/Timepicker/timepicker.js') }}" type="module"></script>
    <script src="{{ asset('plugins/Jquery-cookie/jquery-cookie.js') }}" type="module"></script>
    <script src="{{ asset('js/Ui/DropdownSelect2.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/Remote/networkUtils.js') }}"></script>
    <script src="{{ asset('js/Remote/apiService.js') }}"></script>
    <script src="{{ asset('js/Helper/helper.js') }}"></script>
    <script src="{{ asset('js/Helper/dropzoneUploadCsv.js') }}"></script>

</head>

<body class="overflow-hidden">
    {{-- <div id="loading-line"
        class="duration-1000 w-0 h-[3px] rounded hidden bg-black absolute top-0 bg-gradient-to-r from-violet-300 to-violet-700 ">

    </div> --}}
    <div id="dropdown-menu"
        class="hidden absolute border border-gray-200 backdrop-blur-[3px] rounded-xl shadow-sm p-1.5 bg-transparent z-[999]">
    </div>
    {{-- Loading elemnt --}}
    <div id="loading-block-document" style="display: none;">
        <div class="fixed flex items-center justify-center z-[999] h-screen w-full">
            <div class="w-full h-full absolute" style="background: rgba(92, 92, 92, 0.1);"></div>
            <div class="flex items-center ">
                {{-- backdrop-blur-sm py-2 px-3 rounded-xl --}}
                <svg class="-ml-1 mr-3 h-10 w-10 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <p class="text-gray-500 text-xl font-semibold">Loading...</p>
            </div>
        </div>
    </div>
    {{-- @dd() --}}

    <div class="w-full h-screen flex overflow-hidden">
        @if (request()->segment(2) != 'register' && request()->segment(1) != 'employee-photo')
        @if (Auth::user())
        @php
        if(isset($_COOKIE['side_menu_is_mini']))
        $is_mini = $_COOKIE['side_menu_is_mini'] == 'true';
        else
        $is_mini = false;

        @endphp
        <div id="switch-size-menu"
            class="cursor-pointer w-max absolute top-[20px] {{ $is_mini ? 'left-[70px]' : 'left-[248px]' }}  rounded-full p-0.5 text-gray-500 border shadow backdrop-blur-[3px] ">
            <x-icon icon="chevron-left"
                class="chevron-left-switch-size-menu duration-500 {{ $is_mini ? 'rotate-180' : '' }}" width=16 height=16
                viewBox="20 20" />
        </div>
        <aside id="aside-navigation"
            class="{{ $is_mini ? 'w-[82px]' : 'is-full-size w-[260px]' }} h-full bg-white border-r border-gray-200 flex flex-col justify-between gap-4 px-3 overflow-auto no-scrollbar">
            <div class="flex-1">
                <header class="h-32 w-full flex items-center justify-center flex-col gap-1">
                    <div class="text-gray-700 w-10 h-10 rounded-full overflow-hidden">
                        <img src="{{ Auth::user()->business->logo }}" alt="" class="w-full h-full object-cover">
                    </div>
                    <p class="text-gray-700 font-medium name-business {{ $is_mini ? 'hidden' : '' }}">
                        {{ Auth::user()->business->name }}</p>
                </header>
                <main class="flex flex-col gap-2">
                    <a href="{{ route('home.index') }}" data-tooltip-menu="Dashboard" class="my-dropdown-menu cursor-pointer flex items-center {{ $is_mini ? 'justify-center' : 'justify-between' }} hover:bg-gray-50 p-2.5 rounded-lg w-full 
                                @activemenu('home')
bg-gray-100 active
@else
hover:bg-gray-50
@endactivemenu">
                        <div class="flex items-center gap-2.5">
                            <span class="text-gray-600">
                                <x-icon icon="home" width=18 height=18 viewBox="20 20" />
                            </span>
                            <p class="text-sm font-medium text-gray-600 name-menu {{ $is_mini ? 'hidden' : '' }}">
                                Dashboard</p>
                        </div>
                    </a>
                    @canany(['department.view', 'position.view', 'area.view', 'device.view'])
                    <section class="my-dropdown-menu cursor-pointer hover:bg-gray-50 flex items-center {{ $is_mini ? 'justify-center' : 'justify-between' }} p-2.5 rounded-lg w-full
                                    @activemenu('organization')
bg-gray-100 active
@else
hover:bg-gray-50
@endactivemenu" data-dropdown-toggle="dropdown-menu-organization" data-tooltip-menu="Pengaturan Perusahaan">
                        <div class="flex items-center gap-2.5">
                            <span class="text-gray-600">
                                <x-icon icon="building" width=18 height=18 viewBox="20 20" strokeWidth="3" />
                            </span>
                            <p class="text-sm font-medium text-gray-600 name-menu {{ $is_mini ? 'hidden' : '' }}">
                                Pengaturan Perusahaan</p>
                        </div>
                        <span class="text-gray-500 chevron-icon-menu  {{ $is_mini ? 'hidden' : '' }} duration-300">
                            <x-icon icon="chevron-down" width=18 height=18 viewBox="20 20" />
                        </span>
                    </section>
                    <div id="dropdown-menu-organization" class="sub-menu-content {{ $is_mini ? 'hidden' : 'flex' }} flex-col gap-1
                                 @activemenu('organization')
sub-menu-active
@else
hidden
@endactivemenu">
                        <a href="{{ route('business.index.settings') }}" class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                        @activemenu('setting')
bg-gray-100 active
@else
hover:bg-gray-50
@endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="space-sub-menu w-5"></div>
                                <p class="text-sub-menu text-sm font-medium text-gray-600">
                                    Perusahaan</p>
                            </div>
                        </a>
                        @can('department.view')
                        <a href="{{ route('department.index') }}" class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500
                                            @activemenu('department')
bg-gray-100 active
@else
hover:bg-gray-50
@endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="space-sub-menu w-5"></div>
                                <p class="text-sub-menu text-sm font-medium text-gray-600">
                                    Bagian karyawan</p>
                            </div>
                        </a>
                        @endcan
                        @can('position.view')
                        <a href="{{ route('position.index') }}" class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500
                                            @activemenu('position')
bg-gray-100 active
@else
hover:bg-gray-50
@endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="space-sub-menu w-5"></div>
                                <p class="text-sub-menu text-sm font-medium text-gray-600">
                                    Jabatan karyawan</p>
                            </div>
                        </a>
                        @endcan
                        @can('area.view')
                        <a href="{{ route('area.index') }}" class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500
                                            @activemenu('area')
bg-gray-100 active
@else
hover:bg-gray-50
@endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="space-sub-menu w-5"></div>
                                <p class="text-sub-menu text-sm font-medium text-gray-600">
                                    Area karyawan</p>
                            </div>
                        </a>
                        @endcan
                        <a href="{{ route('device.index') }}" class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                        @activemenu('device')
bg-gray-100 active
@else
hover:bg-gray-50
@endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="space-sub-menu w-5"></div>
                                <p class="text-sub-menu text-sm font-medium text-gray-600">
                                    Perangkat absensi</p>
                            </div>
                        </a>
                    </div>
                    @endcanany
                    @canany(['user.view', 'role.view'])
                    <section class="my-dropdown-menu cursor-pointer flex items-center {{ $is_mini ? 'justify-center' : 'justify-between' }} p-2.5 rounded-lg w-full 
                                        @activemenu('users')
bg-gray-100 active
@else
hover:bg-gray-50
@endactivemenu" data-dropdown-toggle="dropdown-menu-user" data-tooltip-menu="Pengaturan Pengguna">
                        <div class="flex items-center gap-2.5">
                            <span class="text-gray-600">
                                <x-icon icon="User" width=18 height=18 viewBox="20 20" />
                            </span>
                            <p class=" {{ $is_mini ? 'hidden' : '' }} name-menu text-sm font-medium text-gray-600">
                                Pengaturan Pengguna</p>
                        </div>
                        <span class="text-gray-500 chevron-icon-menu  {{ $is_mini ? 'hidden' : '' }} duration-300">
                            <x-icon icon="chevron-down" width=18 height=18 viewBox="20 20" />
                        </span>
                    </section>
                    <div id="dropdown-menu-user" class="sub-menu-content {{ $is_mini ? 'hidden' : 'flex' }} flex-col gap-1 
                                    @activemenu('users')
sub-menu-active
@else
hidden
@endactivemenu">
                        @can('user.view')
                        <a href="{{ route('user.index') }}" class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                            @activemenu('user')
bg-gray-100 active
@else
hover:bg-gray-50
@endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="space-sub-menu w-5"></div>
                                <p class="text-sub-menu text-sm font-medium text-gray-600">
                                    Pengguna</p>
                            </div>
                        </a>
                        @endcan
                        @can('role.view')
                        <a href="{{ route('role.index') }}" class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500
                                            @activemenu('role')
bg-gray-100 active
@else
hover:bg-gray-50
@endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="space-sub-menu w-5"></div>
                                <p class="text-sub-menu text-sm font-medium text-gray-600">
                                    Wewenang</p>
                            </div>
                        </a>
                        @endcan
                        <a href="{{ route('activity-log.index') }}" class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500
                                            @activemenu('activity-log')
bg-gray-100 active
@else
hover:bg-gray-50
@endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="space-sub-menu w-5"></div>
                                <p class="text-sub-menu text-sm font-medium text-gray-600">
                                    Aktifitas pengguna
                                </p>
                            </div>
                        </a>
                    </div>
                    @endcanany
                    @canany(['break-time.view', 'timetable.view', 'shift.view', 'holiday.view'])
                    <section class="my-dropdown-menu cursor-pointer flex items-center {{ $is_mini ? 'justify-center' : 'justify-between' }} p-2.5 rounded-lg w-full
                                    @activemenu('shifts')
bg-gray-100 active
@else
hover:bg-gray-50
@endactivemenu" data-dropdown-toggle="dropdown-menu-shift" data-tooltip-menu="Pengaturan Waktu">
                        <div class="flex items-center gap-2.5">
                            <span class="text-gray-600">
                                <x-icon icon="clock" width=18 height=18 viewBox="20 20" />
                            </span>
                            <p class=" {{ $is_mini ? 'hidden' : '' }} name-menu text-sm font-medium text-gray-600">
                                Pengaturan Waktu</p>
                        </div>
                        <span class="text-gray-500 chevron-icon-menu  {{ $is_mini ? 'hidden' : '' }} duration-300">
                            <x-icon icon="chevron-down" width=18 height=18 viewBox="20 20" />
                        </span>
                    </section>
                    <div id="dropdown-menu-shift" class="sub-menu-content {{ $is_mini ? 'hidden' : 'flex' }} flex-col gap-1
                                 @activemenu('shifts')
sub-menu-active
@else
hidden
@endactivemenu">
                        @can('break-time.view')
                        <a href="{{ route('break-time.index') }}" class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500
                                            @activemenu('break-time')
bg-gray-100 active
@else
hover:bg-gray-50
@endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="space-sub-menu w-5"></div>
                                <p class="text-sub-menu text-sm font-medium text-gray-600">
                                    Istirahat</p>
                            </div>
                        </a>
                        @endcan
                        @can('timetable.view')
                        <a href="{{ route('timetable.index') }}" class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                            @activemenu('timetable')
bg-gray-100 active
@else
hover:bg-gray-50
@endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="space-sub-menu w-5"></div>
                                <p class="text-sub-menu text-sm font-medium text-gray-600">
                                    Jadwal Shift</p>
                            </div>
                        </a>
                        @endcan
                        @can('shift.view')
                        <a href="{{ route('shift.index') }}" class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                            @activemenu('shift')
bg-gray-100 active
@else
hover:bg-gray-50
@endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="space-sub-menu w-5"></div>
                                <p class="text-sub-menu text-sm font-medium text-gray-600">
                                    Shift Bagian</p>
                            </div>
                        </a>
                        @endcan
                        @can('holiday.view')
                        <a href="{{ route('holiday.index') }}" class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                            @activemenu('holiday')
bg-gray-100 active
@else
hover:bg-gray-50
@endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="space-sub-menu w-5"></div>
                                <p class="text-sub-menu text-sm font-medium text-gray-600">
                                    Libur Nasional</p>
                            </div>
                        </a>
                        @endcan
                    </div>
                    @endcanany

                    @canany(['employee.view', 'resign.view', 'kasbon.view', 'transaction.view'])
                    <section class="my-dropdown-menu cursor-pointer flex items-center {{ $is_mini ? 'justify-center' : 'justify-between' }} p-2.5 rounded-lg w-full 
                                    @activemenu('employees')
bg-gray-100 active
@else
hover:bg-gray-50
@endactivemenu" data-dropdown-toggle="dropdown-menu-employee" data-tooltip-menu="Pengaturan Karyawan">
                        <div class="flex items-center gap-2.5">
                            <span class="text-gray-600">
                                <x-icon icon="users" width=18 height=18 viewBox="20 20" />
                            </span>
                            <p class=" {{ $is_mini ? 'hidden' : '' }} name-menu text-sm font-medium text-gray-600">
                                Pengaturan Karyawan</p>
                        </div>
                        <span class="text-gray-500 chevron-icon-menu  {{ $is_mini ? 'hidden' : '' }} duration-300">
                            <x-icon icon="chevron-down" width=18 height=18 viewBox="20 20" />
                        </span>
                    </section>
                    <div id="dropdown-menu-employee" class="sub-menu-content {{ $is_mini ? 'hidden' : 'flex' }} flex-col gap-1
                                 @activemenu('employees')
sub-menu-active
@else
hidden
@endactivemenu">
                        @can('employee.view')
                        <a href="{{ route('employee.index') }}" class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                            @activemenu('employee')
bg-gray-100 active
@else
hover:bg-gray-50
@endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="space-sub-menu w-5"></div>
                                <p class="text-sub-menu text-sm font-medium text-gray-600">
                                    Karyawan</p>
                            </div>
                        </a>
                        @endcan
                        @can('kasbon.view')
                        <a href="{{ route('kasbon.index') }}" class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                            @activemenu('kasbon')
bg-gray-100 active
@else
hover:bg-gray-50
@endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="space-sub-menu w-5"></div>
                                <p class="text-sub-menu text-sm font-medium text-gray-600">
                                    Kasbon</p>
                            </div>
                        </a>
                        @endcan
                        @can('attendance-manual.view')
                        <a href="{{ route('transaction.index') }}" class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                    @activemenu('transaction')
bg-gray-100 active
@else
hover:bg-gray-50
@endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="space-sub-menu w-5"></div>
                                <p class="text-sub-menu text-sm font-medium text-gray-600">
                                    Absensi karyawan</p>
                            </div>
                            @endcan
                            @can('resign.view')
                            <a href="{{ route('resign.index') }}" class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                            @activemenu('resign')
bg-gray-100 active
@else
hover:bg-gray-50
@endactivemenu">
                                <div class="flex items-center gap-2.5">
                                    <div class="space-sub-menu w-5"></div>
                                    <p class="text-sub-menu text-sm font-medium text-gray-600">
                                        Mengundurkan
                                        diri</p>
                                </div>
                            </a>
                            @endcan
                        </a>
                    </div>
                    @endcanany

                    @canany(['operational.view', 'request-help.view', 'request-task.view'])
                    <section class="my-dropdown-menu cursor-pointer flex items-center {{ $is_mini ? 'justify-center' : 'justify-between' }} p-2.5 rounded-lg w-full
                                    @activemenu('task-management')
bg-gray-100 active
@else
hover:bg-gray-50
@endactivemenu" data-dropdown-toggle="dropdown-menu-task-management" data-tooltip-menu="Pengaturan Tugas">
                        <div class="flex items-center gap-2.5">
                            <span class="text-gray-600">
                                <x-icon icon="board" width=18 height=18 viewBox="20 20" />
                            </span>
                            <p class=" {{ $is_mini ? 'hidden' : '' }} name-menu text-sm font-medium text-gray-600">
                                Pengaturan Tugas</p>
                        </div>
                        <span class="text-gray-500 chevron-icon-menu  {{ $is_mini ? 'hidden' : '' }} duration-300">
                            <x-icon icon="chevron-down" width=18 height=18 viewBox="20 20" />
                        </span>
                    </section>
                    <div id="dropdown-menu-task-management" class="sub-menu-content {{ $is_mini ? 'hidden' : 'flex' }} flex-col gap-1
                                 @activemenu('task-management')
sub-menu-active
@else
hidden
@endactivemenu">
                        @can('operational.view')
                        <a href="{{ route('operational.index') }}" class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                    @activemenu('operational')
bg-gray-100 active
@else
hover:bg-gray-50
@endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="space-sub-menu w-5"></div>
                                <p class="text-sub-menu text-sm font-medium text-gray-600">
                                    Manager Operational
                                </p>
                            </div>
                        </a>
                        @endcan
                        @can('request-help.view')
                        <a href="{{ route('request-help.index') }}" class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                    @activemenu('request-help')
bg-gray-100 active
@else
hover:bg-gray-50
@endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="space-sub-menu w-5"></div>
                                <p class="text-sub-menu text-sm font-medium text-gray-600">
                                    Karyawan LTH</p>
                            </div>
                        </a>
                        @endcan
                        @can('TSO.view')
                        <a href="{{ route('TSO.index') }}" class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                    @activemenu('TSO')
bg-gray-100 active
@else
hover:bg-gray-50
@endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="space-sub-menu w-5"></div>
                                <p class="text-sub-menu text-sm font-medium text-gray-600">
                                    Karyawan TSO</p>
                            </div>
                        </a>
                        @endcan
                        @can('request-task.view')
                        <a href="{{ route('request-task.index') }}" class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                    @activemenu('request-task')
bg-gray-100 active
@else
hover:bg-gray-50
@endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="space-sub-menu w-5"></div>
                                <p class="text-sub-menu text-sm font-medium text-gray-600">
                                    Penugasan</p>
                            </div>
                        </a>
                        @endcan
                    </div>
                    @endcanany

                    @canany(['attendance-report.view', 'payroll-report.view', 'overtime-rice-report.view'])
                    <section class="my-dropdown-menu cursor-pointer flex items-center {{ $is_mini ? 'justify-center' : 'justify-between' }} p-2.5 rounded-lg w-full
                                    @activemenu('reports')
bg-gray-100 active
@else
hover:bg-gray-50
@endactivemenu" data-dropdown-toggle="dropdown-menu-reports" data-tooltip-menu="Laporan">
                        <div class="flex items-center gap-2.5">
                            <span class="text-gray-600">
                                <x-icon icon="file" width=18 height=18 viewBox="20 20" />
                            </span>
                            <p class=" {{ $is_mini ? 'hidden' : '' }} name-menu text-sm font-medium text-gray-600">
                                Laporan</p>
                        </div>
                        <span class="text-gray-500 chevron-icon-menu  {{ $is_mini ? 'hidden' : '' }} duration-300">
                            <x-icon icon="chevron-down" width=18 height=18 viewBox="20 20" />
                        </span>
                    </section>
                    <div id="dropdown-menu-reports" class="sub-menu-content {{ $is_mini ? 'hidden' : 'flex' }} flex-col gap-1
                                 @activemenu('reports')
sub-menu-active
@else
hidden
@endactivemenu">
                        @can('attendance-report.view')
                        <a href="{{ route('attendance-report.index') }}" class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                    @activemenu('attendance-report')
bg-gray-100 active
@else
hover:bg-gray-50
@endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="space-sub-menu w-5"></div>
                                <p class="text-sub-menu text-sm font-medium text-gray-600">
                                    Absensi</p>
                            </div>
                        </a>
                        @endcan
                        @can('attendance-card.view')
                        <a href="{{ route('attendance-card.index') }}" class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                    @activemenu('attendance-card')
bg-gray-100 active
@else
hover:bg-gray-50
@endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="space-sub-menu w-5"></div>
                                <p class="text-sub-menu text-sm font-medium text-gray-600">
                                    Kartu absensi</p>
                            </div>
                        </a>
                        @endcan
                        @can('attendance-operational.view')
                        <a href="{{ route('attendance-operational.index') }}" class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                    @activemenu('attendance-operational')
bg-gray-100 active
@else
hover:bg-gray-50
@endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="space-sub-menu w-5"></div>
                                <p class="text-sub-menu text-sm font-medium text-gray-600">
                                    Kartu absensi operasional</p>
                            </div>
                        </a>
                        @endcan
                        @can('payroll-report.view')
                        <a href="{{ route('payroll-report.index') }}" class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500
                                            @activemenu('payroll-report')
bg-gray-100 active
@else
hover:bg-gray-50
@endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="space-sub-menu w-5"></div>
                                <p class="text-sub-menu text-sm font-medium text-gray-600">
                                    Penggajian</p>
                            </div>
                        </a>
                        @endcan
                        @can('overtime-rice-report.view')
                        <a href="{{ route('overtime-rice-report.index') }}" class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500
                                            @activemenu('overtime-rice-report')
bg-gray-100 active
@else
hover:bg-gray-50
@endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="space-sub-menu w-5"></div>
                                <p class="text-sub-menu text-sm font-medium text-gray-600">
                                    Tagihan nasi lembur
                                </p>
                            </div>
                        </a>
                        @endcan
                        <a href="{{ route('transaction.index') }}" class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500
                                        @activemenu('transaction-report')
bg-gray-100 active
@else
hover:bg-gray-50
@endactivemenu">
                            <div class="flex items-center gap-2.5">
                                <div class="space-sub-menu w-5"></div>
                                <p class="text-sub-menu text-sm font-medium text-gray-600">
                                    Log Absensi</p>
                            </div>
                        </a>
                    </div>
                    @endcanany
                </main>
            </div>
            <hr>
            <footer class="{{ !$is_mini ? 'hidden' : 'flex' }} items-center justify-center pb-4 w-full footer-mini">
                <div class="flex items-center gap-2 flex-col">
                    <div class="w-10 h-10 rounded-full bg-gray-100 overflow-hidden">
                        @if (!empty(Auth::user()->photo))
                        <img src="{{ Auth::user()->photo }}" alt="" class="w-full h-full object-cover">
                        @else
                        <img src="@zkPhoto(files / nophoto . gif)" alt="" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <div class="flex flex-col items-center justify-center">
                        <p class="text-center text-gray-900 font-medium text-xs w-12 truncate break-words">
                            {{ Auth::user()->username }}</p>
                        <form action="{{ route('logout') }}">
                            {{ csrf_field() }}
                            <button class="text-gray-500 cursor-pointer">
                                <p class="text-gray-500 runcate text-xs">Logout</p>
                            </button>
                        </form>
                    </div>
                </div>
            </footer>
            <footer
                class="{{ $is_mini ? 'hidden' : 'flex' }} items-center justify-between pb-8 w-full footer-full-size">
                <div class="flex items-center gap-2 ">
                    <div class="w-10 h-10 rounded-full bg-gray-100 overflow-hidden">
                        @if (!empty(Auth::user()->photo))
                        <img src="{{ Auth::user()->photo }}" alt="" class="w-full h-full object-cover">
                        @else
                        <img src="@zkPhoto(files / nophoto . gif)" alt="" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <div class="flex flex-col justify-center text-sm">
                        <p class="text-gray-900 font-medium text-sm truncate w-28">
                            {{ Auth::user()->username }}</p>
                        <p class="text-gray-500 runcate w-28 text-sm">{{ Auth::user()->email }}</p>
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
<script type="module">
    Dropzone.autoDiscover = false;
    Echo.channel(`hello`)
        .listen('HelloEvent', (e) => {
            console.log(e);
        });

</script>
<script type="application/javascript">
    const API = "{{ config('constants.api') }}";
    window.addEventListener('DOMContentLoaded', (event) => {
        $(document).ajaxSend(function(event, request, settings) {
            console.log('send')
        });

        $(document).ajaxComplete(function(event, request, settings) {
            console.log('ajaxComplete')
        });
        $('#switch-size-menu').on('click',function(e) {
            if($('#aside-navigation').hasClass('is-full-size')) {
                $('.footer-full-size').toggle('flex');
                $('.footer-mini').toggle('flex');
                $.cookie('side_menu_is_mini', true);
                $('.sub-menu-content').each(function(e) {
                    if (!$(this).is(':hidden')) $(this).toggle();
                })
                $(this).animate({left: 70}, 300 );
                $('.my-dropdown-menu').addClass('justify-center');
                $('.my-dropdown-menu').removeClass('justify-between');
                $('.chevron-left-switch-size-menu').addClass('rotate-180');
                $('#aside-navigation').removeClass('is-full-size');
                $('#aside-navigation').animate({width: 82}, 300 );
                $('.name-business').toggle()
                $('.name-menu').toggle()
                $('.chevron-icon-menu').toggle();

                $('.my-dropdown-menu').off("mouseenter mouseleave click");
                dropdown_menu_click_mini();
            } else {
                $('.footer-mini').toggle('flex');
                $.cookie('side_menu_is_mini',false)
                $(this).animate({left: 248}, 300 );
                $('.my-dropdown-menu').removeClass('justify-center');
                $('.my-dropdown-menu').addClass('justify-between');
                $('.chevron-left-switch-size-menu').removeClass('rotate-180');
                $('#aside-navigation').addClass('is-full-size');
                $('#aside-navigation').animate({width: 260}, 
                    {
                        duration: 500,
                        complete: function(){
                            $('.footer-full-size').toggle('flex');
                            $('.name-business').toggle('flex')
                            $('.name-menu').toggle()
                            $('.chevron-icon-menu').toggle()
                            $('.sub-menu-content').each(function(e) {
                                if($(this).hasClass('sub-menu-active')) {
                                    $(this).toggle();
                                }
                            })
                        }
                    }
                )

                hide_dropdown_menu();
                $('.my-dropdown-menu').off("mouseenter mouseleave click");
                dropdown_menu_click_full_size()
            }
        })

        if($.cookie('side_menu_is_mini') == 'true') {
            dropdown_menu_click_mini();
        } else {
            dropdown_menu_click_full_size();
        }

    });

    // function set_loading_line(status) {
    //     switch (status) {
    //         case 'pending':
    //         $('#loading-line').removeClass('hidden');
    //         $('#loading-line').addClass('flex');
    //         $('#loading-line').width('20%');
    //             break;
    //         case 'finish':
    //         $('#loading-line').width('100%');
    //         setTimeout(() => {
    //             $('#loading-line').addClass('hidden');
    //             $('#loading-line').width('0');
    //         }, 2000);
    //             break;
    //     }
        
    // }

    function dropdown_menu_click_full_size(params) {
        $('.my-dropdown-menu').each(function (e) {
            $(this).on('click', function(e) {
                let currentSubMenuClass = $(this).data('dropdown-toggle');
                $('#'+ currentSubMenuClass).toggle('hidden');
                if($(this).hasClass('active')) {
                    $('#'+ currentSubMenuClass).removeClass('sub-menu-active')
                    $(this).removeClass('active bg-gray-100');
                    $(this).children('.chevron-icon-menu').removeClass('rotate-180')
                } else {
                    $('#'+ currentSubMenuClass).addClass('sub-menu-active')
                    $(this).addClass('active bg-gray-100');
                    $(this).children('.chevron-icon-menu').addClass('rotate-180')
                }
            })
        });
    }
    function dropdown_menu_click_mini(params) {
        $('.my-dropdown-menu').each(function (e) {
            $(this).mouseenter(function () {
                let position_element = $(this).position();
                var $div = $(
                    `<div class="tooltip-menu absolute border rounded border-gray-200 text-gray-500 text-[10px] backdrop-blur-[3px] py-0.5 px-1.5">
                        ${ $(this).data('tooltip-menu') ?? '-'}
                    </div>`
                ).appendTo('body');
                $div.css({ 
                    position: "absolute",
                    display: 'block',
                    top: position_element.top + ($(this).outerHeight() / 4), left: position_element.left + $(this).outerWidth()+ 5,
                });
            });

            $(this).mouseleave(function (e) {
                $('.tooltip-menu').remove()
            });

            $(this).on('click', function(e) {
                $(document).on("click",function(e){
                    if ($(e.target).closest('#dropdown-menu').length)
                        return;
                    if ($(e.target).closest('.my-dropdown-menu').length)
                        return;
                    hide_dropdown_menu();
                });
                let position_element = $(this).position();
                let id_content_dropdown = $(this).data('dropdown-toggle');
                if(id_content_dropdown) {
                    if($('#dropdown-menu').hasClass('active-dropdown')) {
                        hide_dropdown_menu();
                    } else {
                        $('#dropdown-menu').html($('#' + id_content_dropdown).html());
                        $('#dropdown-menu').find('.space-sub-menu').hide();
                        $('#dropdown-menu').find('.text-sub-menu').removeClass('text-sm');
                        $('#dropdown-menu').find('.text-sub-menu').addClass('text-xs');
                        $('#dropdown-menu').addClass('active-dropdown');
                        $('#dropdown-menu').css({ 
                            position: "absolute",
                            display: 'block',
                            top: position_element.top+$(this).outerHeight()+5, left: position_element.left,
                        });
                    }
                }
            });
        })
    }


    function hide_dropdown_menu() {
        $('#dropdown-menu').html('');
        $('#dropdown-menu').removeClass('active-dropdown');
        $('#dropdown-menu').css({ 
            position: "absolute",
            display: 'none',
            top: 0, left: 0,
        });
    }

    // function resetSortTable() {
    //     $('.sort-table').each(function(e) {
    //         $(this).removeClass('active');
    //         $(this).children('.sort-icon').removeClass('rotate-180');
    //     })
    // }

    // function sort_data(event) {
    //     let sortKey = $(event).data('sort-key');
    //     let sortUrl = $(event).data('sort-url');
    //     let isActive = $(event).hasClass('active');

    //     // Reset sort table
    //     resetSortTable();
    //     // Build Data sort table
    //     let field = { q: $('.search-data-input').val(), };
    //     if(!isActive) {
    //         field.sort = { name: sortKey, order: (isActive) ? 'DESC': 'ASC'}
    //     }
    //     // Get Data sort table
    //     get_data_table(sortUrl, field);
    // }

    // async function get_data_table(url, data) {
    //     var res = await ApiService.get_table(url, data );
    //     $('.table-content').html(res);
    //     $('.select2-page').on('select2:select', function (e) {
    //         delete dataParams.page;
    //         onInit({page_size: $(this).val()})
    //     });  
    // }
</script>

</html>