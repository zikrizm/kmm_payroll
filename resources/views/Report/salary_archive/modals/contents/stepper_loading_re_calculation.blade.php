<div class="flex items-center gap-6 w-full">
    <div class="border-r pr-3 h-full">
        <img src="{{ asset('assets/images/clock.jpg') }}" alt="" width="260" height="260">
    </div>
    <div class="flex flex-col gap-4 flex-1 py-1">
        @foreach ($departments as $key=> $item)
        <div class="flex-col w-full prosess-cointainer {{ $key > 5 ? 'hidden': 'flex' }}">
            <div class="flex items-start gap-3">
                <div>
                    <span class="rounded-full text-violet-600 icon-waiting-prosess {{ $key == 0 ? 'hidden': '' }}">
                        <x-icon icon="rounded-border" width=20 height=20 strokeWidth=3 viewBox="20 20" />
                    </span>
                    <div class="rounded-full bg-violet-600 text-white p-1 icon-finish-prosess hidden">
                        <x-icon icon="check" width=12 height=12 strokeWidth=3 viewBox="20 20" />
                    </div>
                    <span class="rounded-full text-violet-600 icon-on-prosess {{ $key != 0 ? 'hidden': '' }}">
                        <svg class="h-5 w-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </span>
                </div>
                <div class="flex-1 flex flex-col gap-1.5">
                    <div class="flex justify-between items-center">
                        <p class="text-gray-700 text-sm font-semibold">{{ $item['dept_name'] }}</p>
                        <p class="text-gray-400 text-[10px] text-prosess">{{ $key != 0 ? 'Menunggu
                            proses':
                            'Dalam proses' }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>