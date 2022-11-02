<main class='border border-gray-200 rounded-lg shadow-sm overflow-hidden'>
    <div class="w-full overflow-auto overflow-y-hidden">
        <div class="flex border-b">
            @foreach ($dates as $item)
            <div class='flex-1 px-3 py-1.5 cursor-pointer group hover:bg-gray-50'>
                <button type="button"
                    class="w-full text-gray-500 h-8 hidden group-hover:flex items-center justify-center"
                    onclick="get_modal(null,{date: '{{ $item}}'})">
                    <x-icon icon="plus" width=16 height=16 viewBox="20 20" />
                </button>
                <div class="h-8 flex flex-col items-center group-hover:hidden">
                    <p class="text-gray-700 font-medium text-xs">
                        {{ date('d', strtotime($item)) }}
                    </p>
                    <p class="text-gray-500 text-[10px] font-normal">
                        {{ Carbon\Carbon::create($item)->locale('id_ID')->dayName }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
        {{-- @foreach ($dates as $item) --}}
        <div class="flex">
            <div class="w-full flex">
                @foreach ($dates as $item)
                <div class="flex-1 flex flex-col border-r last:border-0 py-1">
                    @foreach (($operationals[$item] ?? []) as $key => $item_op)
                    <div class="cursor-pointer group hover:bg-gray-50 px-2 py-1">
                        <div class="h-12 border rounded flex justify-center flex-col p-2 relative">
                            <p class="text-gray-700 font-medium text-sm">{{ $item_op->department->dept_name }}sdsdfsdfsdfsdfs xdfs</p>
                            <p class="text-gray-400 font-normal text-xs">{{ $item_op->shift->name }}</p>
                            <div class="absolute hidden group-hover:flex items-center justify-center gap-1 top-1 right-1">
                                <button type="button"
                                    class="text-violet-500 border rounded border-violet-100 p-1 hover:bg-violet-100">
                                    <x-icon icon="edit" width=12 height=12 viewBox="20 20" />
                                </button>
                                <button type="button"
                                    class="text-red-500 border rounded border-red-100 p-1 hover:bg-red-100">
                                    <x-icon icon="trash-2" width=12 height=12 viewBox="20 20" />
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
        {{-- <div class="flex">

            <div class='flex-1 px-3 py-1.5 cursor-pointer border-r last:border-0 group hover:bg-gray-50'>
                <div class="hidden group-hover:flex items-center justify-center">
                    <button type="button" class="text-gray-500" onclick="get_modal(null,{date: '{{ $item['date'] }}'})">
                        <x-icon icon="plus" width=16 height=16 viewBox="20 20" />
                    </button>
                </div>
                <div class="flex flex-col items-center group-hover:hidden">
                    <p class="text-gray-700 font-medium text-xs">
                        {{ date('d', strtotime($item['date'])) }}
                    </p>
                    <p class="text-gray-500 text-[10px] font-normal">{{ $item['slug'] }}</p>
                </div>
            </div>
        </div> --}}
        {{-- @endforeach --}}
        {{-- @foreach ($operationals as $key => $item)
        <div class="flex itemsc-center">
            <div class='flex items-center justify-center bg-gray-100 border-r min-w-[40px] w-10 max-w-[40px]'>
                <div class="text-center">
                    <p class="text-gray-500 text-xs truncate font-medium">1</p>
                </div>
            </div>
            @foreach ($th_dates as $item)
            <div class='flex-1 px-3 py-1.5 cursor-pointer border-r last:border-0 group hover:bg-gray-50'>
                <div class="hidden group-hover:flex items-center justify-center">
                    <button type="button" class="text-gray-500" onclick="get_modal(null,{date: '{{ $item['date'] }}'})">
                        <x-icon icon="plus" width=16 height=16 viewBox="20 20" />
                    </button>
                </div>
                <div class="flex flex-col items-center group-hover:hidden">
                    <p class="text-gray-700 font-medium text-xs">
                        {{ date('d', strtotime($item['date'])) }}
                    </p>
                    <p class="text-gray-500 text-[10px] font-normal">{{ $item['slug'] }}</p>
                </div>
            </div>
            @endforeach
        </div> --}}
        {{-- <div>
            @foreach ($item as $item_date)

            <p>{{ $key }}</p>

            @endforeach
        </div> --}}
        {{-- @endforeach --}}



    </div>

</main>