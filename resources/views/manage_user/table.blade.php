<main class='border border-gray-200 rounded-lg shadow-sm w-max overflow-hidden'>
    <table class='table border-collapse w-full'>
        <thead class='border-b border-gray-200 bg-gray-50'>
            <tr class=''>
                <th class='text-left'>
                    <div class='flex items-center'>
                        <div class='pl-4 py-2 flex items-center'>
                            {!! FormCustom::checkbox() !!}
                        </div>
                        <div class='px-6 py-3 cursor-pointer flex-1'>
                            <x-ui.sort-table text="Name & Email" url="{{ route('user.index') }}" field="first_name"
                                order="{{ $order }}" />
                        </div>
                    </div>
                </th>
                <th class='px-6 py-3 text-left cursor-pointer'>
                    <x-ui.sort-table text="Username" url="{{ route('user.index') }}" field="username"
                        order="{{ $order }}" />
                </th>
                <th class='px-6 py-3 text-left cursor-pointer'>
                    <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Role</p>
                </th>
                <th class='px-6 py-3 text-left cursor-pointer'>
                    <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Status</p>
                </th>
                @canany(['user.update', 'user.delete'])
                    <th class='px-6 py-3 text-left text-gray-500 text-xs font-medium'></th>
                @endcanany
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $item)
                <tr class='hover:bg-gray-50 border-b border-gray-200 cursor-pointer'>
                    <td class='text-left'>
                        <div class="flex items-center">
                            <div class="pl-4 py-2 ">
                                {!! FormCustom::checkbox() !!}
                            </div>
                            <div class="flex gap-3 items-center px-6 py-3">
                                <img src="" alt=""
                                    class="w-10 object-contain h-10 min-w-[40px] min-h-[40px] rounded-full">
                                <div>
                                    <p class="text-gray-900 text-sm font-medium truncate sm/max:w-12">
                                        {{ $item->surname }} {{ $item->first_name }} {{ $item->last_name }}
                                    </p>
                                    <p class="text-gray-500 text-sm font-normal truncate sm/max:w-12">
                                        {{ $item->email }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class='px-6 py-4 text-gray-500 text-sm'>
                        {{ $item->username }}
                    </td>
                    <td class='px-6 py-4 text-gray-500 text-sm'>
                        @foreach ($item->roles as $role)
                            {{ $role->name }}
                        @endforeach
                    </td>
                    <td class='px-6 py-4 text-gray-500 text-sm'>
                        {!! $statusbox !!}
                    </td>
                    <td class='px-4 py-4'>
                        <div class='flex gap-1'>
                            <button class='p-2.5 cursor-pointer text-gray-500 delete-btn'>
                                <x-icon icon="trash-2" width=18 height=18 viewBox="20 20" />
                            </button>
                            <button class='p-2.5 cursor-pointer text-gray-500 edit-btn'
                                onclick="get_modal({{ $item->id }})">
                                <x-icon icon="edit" width=18 height=18 viewBox="20 20" />
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <footer class='flex justify-between items-center px-6 pt-3 pb-4'>
        <p class='text-gray-700 text-sm'>Page <span>1</span> of <span>30</span></p>
        <div class='flex gap-3'>
            <button class='px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Previous</button>
            <button class='px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50'>Next</button>
        </div>
    </footer>
</main>
