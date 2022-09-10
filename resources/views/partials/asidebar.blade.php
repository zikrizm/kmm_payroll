<!-- component -->
@if (request()->segment(2) != 'register')
@if(Auth::user())
<div class="bg-white no-scrollbar h-screen w-60 z-10 flex flex-col shadow relative">
    <div class="absolute right-[-15px] top-[24px] text-gray-500 bg-white shadow-lg rounded-full p-1.5 border border-gray-100">
        <x-icon icon="arrow-left" width=18 height=18 viewBox="20 20" />
        {{-- <x-icon icon="arrow-right" width=18 height=18 viewBox="20 20" /> --}}
    </div>
    <div class="flex flex-col items-center justify-center gap-2 px-2.5 py-8">
        {{-- <span class="text-indigo-700">
            <x-icon icon="codesandbox" width=40 height=40 viewBox="20 20" />
        </span>
        <p class="font-bold text-base">PT KENCANA MAS MULIA</p> --}}
    </div>
    <main class="w-full flex-1 flex flex-col justify-between items-center ">
        <section class="w-full">
            {{-- <ul class="w-full flex flex-col gap-2">
                <li>
                    <a href="{{ route('home.index') }}" class="w-full h-10 flex items-center gap-3">
                        <div class="rounded-r-lg h-full flex items-center justify-end w-14 pr-1.5 text-gray-500">
                            <x-icon icon="home" width=20 height=20 viewBox="20 20" strokeWidth=3 />
                        </div>
                        <p class="text-base text-gray-500 font-medium">Dashboard</p>
                    </a>
                </li>
                <li>
                    <a href="{{ route('users.index') }}" class="w-full h-10 flex items-center gap-3">
                        <div
                            class="bg-gray-100 rounded-r-lg h-full flex items-center justify-end w-14 pr-1.5 text-indigo-700">
                            <x-icon icon="user" width=20 height=20 viewBox="20 20" strokeWidth=3 />
                        </div>
                        <p class="text-base font-medium text-indigo-700">User management</p>
                    </a>
                </li>
                <li>
                    <a href="{{ route('access-control.index') }}" class="w-full h-10 flex items-center gap-3">
                        <div class="rounded-r-lg h-full flex items-center justify-end w-14 pr-1.5 text-gray-500">
                            <x-icon icon="sliders" width=20 height=20 viewBox="20 20" strokeWidth=3 />
                        </div>
                        <p class="text-base text-gray-500 font-medium">Access control</p>
                    </a>
                </li>
            </ul> --}}
        </section>
        <footer class="flex items-center justify-between px-6 py-8 w-full">
            <div class="flex items-center gap-2 ">
                <div class="w-11 h-11 rounded-lg bg-gray-100 overflow-hidden">
                    <img src="" alt="" class="w-full h-full object-cover">
                </div>
                <div class="flex flex-col justify-center text-sm">
                    <p class="text-gray-900 font-medium">Easin Arafat</p>
                    <p class="text-gray-500">Free Account</p>
                </div>
            </div>
            <form action="{{ route('logout') }}">
                {{ csrf_field() }}
                <button class="text-gray-500">
                    <x-icon icon="log-out" width=20 height=20 viewBox="20 20" strokeWidth=2.5 />
                </button>
            </form>
        </footer>
    </main>
    <!-- Dropdown Profile -->

</div>
@endif
@endif