<li>
    <a href="{{ route('home.index') }}" class="{{ (request()->segment(1) == 'home') ? 'bg-violet-50' : '' }} 
        group w-full flex items-center justify-between pr-4 hover:bg-violet-50 py-1">
        <div class="flex items-center h-8">
            <span
                class="{{ (request()->segment(1) == 'home') ? 'bg-violet-700' : '' }} 
                flex h-8 w-5 rounded-[3px] transform -translate-x-4 group-hover:bg-violet-700 line-left-menu"></span>
            <div class="flex items-center gap-3 h-full">
                <span
                    class="{{ (request()->segment(1) == 'home') ? 'text-violet-700' : 'text-gray-500' }} 
                    rounded-r-md h-full flex items-center justify-end group-hover:text-violet-700 icon-menu">
                    <x-icon icon="home" width=16 height=16 viewBox="20 20" />
                </span>
                <p class="{{ (request()->segment(1) == 'home') ? 'text-violet-700' : 'text-gray-500' }} 
                    text-xs font-medium group-hover:text-violet-700 text-menu">
                    Dashboard
                </p>
            </div>
        </div>
    </a>
</li>