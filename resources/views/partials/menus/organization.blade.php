<li>
    <button type="button" data-dropdown-toggle="dropdown-menu-organization" class="{{ in_array(request()->segment(1), ['department', 'position', 'area']) ? 'bg-violet-50' : '' }} 
        myDropdownMenu group w-full flex items-center justify-between pr-4 hover:bg-violet-50 py-1">
        <div class="flex items-center h-8">
            <span class="{{ in_array(request()->segment(1), ['department', 'position', 'area']) ? 'bg-violet-700' : '' }} 
                flex h-8 w-5 rounded-[3px] transform -translate-x-4 line-left-menu"></span>
            <div class="flex items-center gap-3 h-full">
                <span class="{{ in_array(request()->segment(1), ['department', 'position', 'area']) ? 'text-violet-700' : 'text-gray-500' }} 
                    rounded-r-md h-full flex items-center justify-end icon-menu">
                    <x-icon icon="git-pull-request" width=16 height=16 viewBox="20 20" />
                </span>
                <p class="{{ in_array(request()->segment(1), ['department', 'position', 'area']) ? 'text-violet-700' : 'text-gray-500' }} 
                    text-xs font-medium group-hover:text-violet-700 text-menu">
                    Organization
                </p>
            </div>
        </div>
        <span
            class="{{ in_array(request()->segment(1), ['department', 'position', 'area']) ? 'text-violet-700 rotate-180' : 'text-gray-500 ' }} group-hover:text-violet-700">
            <x-icon icon="chevron-down" width=14 height=14 viewBox="20 20" />
        </span>
    </button>
    <div class="w-full bg-gray-50">
        <ul id="dropdown-menu-organization"
            class="{{ in_array(request()->segment(1), ['department', 'position', 'area']) ? 'w-full bg-gray-50' : 'hidden' }}">
            <li>
                <a href="{{ route('department.index') }}"
                    class="{{ (request()->segment(1) == 'department') ? 'bg-violet-50'  : ''}} group w-full flex items-center justify-between pr-4 hover:bg-violet-50 py-1">
                    <div class="flex items-center h-8 w-full">
                        <span class="flex h-8 w-5 rounded-[3px] transform -translate-x-4"></span>
                        <div class="text-gray-500 flex items-center gap-3 h-full flex-1">
                            <span class="{{ (request()->segment(1) == 'department') ? 'text-violet-700'  : 'text-gray-500'}}
                                rounded-r-md h-full flex items-center justify-end group-hover:text-violet-700">
                                <x-icon icon="corner-down-right" width=12 height=12 viewBox="20 20" />
                            </span>
                            <p class="{{ (request()->segment(1) == 'department') ? 'text-violet-700'  : 'text-gray-500'}}
                                text-gray-500 text-xs font-medium group-hover:text-violet-700 py-0.5 px-1">
                                Department
                            </p>
                        </div>
                    </div>
                </a>
            </li>
            <li>
                <a href="{{ route('position.index') }}"
                    class="{{ (request()->segment(1) == 'position') ? 'bg-violet-50'  : ''}} group w-full flex items-center justify-between pr-4 hover:bg-violet-50 py-1">
                    <div class="flex items-center h-8 w-full">
                        <span class="flex h-8 w-5 rounded-[3px] transform -translate-x-4"></span>
                        <div class="text-gray-500 flex items-center gap-3 h-full flex-1">
                            <span
                                class="{{ (request()->segment(1) == 'position') ? 'text-violet-700'  : 'text-gray-500'}} rounded-r-md h-full flex items-center justify-end group-hover:text-violet-700">
                                <x-icon icon="corner-down-right" width=12 height=12 viewBox="20 20" />
                            </span>
                            <p
                                class="{{ (request()->segment(1) == 'position') ? 'text-violet-700'  : 'text-gray-500'}} text-gray-500 text-xs font-medium group-hover:text-violet-700 py-0.5 px-1">
                                Position
                            </p>
                        </div>
                    </div>
                </a>
            </li>
            <li>
                <a href="{{ route('area.index') }}"
                    class="{{ (request()->segment(1) == 'area') ? 'bg-violet-50'  : ''}} group w-full flex items-center justify-between pr-4 hover:bg-violet-50 py-1">
                    <div class="flex items-center h-8">
                        <span class="flex h-8 w-5 rounded-[3px] transform -translate-x-4 "></span>
                        <div class="text-gray-500 flex items-center gap-3 h-full">
                            <span
                                class="{{ (request()->segment(1) == 'area') ? 'text-violet-700'  : 'text-gray-500'}} rounded-r-md h-full flex items-center justify-end group-hover:text-violet-700 ">
                                <x-icon icon="corner-down-right" width=12 height=12 viewBox="20 20" />
                            </span>
                            <p
                                class="{{ (request()->segment(1) == 'area') ? 'text-violet-700'  : 'text-gray-500'}} text-gray-500 text-xs font-medium group-hover:text-violet-700 py-0.5 px-1">
                                Area
                            </p>
                        </div>
                    </div>
                </a>
            </li>
        </ul>
    </div>
</li>