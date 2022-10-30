<!-- component -->
@if (request()->segment(2) != 'register')
@if(Auth::user())
<div class="bg-white no-scrollbar h-screen w-56 z-10 flex flex-col shadow relative overflow-y-auto">
    {{-- <div
        class="absolute right-[-13px] top-[24px] text-gray-500 bg-white shadow-lg rounded-full p-1.5 border border-gray-100">
        <x-icon icon="arrow-left" width=14 height=14 viewBox="20 20" />
    </div> --}}
    <div class="flex flex-col items-center justify-center gap-2 px-2.5 pt-8 pb-5">
        {{-- <img src="{{( Auth::user()->business->logo) ? asset('storage/business_logos/'.Auth::user()->business->logo.''): '' }}"
            alt="" class="w-12 h-12 object-contain  min-w-[48px] min-h-[48px] rounded-full">
        <p class="font-bold text-xs text-gray-700">{{ Auth::user()->business->name }}</p> --}}
    </div>
    <main class=" w-full flex-1 flex flex-col justify-between items-center ">
        <section class="w-full">
            <ul class="w-full flex flex-col">
                @include('partials.menus.dashboard')
                @include('partials.menus.organization')
                @include('partials.menus.user')
                @include('partials.menus.shift')
                @include('partials.menus.business')
                {{-- <li>
                    <a href="{{ route('work-sections.index') }}" class="w-full h-8 flex items-center gap-3">
                        <div class="{{ (request()->segment(1) == 'work-sections') ? 'bg-gray-100 text-violet-700'  : 'text-gray-500'}} 
                            rounded-r-lg h-full flex items-center justify-end w-10 pr-1.5">
                            <x-icon icon="file" width=14 height=14 viewBox="20 20" />
                        </div>
                        <p class="{{ (request()->segment(1) == 'work-sections')? 'text-violet-700': 'text-gray-500' }} 
                            text-xs font-medium">Work section</p>
                    </a>
                </li>
                <li>
                    <a href="{{ route('holidays.index') }}" class="w-full h-8 flex items-center gap-3">
                        <div class="{{ (request()->segment(1) == 'holidays') ? 'bg-gray-100 text-violet-700'  : 'text-gray-500'}} 
                            rounded-r-lg h-full flex items-center justify-end w-10 pr-1.5">
                            <x-icon icon="sun" width=14 height=14 viewBox="20 20" />
                        </div>
                        <p class="{{ (request()->segment(1) == 'holidays')? 'text-violet-700': 'text-gray-500' }} 
                            text-xs font-medium">Holiday</p>
                    </a>
                </li>
                <li>
                    <a href="{{ route('employees.index') }}" class="w-full h-8 flex items-center gap-3">
                        <div class="{{ (request()->segment(1) == 'employees') ? 'bg-gray-100 text-violet-700'  : 'text-gray-500'}} 
                            rounded-r-lg h-full flex items-center justify-end w-10 pr-1.5">
                            <x-icon icon="users" width=14 height=14 viewBox="20 20" />
                        </div>
                        <p class="{{ (request()->segment(1) == 'employees')? 'text-violet-700': 'text-gray-500' }} 
                            text-xs font-medium">Employees</p>
                    </a>
                </li>
                <li>
                    <a href="{{ route('groups.index') }}" class="w-full h-8 flex items-center gap-3">
                        <div class="{{ (request()->segment(1) == 'groups') ? 'bg-gray-100 text-violet-700'  : 'text-gray-500'}} 
                            rounded-r-lg h-full flex items-center justify-end w-10 pr-1.5">
                            <x-icon icon="grid" width=14 height=14 viewBox="20 20" />
                        </div>
                        <p class="{{ (request()->segment(1) == 'groups')? 'text-violet-700': 'text-gray-500' }} 
                            text-xs font-medium">Group</p>
                    </a>
                </li>
                <li>
                    <a href="{{ route('locations.index') }}" class="w-full h-8 flex items-center gap-3">
                        <div class="{{ (request()->segment(2) == 'locations') ? 'bg-gray-100 text-violet-700'  : 'text-gray-500'}} 
                            rounded-r-lg h-full flex items-center justify-end w-10 pr-1.5">
                            <x-icon icon="map-pin" width=14 height=14 viewBox="20 20" />
                        </div>
                        <p class="{{ (request()->segment(2) == 'locations')? 'text-violet-700': 'text-gray-500' }} 
                            text-xs font-medium">Business location</p>
                    </a>
                </li>
                <li>
                    <a href="{{ route('business.index.settings') }}" class="w-full h-8 flex items-center gap-3">
                        <div class="{{ (request()->segment(2) == 'settings') ? 'bg-gray-100 text-violet-700'  : 'text-gray-500'}} 
                            rounded-r-lg h-full flex items-center justify-end w-10 pr-1.5">
                            <x-icon icon="monitor" width=14 height=14 viewBox="20 20" />
                        </div>
                        <p class="{{ (request()->segment(2) == 'settings')? 'text-violet-700': 'text-gray-500' }} 
                            text-xs font-medium">Business settings</p>
                    </a>
                </li> --}}
            </ul>
        </section>
        <footer class="flex items-center justify-between px-2.5 py-8 w-full">
            <div class="flex items-center gap-2 ">
                <div class="w-8 h-8 rounded-lg bg-gray-100 overflow-hidden">
                    <img src="{{( Auth::user()->photo) ? asset('storage/profiles/'.Auth::user()->photo.''): '' }}"
                        alt="" class="w-full h-full object-cover">
                </div>
                <div class="flex flex-col justify-center text-sm">
                    <p class="text-gray-900 font-medium text-xs truncate w-28">{{ Auth::user()->surname }} {{
                        Auth::user()->first_name }} {{ Auth::user()->last_name }} </p>
                    <p class="text-gray-500 runcate w-28 text-xs">{{ Auth::user()->username }}</p>
                </div>
            </div>
            <form action="{{ route('logout') }}">
                {{ csrf_field() }}
                <button class="text-gray-500 mt-2.5 cursor-pointer">
                    <x-icon icon="log-out" width=14 height=14 viewBox="20 20" />
                </button>
            </form>
        </footer>
    </main>
