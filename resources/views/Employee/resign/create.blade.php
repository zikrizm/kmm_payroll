<form autocomplete="off" action="{{ route('resign.store') }}" method="POST"
    class="submit-resign flex items-start gap-5 justify-center">
    @csrf
    <!-- {{ csrf_field() }} -->
    <section
        class="flex flex-col gap-8 pt-4 bg-white w-[600px] max-h-[90vh] overflow-y-auto overflow-x-hidden relative rounded-lg">
        <header class="px-4 flex flex-col gap-5 pt-4 xs/max:gap-3 relative">
            <button
                class="absolute top-[-5px] right-3 xs/max:top-[-6px] modal-close hover:bg-gray-100 text-red rounded p-2">
                <x-icon icon="x" width=16 height=16 viewBox="20 20" />
            </button>
            <div class="flex flex-col gap-1">
                <div class="flex items-start gap-2">
                    <div
                        class="rounded-full bg-violet-100 p-1.5 border-[4px] border-violet-50 box-border mr-2 text-violet-800">
                        <x-icon icon="user-x" width=18 height=18 viewBox="20 20" />
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 xs/max:text-xl xs/max:font-semibold">New resign
                        </p>
                        <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                            Please provide the resign's detail.
                        </p>
                    </div>
                </div>
            </div>
            <hr>
        </header>
        <div>
            <main class="px-4 flex flex-col gap-8 xs/max:gap-3 mb-8">
                <div class="">
                    <main class='border border-gray-200 rounded-lg shadow-sm '>
                        <div class="w-full overflow-auto overflow-y-hidden">
                            <table class='table border-collapse w-full'>
                                <thead class='border-b border-gray-200 bg-gray-50'>
                                    <tr class=''>
                                        <th class='text-left'>
                                            <div class='flex items-center'>
                                                <div class='pl-4 py-2 flex items-center'>
                                                    {!! FormCustom::checkbox() !!}
                                                </div>
                                                <div class='px-6 py-3 cursor-pointer flex-1'>
                                                    <p
                                                        class="text-xs font-medium text-gray-500 truncate cursor-pointer">
                                                        Employee code</p>
                                                </div>
                                            </div>
                                        </th>
                                        <th class='px-3 py-3 text-left cursor-pointer'>
                                            <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Fisrt
                                                name</p>
                                        </th>
                                        <th class='px-3 py-3 text-left cursor-pointer'>
                                            <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Last
                                                name</p>
                                        </th>
                                        <th class='px-3 py-3 text-left cursor-pointer'>
                                            <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">
                                                Department</p>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @foreach ($resigns['data'] as $item)
                                    <tr class='hover:bg-gray-50 border-b border-gray-200 cursor-pointer'>
                                        <td class='text-left'>
                                            <div class="flex items-center">
                                                <div class="pl-4 py ">
                                                    {!! FormCustom::checkbox() !!}
                                                </div>
                                                <div class="flex gap-3 items-center px-6 py-3">
                                                    <p class="text-gray-500 text-sm">
                                                        {{ $item['emp_code'] }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class='px-3 py text-gray-500 text-sm'>
                                            <p class="truncate">
                                                {{ $item['first_name'] ?? '-' }} {{ $item['last_name'] ?? '-' }}
                                            </p>
                                        </td>
                                        <td class='px-3 py text-gray-500 text-sm'>
                                            <p class="truncate">
                                                {{ (!empty($item['department']) ? $item['department']['dept_name']??'-'
                                                : '-') }}
                                            </p>
                                        </td>
                                        <td class='px-3 py text-gray-500 text-sm'>
                                            <p class="truncate">
                                                {{ (!empty($item['position']) ? $item['position']['position_name']??'-'
                                                : '-') }}
                                            </p>
                                        </td>
                                        <td class='px-3 py text-gray-500 text-sm'>
                                            <p class="truncate">
                                                {{ $item['resign_type'] }}
                                            </p>
                                        </td>
                                        <td class='px-3 py text-center text-gray-500 text-sm'>
                                            <div class="flex items-center gap-2">
                                                <x-icon icon="calendar" width=18 height=18 viewBox="20 20" />
                                                <p class="truncate">
                                                    {{ date('Y-m-d', strtotime($item['resign_date'])); }}
                                                </p>
                                            </div>
                                        </td>
                                        <td class='px-3 py text-center text-gray-500 text-sm'>
                                            <p class="truncate">
                                                {{ $item['resign_date'] }}
                                            </p>
                                        </td>
                                        <td class='px-3 py text-center text-gray-500 text-sm'>
                                            <p class="truncate">
                                                {{ $item['resign_date'] }}
                                            </p>
                                        </td>
                                        <td class='px-3 py'>
                                            <div class='flex gap-1'>
                                                <button onclick="open_modal_confirm('{{ $item['id'] }}')"
                                                    class='p-2.5 cursor-pointer text-gray-500 delete-btn'>
                                                    <x-icon icon="trash-2" width=18 height=18 viewBox="20 20" />
                                                </button>
                                                <button class='p-2.5 cursor-pointer text-gray-500 edit-btn'
                                                    onclick="get_modal({{ $item['emp_code'] }})">
                                                    <x-icon icon="edit" width=18 height=18 viewBox="20 20" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach --}}
                                </tbody>
                            </table>
                        </div>
                        
                    </main>
                </div>
                <div class="flex flex-col gap-4">
                    <div>
                        <p class="text-lg font-medium text-gray-900
                            xs/max:font-semibold">Resignation information
                        </p>
                        <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                            Please complete this resignation data
                        </p>
                    </div>
                    <hr>
                    <section class="flex flex-col gap-1">
                        <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Resignation Date*</label>
                        {!! FormCustom::input('resign_date', null, [ 'placeholder' => 'Enter new your resignation date',
                        'class' => 'date_input', 'readonly' => true, 'prefixiconname' => 'calendar' ]) !!}
                    </section>
                    <section class="flex flex-col gap-1">
                        <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Resignation Type</label>
                        {!! FormCustom::input('resign_type', null, [ 'placeholder' => 'Enter new your resignation type',
                        'class' => 'date_input', 'readonly' => true, 'prefixiconname' => 'calendar' ]) !!}
                    </section>
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