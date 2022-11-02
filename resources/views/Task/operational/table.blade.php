<main class='border border-gray-200 rounded-lg shadow-sm overflow-hidden'>
    <div class="w-full overflow-auto overflow-y-hidden">
        <div class="flex border-b">
            <div class='flex items-center justify-center bg-gray-100 border-r min-w-[40px] w-10 max-w-[40px]'>
                <div class="text-center">
                    <p class="text-gray-500 text-xs truncate font-medium">No.</p>
                </div>
            </div>
            @foreach ($th_dates as $item)
            <div class='flex-1 px-3 py-1.5 cursor-pointer group hover:bg-gray-50'>
                <button type="button"
                    class="w-full text-gray-500 h-8 hidden group-hover:flex items-center justify-center"
                    onclick="get_modal(null,{date: '{{ $item['date'] }}'})">
                    <x-icon icon="plus" width=16 height=16 viewBox="20 20" />
                </button>
                <div class="h-8 flex flex-col items-center group-hover:hidden">
                    <p class="text-gray-700 font-medium text-xs">
                        {{ date('d', strtotime($item['date'])) }}
                    </p>
                    <p class="text-gray-500 text-[10px] font-normal">{{ $item['slug'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
        {{-- @foreach ($th_dates as $item) --}}
        <div class="flex">
            <div class="flex flex-col">
                @foreach ([0,1,2,3,4,5,6,7,8,9] as $key=> $item)
                <div
                    class="h-12 flex items-center justify-center bg-gray-100 border-r min-w-[40px] max-w-[40px] w-10 h-10">
                    <p class="text-gray-500 text-xs truncate font-medium">{{ $key +1 }}</p>
                </div>
                @endforeach
            </div>
            <div class="w-full flex">
                @foreach ($th_dates as $item)
                <div class="flex-1 flex flex-col border-r last:border-0 py-1">
                    @foreach (($operationals[$item['date']] ?? []) as $key => $item_op)
                    <div class="cursor-pointer group hover:bg-gray-50 px-2 py-1">
                        <div class="h-12 hidden group-hover:flex items-center justify-center gap-3">
                            <button type="button"
                                class="text-violet-500 border rounded border-violet-100 p-1.5 hover:bg-violet-50">
                                <x-icon icon="edit" width=16 height=16 viewBox="20 20" />
                            </button>
                            <button type="button"
                                class="text-red-500 border rounded border-red-100 p-1.5 hover:bg-red-50">
                                <x-icon icon="trash-2" width=16 height=16 viewBox="20 20" />
                            </button>
                        </div>
                        <div class="h-12 border rounded flex justify-center flex-col p-2 group-hover:hidden">
                            <p class="text-gray-700 font-medium text-sm">{{ $item_op->dept_id }}</p>
                            <p class="text-gray-400 font-normal text-xs">Shift Producksi 01</p>
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