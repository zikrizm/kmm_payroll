<main class='border border-gray-200 rounded-lg shadow-sm '>
    <div class="w-full overflow-auto overflow-y-hidden">
        <div class="flex ">
            @foreach ($th_dates as $item)
            <div class='border-b flex-1 px-3 py-1.5 group min-w-[170px] hover:bg-gray-50'>
                <div class="hidden group-hover:flex justify-around">
                    <button type="button"
                        class="text-gray-500 h-8 w-max flex px-2 items-center rounded justify-center flex-col border"
                        onclick="get_modal(null, {date: '{{ $item['date'] }}'})">
                        <x-icon icon="plus" width=16 height=16 viewBox="20 20" />
                        <p class="text-[8px]">Operasional</p>
                    </button>
                    <button type="button"
                        class="text-gray-500 h-8 w-max flex px-2 items-center rounded flex-col justify-center border"
                        onclick="get_modal_task(null, {date: '{{ $item['date'] }}'})">
                        <x-icon icon="plus" width=16 height=16 viewBox="20 20" />
                        <p class="text-[8px]">Penugasan</p>
                    </button>
                </div>
                <div class="h-8 flex flex-col items-center group-hover:hidden">
                    <p class="font-medium text-sm {{ $item['is_holiday'] ? 'text-red-700' : 'text-gray-700' }}">
                        {{ date('d', strtotime($item['date'])) }}
                    </p>
                    <p
                        class="text-gray-500 text-[10px] font-normal {{ $item['is_holiday'] ? 'text-red-700' : 'text-gray-500' }}">
                        {{ Carbon\Carbon::create($item['date'])->locale('id_ID')->dayName }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
        <div class="flex">
            <div class="w-full flex">
                @foreach ($dates as $item)
                <div class="flex-1 flex flex-col border-r last:border-0 py-1 min-w-[170px]">
                    @forelse(($request_tasks[$item] ?? []) as $key => $item_task)
                    <div class="group px-2 py-1 h-[65px]">
                        <div class="h-full border rounded flex justify-between flex-col p-2 relative hover:bg-gray-50 ">
                            <p class="text-gray-700 font-medium text-sm truncate w-[135px] break-words">Tugas - {{ (!empty($item_task['position']))?
                                $item_task->position->position_name??'' : '-' }}</p>
                            <div class="flex justify-start items-center relative h-[18px] gap-0.5">
                                @foreach ($item_task->request_task_has_emps as $key => $emp)
                                <div class='rounded-full border-white bg-white border-[1.5px] h-[18px] w-[18px] overflow-hidden'>
                                    @if (!empty($emp->employee->photo))
                                    <img src="{{config('constants.api_zkteco')}}{{ $emp->employee->photo }}" alt=""
                                        class="w-full h-full object-cover">
                                    @else
                                    <img src="{{config('constants.api_zkteco')}}/files/nophoto.gif" alt="" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            <div
                                class="absolute hidden group-hover:flex flex-col items-center justify-center gap-0.5 top-0.5 right-0.5">
                                <button type="button"
                                    onclick="get_modal_task('{{ $item_task->id }}', {date: '{{ $item }}'})"
                                    class="bg-white text-violet-500 border rounded border-violet-100 p-1 hover:bg-violet-100">
                                    <x-icon icon="edit" width=12 height=12 viewBox="20 20" />
                                </button>
                                <button type="button" onclick="open_modal_confirm_task(this,'{{ $item_task->id }}')"
                                    class="bg-white text-red-500 border rounded border-red-100 p-1 hover:bg-red-100">
                                    <x-icon icon="trash-2" width=12 height=12 viewBox="20 20" />
                                </button>
                            </div>
                        </div>

                    </div>
                    @empty
                    @endforelse
                    @forelse(($operationals[$item] ?? []) as $key => $item_op)
                    <div class="group px-2 py-1 h-[65px]">
                        <div class="h-full border rounded flex justify-between flex-col p-2 relative hover:bg-gray-50 ">
                            <p class="text-gray-700 font-medium text-sm truncate w-[135px] break-words">{{ $item_op->department->dept_name }}</p>
                            <div class="flex items-center flex-wrap gap-0.5 flex-1 ">
                                @foreach ($item_op->operational_has_timetables as $item_timetable)
                                <div
                                    class="tooltip-custom cursor-pointer flex items-center gap-1 rounded-xl px-1 py w-max {{ !empty($item_timetable->ot_limit) ? 'border':'' }} {{ $item_timetable->status == 'active'? 'border-green-100 text-green-700': 'border-red-100 text-red-700' }}">
                                    <span
                                        class="w-[8px] h-[8px] rounded-full {{ $item_timetable->status == 'active'? 'bg-green-600': 'bg-red-600' }} block"></span>
                                    @if (!empty($item_timetable->ot_limit))
                                    <p
                                        class="text-[10px] font-normal flex items-center gap-1 capitalize {{ $item_timetable->status == 'active'? 'text-green-600': 'text-red-600' }}">
                                        + {{ $item_timetable->ot_limit }}
                                    </p>
                                    @endif
                                    <div
                                        class="tooltip-custom-text border p-1.5 {{ !empty($item_timetable->ot_limit) ? 'top-[-50px]': 'top-[-55px]'}} rounded bg-white after:!border-t-gray-300 flex flex-col items-center">
                                        <p
                                            class="text-xs text-gray-700 {{ $item_timetable->status != 'active' ? 'line-through decoration-gray-500':'' }}">
                                            {{ $item_timetable->timetable->name??'' }}</p>
                                        <div class="flex w-full items-center gap-1.5 text-gray-400 justify-start">
                                            <x-icon icon="clock" width=12 height=12 viewBox="20 20" />
                                            <div class="flex items-center gap-1 text-gray-400 text-[10px] mt-0.5">
                                                <p class="truncate">
                                                    {{ date('H:i', strtotime($item_timetable->timetable->check_in)); }}
                                                </p>
                                                -
                                                <p class="truncate">
                                                    {{ date('H:i', strtotime($item_timetable->timetable->check_out)); }}
                                                </p>
                                                @if (!empty($item_timetable->ot_limit))
                                                <p class="text-[10px] font-normal flex items-center gap-1 capitalize">
                                                    + {{ $item_timetable->ot_limit }} (lembur)
                                                </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div
                                class="absolute hidden group-hover:flex flex-col items-center justify-center gap-0.5 top-0.5 right-0.5">
                                <button type="button" onclick="get_modal('{{ $item_op->id }}', {date: '{{ $item }}'})"
                                    class="bg-white text-violet-500 border rounded border-violet-100 p-1 hover:bg-violet-100">
                                    <x-icon icon="edit" width=12 height=12 viewBox="20 20" />
                                </button>
                                <button type="button" onclick="open_modal_confirm(this,'{{ $item_op->id }}')"
                                    class="bg-white text-red-500 border rounded border-red-100 p-1 hover:bg-red-100">
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