<main class='border border-gray-200 rounded-lg shadow-sm overflow-hidden'>
    <div class="w-full overflow-auto overflow-y-hidden">
        <div class="flex ">
            @foreach ($dates as $item)
            <div class='border-b flex-1 px-3 py-1.5 cursor-pointer group hover:bg-gray-50 min-w-[150px]'>
                <button type="button"
                    class="w-full text-gray-500 h-8 hidden group-hover:flex items-center justify-center"
                    onclick="get_modal(null, {date: '{{ $item }}'})">
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
                <div class="flex-1 flex flex-col border-r last:border-0 py-1 min-w-[150px]">
                    @forelse(($operationals[$item] ?? []) as $key => $item_op)
                    <div class="cursor-pointer group hover:bg-gray-50 px-2 py-1">
                        <div class="border rounded flex justify-center flex-col p-2 relative">
                            <p class="text-gray-700 font-medium text-sm">{{ $item_op->department->dept_name }}</p>
                            <p class="text-gray-400 font-normal text-xs">{{ $item_op->shift->name }}</p>
                            <div
                                class="absolute hidden group-hover:flex flex-col items-center justify-center gap-0.5 top-0.5 right-0.5">
                                <button type="button" onclick="get_modal(null, {date: '{{ $item }}'})"
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
                    @empty
                    @endforelse
                </div>
                @endforeach
            </div>
        </div>
    </div>

</main>