<main class="flex flex-col gap-8  pt-4 w-[375px] xs/max:w-[280px]">
    <header class="px-4 flex flex-col gap-5 pt-4 xs/max:gap-3 relative">
        <button class="absolute top-[-5px] right-3 xs/max:top-[-6px] modal-close hover:bg-gray-100 
        text-red rounded p-2">
            <x-icon icon="x" width=16 height=16 viewBox="20 20" />
        </button>
        <div class="flex flex-col gap-1">
            <div class="flex items-start gap-2">
                <div class="rounded-full bg-violet-50 p-2.5 box-border mr-2 text-violet-800">
                    <x-icon icon="settings" width=20 height=20 viewBox="20 20" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 xs/max:text-xl xs/max:font-semibold">New role</p>
                    <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                        Please provide the role's detail.
                    </p>
                </div>
            </div>
        </div>
        <hr>
    </header>
    <form autocomplete="off" action="{{ route('access-control.store') }}" method="POST" class="submit-role">
        @csrf
        <!-- {{ csrf_field() }} -->
        <main class="px-4 flex flex-col gap-4 xs/max:gap-3 mb-8">
            <section class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">name*</label>
                {!! FormCustom::input('name', null, [ "placeholder" => 'Enter new your access control name'])
                !!}
            </section>
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
                        <input type="checkbox" class="accent-green-500 user-check" name="roles[]" value="user.view">
                        View
                        user
                    </label>
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                        <input type="checkbox" class="accent-green-500 user-check" name="roles[]" value="user.create">
                        Add user
                    </label>
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                        <input type="checkbox" class="accent-green-500 user-check" name="roles[]" value="user.update">
                        Edit
                        user
                    </label>
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                        <input type="checkbox" class="accent-green-500 user-check" name="roles[]" value="user.delete">
                        Delete
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
                        <input type="checkbox" class="accent-green-500 employee-check" name="roles[]"
                            value="employee.view">
                        View employee
                    </label>
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                        <input type="checkbox" class="accent-green-500 employee-check" name="roles[]"
                            value="employee.create">
                        Add employee
                    </label>
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                        <input type="checkbox" class="accent-green-500 employee-check" name="roles[]"
                            value="employee.update">
                        Edit employee
                    </label>
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                        <input type="checkbox" class="accent-green-500 employee-check" value='employee.delete'
                            name="roles[]"> Delete employee
                    </label>
                </div>
            </div>
        </main>
        <hr>
        <footer class="flex justify-end items-center gap-3 p-4  pb-6">
            <button type="reset"
                class="modal-close shadow text-gray-500 bg-white hover:bg-gray-100 focus:ring-2 focus:ring-gray-300 rounded-lg xs/max:rounded-md border border-gray-200 text-sm xs/max:text-xs font-medium xs/max:px-4 px-6 xs/max:py-1.5 py-2 hover:text-gray-900 focus:z-10">
                Cancel</button>
            <button type="submit"
                class="text-white shadow bg-green-600 hover:bg-green-700 focus:ring-2 focus:ring-green-700 font-medium rounded-lg xs/max:rounded-md text-sm xs/max:text-xs inline-flex items-center xs/max:px-4 px-6 xs/max:py-1.5 py-2 text-center">Done</button>
        </footer>
    </form>
    </div>
</main>