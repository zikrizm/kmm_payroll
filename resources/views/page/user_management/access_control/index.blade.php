<section class="border rounded-xl shadow-md w-max lg/max:w-full">
    <header class="px-6 py-5 flex items-center gap-3">
        <button onclick="getCreateComponent('access_control',null)"
            class="flex gap-2 shadow-xs rounded-lg py-2 px-3.5 text-white text-sm font-medium flex items-center bg-green-600">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-plus">
                <line x1="12" y1="5" x2="12" y2="19" />
                <line x1="5" y1="12" x2="19" y2="12" />
            </svg>
            New access control</button>
    </header>
    <div class="w-full overflow-x-scroll">
        <table class="table border-collapse w-full max-w">
            <thead class="border-y border-gray-200 bg-gray-50">
                <tr class="text-left">
                    <th class="py-3 px-6 text-xs font-medium text-gray-500">Roles</th>
                    <th class="py-3 px-6 text-xs font-medium text-gray-500">Roles field</th>
                    <th class="py-3 px-6"></th>
                </tr>
            </thead>
            <tbody class="text-sm font-normal text-gray-700">
                @foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9, 0] as $item)
                    <tr class="border-b border-grey/200 cursor-pointer hover:bg-grey/50">
                        <td class="px-6 py-3 text-left">
                            <p class="text-gray-500 text-sm font-normal truncate xs/max:w-12">Admin</p>
                        </td>
                        <td class="px-6 py-3 text-left">
                            <p class="text-gray-500 text-sm font-normal truncate xs/max:w-12">Dashboard, User management
                            </p>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex gap-1">
                                <button class="p-2.5 cursor-pointer text-gray-500 rounded hover:bg-gray-50 delete-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
                                        <polyline points="3 6 5 6 21 6" />
                                        <path
                                            d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                        <line x1="10" y1="11" x2="10" y2="17" />
                                        <line x1="14" y1="11" x2="14" y2="17" />
                                    </svg> </button>
                                <button class="p-2.5 cursor-pointer text-gray-500 rounded hover:bg-gray-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2">
                                        <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z" />
                                    </svg> </button>
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