</div>
<script type="application/javascript">
    window.addEventListener('DOMContentLoaded', (event) => {
        $('.myDropdownMenu').each(function (e) {
            $(this).on('click', function(e) {
                let currentSubMenuClass = $(this).data('dropdown-toggle');
                let childParent = $(this).children().first().children();
                $('#'+ currentSubMenuClass).toggle('hidden');

                if($(this).hasClass('bg-violet-50')) {
                    $(this).removeClass('bg-violet-50')
                    childParent.find('.text-menu').removeClass('text-violet-700')
                    childParent.find('.icon-menu').removeClass('text-violet-700')
                    childParent.find('.text-menu').addClass('text-gray-500')
                    childParent.find('.icon-menu').addClass('text-gray-500')
                    $(this).children().last().removeClass('rotate-180 text-violet-700')
                    $(this).children().last().addClass('text-gray-500')
                    $(this).children().first().children('.line-left-menu').removeClass('bg-violet-700')
                } else {
                    $(this).addClass('bg-violet-50')
                    childParent.find('.text-menu').addClass('text-violet-700')
                    childParent.find('.icon-menu').addClass('text-violet-700')
                    childParent.find('.text-menu').removeClass('text-gray-500')
                    childParent.find('.icon-menu').removeClass('text-gray-500')
                    $(this).children().last().addClass('rotate-180 text-violet-700')
                    $(this).children().last().removeClass('text-gray-500')
                    $(this).children().first().children('.line-left-menu').addClass('bg-violet-700')
                }
            })
        });
    });
</script>
@endif
@endif