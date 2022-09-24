<li>
    <button type="button" data-dropdown-toggle="dropdown-menu-user" class="{{ in_array(request()->segment(1), ['users', 'access-controls']) ? 'bg-violet-50' : '' }} 
        myDropdownMenu group w-full flex items-center justify-between pr-4 hover:bg-violet-50 py-1">
        <div class="flex items-center h-8">
            <span class="{{ in_array(request()->segment(1), ['users', 'access-controls']) ? 'bg-violet-700' : '' }} 
                flex h-8 w-5 rounded-[3px] transform -translate-x-4 line-left-menu"></span>
            <div class="flex items-center gap-3 h-full">
                <span class="{{ in_array(request()->segment(1), ['users', 'access-controls']) ? 'text-violet-700' : 'text-gray-500' }} 
                    rounded-r-md h-full flex items-center justify-end group-hover:text-violet-700 icon-menu">
                    <x-icon icon="user" width=16 height=16 viewBox="20 20" />
                </span>
                <p class="{{ in_array(request()->segment(1), ['users', 'access-controls']) ? 'text-violet-700' : 'text-gray-500' }} 
                    text-xs font-medium group-hover:text-violet-700 text-menu">
                    User
                </p>
            </div>
        </div>
        <span
            class="{{ in_array(request()->segment(1), ['users', 'access-controls']) ? 'text-violet-700 rotate-180' : 'text-gray-500 ' }} duration-30 group-hover:text-violet-700">
            <x-icon icon="chevron-down" width=14 height=14 viewBox="20 20" />
        </span>
    </button>
    <div class="w-full bg-gray-50">
        <ul id="dropdown-menu-user"
            class="{{ in_array(request()->segment(1), ['users', 'access-controls']) ? 'w-full bg-gray-50' : 'hidden' }}">
            <li>
                <a href="{{ route('users.index') }}"
                    class="{{ (request()->segment(1) == 'users') ? 'bg-violet-50'  : ''}} group w-full flex items-center justify-between pr-4 hover:bg-violet-50 py-1">
                    <div class="flex items-center h-8 w-full">
                        <span class="flex h-8 w-5 rounded-[3px] transform -translate-x-4"></span>
                        <div class="text-gray-500 flex items-center gap-3 h-full flex-1">
                            <span
                                class="{{ (request()->segment(1) == 'users') ? 'text-violet-700'  : 'text-gray-500'}} rounded-r-md h-full flex items-center justify-end group-hover:text-violet-700">
                                <x-icon icon="corner-down-right" width=12 height=12 viewBox="20 20" />
                            </span>
                            <p
                                class="{{ (request()->segment(1) == 'users') ? 'text-violet-700'  : 'text-gray-500'}} text-gray-500 text-xs font-medium group-hover:text-violet-700 py-0.5 px-1">
                                User
                            </p>
                        </div>
                    </div>
                </a>
            </li>
            <li>
                <a href="{{ route('access-controls.index') }}"
                    class="{{ (request()->segment(1) == 'access-controls') ? 'bg-violet-50'  : ''}} group w-full flex items-center justify-between pr-4 hover:bg-violet-50 py-1">
                    <div class="flex items-center h-8">
                        <span class="flex h-8 w-5 rounded-[3px] transform -translate-x-4 "></span>
                        <div class="text-gray-500 flex items-center gap-3 h-full">
                            <span
                                class="{{ (request()->segment(1) == 'access-controls') ? 'text-violet-700'  : 'text-gray-500'}} rounded-r-md h-full flex items-center justify-end group-hover:text-violet-700 ">
                                <x-icon icon="corner-down-right" width=12 height=12 viewBox="20 20" />
                            </span>
                            <p
                                class="{{ (request()->segment(1) == 'access-controls') ? 'text-violet-700'  : 'text-gray-500'}} text-gray-500 text-xs font-medium group-hover:text-violet-700 py-0.5 px-1">
                                Role
                            </p>
                        </div>
                    </div>
                </a>
            </li>
        </ul>
    </div>
</li>