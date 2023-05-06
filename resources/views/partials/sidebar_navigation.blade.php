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
                    <p class="text-sm font-medium text-gray-600">Dashboard</p>
                </div>
            </a>
            @canany(['department.view', 'position.view', 'area.view', 'device.view'])
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
                        <p class="text-sm font-medium text-gray-600">Perusahaan</p>
                    </div>
                </a>
                @can('department.view')
                <a href="{{ route('department.index') }}"
                    class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500
                                            @activemenu('department') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu">
                    <div class="flex items-center gap-2.5">
                        <div class="w-5"></div>
                        <p class="text-sm font-medium text-gray-600">Bagian karyawan</p>
                    </div>
                </a>
                @endcan
                @can('position.view')
                <a href="{{ route('position.index') }}"
                    class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500
                                            @activemenu('position') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu">
                    <div class="flex items-center gap-2.5">
                        <div class="w-5"></div>
                        <p class="text-sm font-medium text-gray-600">Jabatan karyawan</p>
                    </div>
                </a>
                @endcan
                @can('area.view')
                <a href="{{ route('area.index') }}"
                    class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500
                                            @activemenu('area') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu">
                    <div class="flex items-center gap-2.5">
                        <div class="w-5"></div>
                        <p class="text-sm font-medium text-gray-600">Area karyawan</p>
                    </div>
                </a>
                @endcan
                <a href="{{ route('device.index') }}"
                    class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                        @activemenu('device') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu">
                    <div class="flex items-center gap-2.5">
                        <div class="w-5"></div>
                        <p class="text-sm font-medium text-gray-600">Perangkat absensi</p>
                    </div>
                </a>
            </div>
            @endcanany

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
            <div id="dropdown-menu-user" class="flex flex-col gap-1 @activemenu('users') @else hidden @endactivemenu">
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
                <a href="{{ route('activity-log.index') }}"
                    class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500
                                            @activemenu('activity-log') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu">
                    <div class="flex items-center gap-2.5">
                        <div class="w-5"></div>
                        <p class="text-sm font-medium text-gray-600">Aktifitas pengguna</p>
                    </div>
                </a>
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
                        <p class="text-sm font-medium text-gray-600">Jadwal Shift</p>
                    </div>
                </a>
                @endcan
                @can('shift.view')
                <a href="{{ route('shift.index') }}"
                    class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                            @activemenu('shift') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu">
                    <div class="flex items-center gap-2.5">
                        <div class="w-5"></div>
                        <p class="text-sm font-medium text-gray-600">Shift Bagian</p>
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

            @canany(['employee.view', 'resign.view', 'kasbon.view', 'transaction.view'])
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
                @can('attendance-manual.view')
                <a href="{{ route('transaction.index') }}"
                    class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                    @activemenu('transaction') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu">
                    <div class="flex items-center gap-2.5">
                        <div class="w-5"></div>
                        <p class="text-sm font-medium text-gray-600">Absensi karyawan</p>
                    </div>
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
                </a>
            </div>
            @endcanany

            @canany(['operational.view', 'request-help.view', 'request-task.view'])
            <section
                class="my-dropdown-menu flex items-center justify-between p-2.5 rounded-lg w-full
                                    @activemenu('task-management') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu"
                data-dropdown-toggle="dropdown-menu-task-management">
                <div class="flex items-center gap-2.5">
                    <span class="text-gray-600">
                        <x-icon icon="board" width=18 height=18 viewBox="20 20" />
                    </span>
                    <p class="text-sm font-medium text-gray-600">Pengaturan Tugas</p>
                </div>
                <span class="text-gray-500 chevron-icon duration-300">
                    <x-icon icon="chevron-down" width=18 height=18 viewBox="20 20" />
                </span>
            </section>
            <div id="dropdown-menu-task-management" class="flex flex-col gap-1
                                 @activemenu('task-management') @else hidden @endactivemenu">
                @can('operational.view')
                <a href="{{ route('operational.index') }}"
                    class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                    @activemenu('operational') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu">
                    <div class="flex items-center gap-2.5">
                        <div class="w-5"></div>
                        <p class="text-sm font-medium text-gray-600">Manager Operational</p>
                    </div>
                </a>
                @endcan
                @can('request-help.view')
                <a href="{{ route('request-help.index') }}"
                    class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                    @activemenu('request-help') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu">
                    <div class="flex items-center gap-2.5">
                        <div class="w-5"></div>
                        <p class="text-sm font-medium text-gray-600">Karyawan LTH</p>
                    </div>
                </a>
                @endcan
                @can('request-task.view')
                <a href="{{ route('request-task.index') }}"
                    class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                    @activemenu('request-task') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu">
                    <div class="flex items-center gap-2.5">
                        <div class="w-5"></div>
                        <p class="text-sm font-medium text-gray-600">Penugasan</p>
                    </div>
                </a>
                @endcan
            </div>
            @endcanany

            @canany(['attendance-report.view', 'payroll-report.view', 'ot-rice-bill.view'])
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
                @can('attendance-card.view')
                <a href="{{ route('attendance-card.index') }}"
                    class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                    @activemenu('attendance-card') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu">
                    <div class="flex items-center gap-2.5">
                        <div class="w-5"></div>
                        <p class="text-sm font-medium text-gray-600">Kartu absensi</p>
                    </div>
                </a>
                @endcan
                @can('attendance-card.view')
                <a href="{{ route('attendance-card.index') }}"
                    class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500 
                                    @activemenu('attendance-card') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu">
                    <div class="flex items-center gap-2.5">
                        <div class="w-5"></div>
                        <p class="text-sm font-medium text-gray-600">Kartu absensi operasional</p>
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
                @can('ot-rice-bill.view')
                <a href="{{ route('ot-rice-bill.index') }}"
                    class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500
                                            @activemenu('ot-rice-bill') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu">
                    <div class="flex items-center gap-2.5">
                        <div class="w-5"></div>
                        <p class="text-sm font-medium text-gray-600">Tagihan nasi lembur</p>
                    </div>
                </a>
                @endcan
                <a href="{{ route('transaction.index') }}"
                    class="flex items-center justify-between p-2.5 rounded-lg w-full hover:underline hover:decoration-gray-500
                                        @activemenu('transaction-report') bg-gray-100 active @else hover:bg-gray-50 @endactivemenu">
                    <div class="flex items-center gap-2.5">
                        <div class="w-5"></div>
                        <p class="text-sm font-medium text-gray-600">Log Absensi</p>
                    </div>
                </a>
            </div>
            @endcanany


            {{-- @canany(['business_settings.access'])
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
            @endcanany --}}
        </main>
    </div>
    <hr>
    <footer class="flex items-center justify-between pb-8 w-full">
        <div class="flex items-center gap-2 ">
            <div class="w-10 h-10 rounded-full bg-gray-100 overflow-hidden">
                @if (!empty( Auth::user()->photo ))
                <img src="{{ Auth::user()->photo }}" alt="" class="w-full h-full object-cover">
                @else
                <img src="{{config('constants.api_zkteco')}}/files/nophoto.gif" alt="" class="w-full h-full object-cover">
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