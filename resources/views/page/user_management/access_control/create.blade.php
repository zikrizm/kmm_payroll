<div class="flex flex-col gap-6 xs/max:gap-6">
    <header class="px-4 flex flex-col gap-5 xs/max:gap-3">
        <div class="flex flex-col gap-1">
            <p class="text-2xl font-bold text-gray-900 xs/max:text-xl xs/max:font-semibold">New access control</p>
            <p class="text-base font-normal text-gray-500 xs/max:text-sm">Lorem ipsum dolor sit, amet consectetur
                adipisicing
                elit.
            </p>
        </div>
        <hr>
    </header>
    <form autocomplete="off" action="{{ route('userManagement.store') }}" method="POST" id="submit_user_management">
        @csrf
        <!-- {{ csrf_field() }} -->
        <input type="hidden" name="type" value="{{ $type }}">
        <main class="px-4 flex flex-col xs/max:gap-4 gap-6 mb-8">
            <section class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Role name*</label>
                <input
                    class="py-1.5 px-2.5 text-sm xs/max:text-xs rounded-lg xs/max:rounded shadow-sm border border-gray-300 focus:outline-none focus:ring-2 focus:shadow focus:ring-gray-300 focus:border-transparent"
                    type="text" placeholder="Enter new your role name" name="role_name" required />
            </section>
            <section class="flex flex-col gap-4 xs/max:gap-3">
                <div class="flex flex-col gap-1">
                    <p class="text-xl font-semibold text-gray-900 xs/max:text-sm">Permissions</p>
                    <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                        Lorem ipsum dolor sit, amet consectetur adipisicing elit.
                    </p>
                </div>
                <hr>
                <div class="flex flex-col gap-2">
                    <label class="font-normal text-base font-medium text-gray-700 xs/max:text-sm">User
                        management</label>
                    <div class="flex flex-col flex-1 gap-1">
                        <label
                            class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100 mb-3">
                            <input type="checkbox" class="accent-green-500"
                                onchange="onSelectAllCheckbox(this,'.user-check')"> Select all
                        </label>
                        <label
                            class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                            <input type="checkbox" class="accent-green-500 user-check" name="roles[view_user]"> View
                            user
                        </label>
                        <label
                            class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                            <input type="checkbox" class="accent-green-500 user-check" name="roles[add_user]"> Add user
                        </label>
                        <label
                            class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                            <input type="checkbox" class="accent-green-500 user-check" name="roles[edit_user]"> Edit
                            user
                        </label>
                        <label
                            class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                            <input type="checkbox" class="accent-green-500 user-check" name="roles[delete_user]"> Delete
                            user
                        </label>
                    </div>
                </div>
                <hr>
                <div class="flex flex-col gap-2">
                    <label class="font-normal text-base font-medium text-gray-700 xs/max:text-sm">Employee
                        management</label>
                    <div class="flex flex-col flex-1 gap-1">
                        <label
                            class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100 mb-3">
                            <input type="checkbox" class="accent-green-500"
                                onchange="onSelectAllCheckbox(this,'.employee-check')"> Select all
                        </label>
                        <label
                            class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                            <input type="checkbox" class="accent-green-500 employee-check" name="roles[view_employee]">
                            View employee
                        </label>
                        <label
                            class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                            <input type="checkbox" class="accent-green-500 employee-check" name="roles[add_employee]">
                            Add employee
                        </label>
                        <label
                            class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                            <input type="checkbox" class="accent-green-500 employee-check" name="roles[edit_employee]">
                            Edit employee
                        </label>
                        <label
                            class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                            <input type="checkbox" class="accent-green-500 employee-check"
                                name="roles[delete_employee]"> Delete employee
                        </label>
                    </div>
                </div>
            </section>
        </main>
        <hr>
        <footer class="flex justify-end items-center gap-3 p-4">
            <button type="reset"
                class="modal-close shadow text-gray-500 bg-white hover:bg-gray-100 focus:ring-2 focus:ring-gray-300 rounded-lg xs/max:rounded-md border border-gray-200 text-sm xs/max:text-xs font-medium xs/max:px-4 px-6 xs/max:py-1.5 py-2 hover:text-gray-900 focus:z-10">
                Cancel</button>
            <button type="submit"
                class="text-white shadow bg-green-600 hover:bg-green-700 focus:ring-2 focus:ring-green-700 font-medium rounded-lg xs/max:rounded-md text-sm xs/max:text-xs inline-flex items-center xs/max:px-4 px-6 xs/max:py-1.5 py-2 text-center">Done</button>
        </footer>
    </form>
</div>
