<section class="border rounded-xl shadow-md w-max lg/max:w-full">
    <header class="px-6 py-5 flex items-center gap-3">
        <button onclick="getModal()"
            class="flex gap-2 shadow-xs rounded-lg py-2 px-3.5 text-white text-sm font-medium flex items-center bg-green-600 xs/max:text-xs xs/max:rounded">
            <x-icon icon="plus" width=16 height=16 viewBox="20 20" />
            New user
        </button>
    </header>
    <hr>
    <section class="px-6 py-5 flex items-center gap-3">
        <div class="flex-1 flex">
            <select class="select2 w-full max-w-[200px]" name="Status">
                <option value="Kerja">
                    All Role
                </option>
                <option value="Berhenti">Berhenti</option>
            </select>
        </div>
        <div class="flex-1">
            <div className='px-6 py-5 w-full'>
                <div
                    class="rounded-lg xs/max:rounded shadow-sm border border-gray-300 overflow-hidden flex items-center">
                    <span class="text-gray-500 ml-2 ">
                        <x-icon icon="search" width=16 height=16 viewBox="20 20" />
                    </span>
                    <input
                        class="py-1.5 px-2.5 text-sm xs/max:text-xs focus:outline-none focus:ring-0 focus:border-transparent flex-1"
                        type="text" placeholder="Search here" />
                </div>

            </div>
        </div>
    </section>
    <div class="w-full overflow-x-auto">
        <table class="table border-collapse w-full max-w">
            <thead class="border-y border-gray-200 bg-gray-50">
                <tr class="text-left">
                    <th class="py-3 px-6 text-xs font-medium text-gray-500 xs/max:px-4 xs/max:py">Name & Email</th>
                    <th class="py-3 px-6 text-xs font-medium text-gray-500">
                        <p class="truncate xs/max:w-12">Mobile number</p>
                    </th>
                    <th class="py-3 px-6 text-xs font-medium text-gray-500">Role</th>
                    <th class="py-3 px-6 text-xs font-medium text-gray-500">Status</th>
                    <th class="py-3 px-6"></th>
                </tr>
            </thead>
            <tbody class="text-sm font-normal text-gray-700">

                @foreach ($users as $item)
                <tr class="border-b border-grey/200 cursor-pointer hover:bg-grey/50">
                    <td class="px-6 py-4 text-left">
                        <div class="flex gap-3 items-center">
                            <button type="button">
                                <img src="https://pixlr.com/studio/template/6264364c-b8cc-4f4f-92d8-28c69a2b756w/thumbnail.webp"
                                    alt="" class="w-10 object-contain h-10 min-w-[40px] min-h-[40px]">
                            </button>
                            <div>
                                <p class="text-gray-900 text-sm font-medium truncate sm/max:w-12">
                                    {{$item->name}}</p>
                                <p class="text-gray-500 text-sm font-normal truncate sm/max:w-12">
                                    {{$item->email}}
                                </p>
                            </div>
                        </div>

                    </td>
                    <td class="px-6 py-3 text-left">
                        <p class="text-gray-500 text-sm font-normal truncate xs/max:w-12">081578925512</p>
                    </td>
                    <td class="px-6 py-3 text-left">
                        <p class="text-gray-500 text-sm font-normal truncate xs/max:w-12 ">
                            @foreach ($item->roles as $role)
                            {{ $role->name }}
                            @endforeach
                        </p>
                    </td>
                    <td class="px-6 py-3 text-left ">
                        {!!$item->statusbox!!}
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex gap-1">
                            <button class="p-2.5 cursor-pointer text-gray-500 rounded hover:bg-gray-50 delete-btn">
                                <x-icon icon="trash-2" width=16 height=16 viewBox="20 20"
                                    onclick="getModalDelete('{{ $item->id }}')" />
                            </button>
                            <button class="p-2.5 cursor-pointer text-gray-500 rounded hover:bg-gray-50"
                                onclick="getModal('{{ $item->id }}')">
                                <x-icon icon="edit-2" width=16 height=16 viewBox="20 20" />
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <footer class="flex justify-between items-center px-6 pt-3 pb-4">
        <p class="text-gray-700 text-sm">Page <span>1</span> of <span>7</span></p>
        <div class="flex gap-3"><button
                class="px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 text-gray-700">Next</button>
        </div>
    </footer>
</section>