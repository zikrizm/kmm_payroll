<form autocomplete="off" action="{{ route('operational.store') }}" method="POST"
    class="submit-operational flex items-start gap-5 justify-center">
    @csrf
    <!-- {{ csrf_field() }} -->
    <section
        class="flex flex-col gap-8 pt-4 bg-white w-[400px] max-h-[95vh] overflow-y-auto overflow-x-hidden relative rounded-lg no-scrollbar">
        <header class="px-4 flex flex-col gap-5 pt-4 xs/max:gap-3 relative">
            <button
                class="absolute top-[-5px] right-3 xs/max:top-[-6px] modal-close hover:bg-gray-100 text-red rounded p-2">
                <x-icon icon="x" width=16 height=16 viewBox="20 20" />
            </button>
            <div class="flex flex-col gap-1">
                <div class="flex items-start gap-2">
                    <div
                        class="rounded-full bg-violet-100 p-2 border-[4px] border-violet-50 box-border mr-2 text-violet-800">
                        <x-icon icon="board" width=16 height=16 viewBox="20 20" />
                    </div>
                    <div>
                        <p class="text-xl font-semibold text-gray-900">Tambah operasional
                        </p>
                        <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                            Harap berikan detail operasional.
                        </p>
                    </div>
                </div>
            </div>
            <hr>
        </header>
        <div>
            <main class="px-4 flex flex-col gap-2.5 xs/max:gap-3 mb-8">
                <section class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Tanggal
                        operasional*</label>
                    {!! FormCustom::input('date', $date, [
                        'placeholder' => 'Pilih tanggal operasional',
                        'class' => 'operational_date',
                        'readonly' => true,
                        'block_input' => !empty($date),
                        'prefixiconname' => 'calendar',
                    ]) !!}
                </section>
                <section class="flex flex-col gap-1">
                    <select class="select2-department" name="department">
                        <option value="" disabled selected>Silahkan Pilih Bagian</option>
                        @foreach ($departments['data'] ?? [] as $item)
                            <option value="{{ $item['id'] }}">{{ $item['dept_name'] }}</option>
                        @endforeach
                    </select>
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs department hint-text"></label>
                </section>
                {{-- <div class="border rounded p-3 flex flex-col gap-2.5">
                    <div class="flex flex-col gap-3" id="timetable-day-content">
                        <div class="flex flex-col gap-2">
                            <div class="flex items-start gap-2">
                                <span class="pt-1">
                                    {!! FormCustom::checkbox('status', -1) !!}
                                </span>
                                <div class="flex flex-col">
                                    <p class="text-gray-700 text-sm ">Senin</p>
                                    <dd class="text-gray-500 text-xs">Lorem ipsum dolor sit, amet consectetur</dd>
                                </div>
                            </div>
                            <hr>
                            <div
                                class="overflow-auto overflow-y-hidden border border-gray-200 rounded-lg shadow-sm ml-5">
                                <table class='table border-collapse w-full'>
                                    <thead class='border-b border-gray-200 bg-gray-50'>
                                        <tr class=''>
                                            <th class='text-left'>
                                                <div class='flex items-center'>
                                                    <div class='pl-4 py-2 flex items-center'>
                                                        {!! FormCustom::checkbox() !!}
                                                        <p class="px-4 text-xs font-medium text-gray-500 truncate">
                                                            Nama jadwal</p>
                                                    </div>
                                                </div>
                                            </th>
                                            <th class="text-xs font-medium text-gray-500 truncate">Waktu</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ([1,2,3] as $item)
                                            <tr class='hover:bg-gray-50 border-b border-gray-200 cursor-pointer'>
                                                <td class='text-left w-[80%]'>
                                                    <div class="flex items-center">
                                                        <div class="pl-4">
                                                            {!! FormCustom::checkbox() !!}
                                                        </div>
                                                        <div class="px-4">
                                                            <p class="text-gray-700 text-sm">Shift001</p>
                                                            <dd class="text-gray-400 text-[10px]">07:00 - 16:00
                                                            </dd>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class='px-3 text-gray-500 text-sm'>
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-12 pt-1">
                                                            {!! FormCustom::input('overtime', 0, ['placeholder' => '-', 'class' => '!h-7 number text-center']) !!}
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div> --}}
                <div id="timetable-content" class="hidden"></div>
                <div id="note-content" class="">
                    <div class="flex justify-center w-full">
                        <div class="flex flex-col gap-2.5 border rounded p-4 w-[375px]">
                            <div class="w-full flex items-center justify-center gap-3">
                                <span class="text-gray-300">
                                    <x-icon icon="calendar-version-2" width=80 height=80 viewBox="20 20" />
                                </span>
                                <div>
                                    <p class="text-sm font-medium text-gray-400">Catatan.</p>
                                    <p class="text-xs text-gray-300">Silahkan pilih bagian operasional-nya
                                        dan isi datanya.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="note-warning-content" class="hidden">
                    <div class="flex justify-center w-full bg-[#fffaf4]">
                        <div class="flex flex-col gap-2.5 border border-orange-300 rounded p-4 w-[375px]">
                            <div class="w-full flex items-center justify-center gap-3">
                                <span class="text-orange-200">
                                    <x-icon icon="calendar-version-2" width=80 height=80 viewBox="20 20" />
                                </span>
                                <div>
                                    <p class="text-sm font-medium text-orange-700">Informasi.</p>
                                    <p class="text-xs text-orange-600" id="text-error-operation"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <hr>
            <footer class="flex justify-end items-center gap-3 p-4  pb-6">
                <button type="reset"
                    class="modal-close shadow text-gray-500 bg-white hover:bg-gray-100 focus:ring-2 focus:ring-gray-300 rounded-lg xs/max:rounded-md border border-gray-200 text-sm xs/max:text-xs font-medium xs/max:px-4 px-6 xs/max:py-1.5 py-2 hover:text-gray-900 focus:z-10">
                    Cancel</button>
                <button type="submit"
                    class="text-white shadow bg-violet-600 hover:bg-violet-700 focus:ring-2 focus:ring-violet-700 font-medium rounded-lg xs/max:rounded-md text-sm xs/max:text-xs inline-flex items-center xs/max:px-4 px-6 xs/max:py-1.5 py-2 text-center">Done</button>
            </footer>
        </div>
    </section>
</form>
