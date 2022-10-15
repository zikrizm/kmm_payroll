<main class="flex flex-col gap-8  pt-4 w-[375px] xs/max:w-[280px]">
    <header class="px-4 flex flex-col gap-5 pt-4 xs/max:gap-3 relative">
        <button class="absolute top-[-5px] right-3 xs/max:top-[-6px] modal-close hover:bg-gray-100 
        text-red rounded p-2">
            <x-icon icon="x" width=16 height=16 viewBox="20 20" />
        </button>
        <div class="flex flex-col gap-1">
            <div class="flex items-start gap-2">
                <div
                    class="rounded-full bg-violet-100 p-1.5 border-[4px] border-violet-50 box-border mr-2 text-violet-800">
                    <x-icon icon="settings" width=18 height=18 viewBox="20 20" />
                </div>
                <div>
                    <p class="text-xl font-semibold text-gray-900">New role</p>
                    <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                        Please provide the role's detail.
                    </p>
                </div>
            </div>
        </div>
        <hr>
    </header>
    <form autocomplete="off" action="{{ route('access-controls.update', ['access_control' => $role->id ]) }}"
        method="POST" class="submit-role">
        @csrf
        <!-- {{ csrf_field() }} -->
        <main class="px-4 flex flex-col gap-4 xs/max:gap-3 mb-8">
            <section class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">name*</label>
                {!! FormCustom::input('name', $role->name, [ "placeholder" => 'Enter new your access control name'])
                !!}
            </section>
            <hr>
            <div class="flex flex-col gap-4">
                <div class="flex flex-col">
                    <p class="font-normal text-base font-medium text-gray-700 xs/max:text-sm">User
                        management</p>
                    <p class="font-normal text-sm text-gray-500 xs/max:text-xs">Please select access for
                        users</p>
                </div>
                <div class="flex flex-col flex-1 gap-1">
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100 mb-3">
                        <input type="checkbox" class="accent-violet-500"
                            onchange="onSelectAllCheckbox(this,'.user-check')"> Select all
                    </label>
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                        <input {{ in_array('user.view', $role_permissions ) ? 'checked' : '' }} type="checkbox"
                            class="accent-violet-500 user-check" name="roles[]" value="user.view">
                        View user
                    </label>
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                        <input {{ in_array('user.create', $role_permissions ) ? 'checked' : '' }} type="checkbox"
                            class="accent-violet-500 user-check" name="roles[]" value="user.create">
                        Add user
                    </label>
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                        <input {{ in_array('user.update', $role_permissions ) ? 'checked' : '' }} type="checkbox"
                            class="accent-violet-500 user-check" name="roles[]" value="user.update">
                        Edit
                        user
                    </label>
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                        <input {{ in_array('user.delete', $role_permissions ) ? 'checked' : '' }} type="checkbox"
                            class="accent-violet-500 user-check" name="roles[]" value="user.delete">
                        Delete
                        user
                    </label>
                </div>
            </div>
            <hr>
            <div class="flex flex-col gap-4">
                <div class="flex flex-col">
                    <p class="font-normal text-base font-medium text-gray-700 xs/max:text-sm">Access control
                        management</p>
                    <p class="font-normal text-sm text-gray-500 xs/max:text-xs">Please select access for
                        role</p>
                </div>
                <div class="flex flex-col flex-1 gap-1">
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100 mb-3">
                        <input type="checkbox" class="accent-violet-500"
                            onchange="onSelectAllCheckbox(this,'.access-control-check')"> Select all
                    </label>
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                        <input {{ in_array('access-control.view', $role_permissions ) ? 'checked' : '' }}
                            type="checkbox" class="accent-violet-500 access-control-check" name="roles[]"
                            value="access-control.view">
                        View access-control
                    </label>
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                        <input {{ in_array('access-control.view', $role_permissions ) ? 'checked' : '' }}
                            type="checkbox" class="accent-violet-500 access-control-check" name="roles[]"
                            value="access-control.create">
                        Add access-control
                    </label>
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                        <input {{ in_array('access-control.view', $role_permissions ) ? 'checked' : '' }}
                            type="checkbox" class="accent-violet-500 access-control-check" name="roles[]"
                            value="access-control.update">
                        Edit access-control
                    </label>
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                        <input {{ in_array('access-control.delete', $role_permissions ) ? 'checked' : '' }}
                            type="checkbox" class="accent-violet-500 access-control-check" value='access-control.delete'
                            name="roles[]"> Delete access-control
                    </label>
                </div>
            </div>
            <hr>
            <hr>
            <div class="flex flex-col gap-4">
                <div class="flex flex-col">
                    <p class="font-normal text-base font-medium text-gray-700 xs/max:text-sm">Shift
                        management</p>
                    <p class="font-normal text-sm text-gray-500 xs/max:text-xs">Please select access for
                        shift</p>
                </div>
                <div class="flex flex-col flex-1 gap-1">
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100 mb-3">
                        <input type="checkbox" class="accent-violet-500"
                            onchange="onSelectAllCheckbox(this,'.shift-check')"> Select all
                    </label>
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                        <input {{ in_array('shift.view', $role_permissions ) ? 'checked' : '' }} type="checkbox"
                            class="accent-violet-500 shift-check" name="roles[]" value="shift.view">
                        View shift
                    </label>
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                        <input {{ in_array('shift.view', $role_permissions ) ? 'checked' : '' }} type="checkbox"
                            class="accent-violet-500 shift-check" name="roles[]" value="shift.create">
                        Add shift
                    </label>
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                        <input {{ in_array('shift.view', $role_permissions ) ? 'checked' : '' }} type="checkbox"
                            class="accent-violet-500 shift-check" name="roles[]" value="shift.update">
                        Edit shift
                    </label>
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                        <input {{ in_array('shift.delete', $role_permissions ) ? 'checked' : '' }} type="checkbox"
                            class="accent-violet-500 shift-check" value='shift.delete' name="roles[]"> Delete shift
                    </label>
                </div>
            </div>
            <hr>
            <div class="flex flex-col gap-4">
                <div class="flex flex-col">
                    <p class="font-normal text-base font-medium text-gray-700 xs/max:text-sm">Group
                        management</p>
                    <p class="font-normal text-sm text-gray-500 xs/max:text-xs">Please select access for
                        group</p>
                </div>
                <div class="flex flex-col flex-1 gap-1">
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100 mb-3">
                        <input type="checkbox" class="accent-violet-500"
                            onchange="onSelectAllCheckbox(this,'.group-check')"> Select all
                    </label>
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                        <input {{ in_array('group.view', $role_permissions ) ? 'checked' : '' }} type="checkbox"
                            class="accent-violet-500 group-check" name="roles[]" value="group.view">
                        View group
                    </label>
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                        <input {{ in_array('group.view', $role_permissions ) ? 'checked' : '' }} type="checkbox"
                            class="accent-violet-500 group-check" name="roles[]" value="group.create">
                        Add group
                    </label>
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                        <input {{ in_array('group.view', $role_permissions ) ? 'checked' : '' }} type="checkbox"
                            class="accent-violet-500 group-check" name="roles[]" value="group.update">
                        Edit group
                    </label>
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                        <input {{ in_array('group.delete', $role_permissions ) ? 'checked' : '' }} type="checkbox"
                            class="accent-violet-500 group-check" value='group.delete' name="roles[]"> Delete group
                    </label>
                </div>
            </div>
            <hr>
            <div class="flex flex-col gap-4">
                <div class="flex flex-col">
                    <p class="font-normal text-base font-medium text-gray-700 xs/max:text-sm">Work section
                        management</p>
                    <p class="font-normal text-sm text-gray-500 xs/max:text-xs">Please select access for
                        work section</p>
                </div>
                <div class="flex flex-col flex-1 gap-1">
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100 mb-3">
                        <input type="checkbox" class="accent-violet-500"
                            onchange="onSelectAllCheckbox(this,'.work-section-check')"> Select all
                    </label>
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                        <input {{ in_array('work-section.view', $role_permissions ) ? 'checked' : '' }} type="checkbox"
                            class="accent-violet-500 work-section-check" name="roles[]" value="work-section.view">
                        View work-section
                    </label>
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                        <input {{ in_array('work-section.view', $role_permissions ) ? 'checked' : '' }} type="checkbox"
                            class="accent-violet-500 work-section-check" name="roles[]" value="work-section.create">
                        Add work-section
                    </label>
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                        <input {{ in_array('work-section.view', $role_permissions ) ? 'checked' : '' }} type="checkbox"
                            class="accent-violet-500 work-section-check" name="roles[]" value="work-section.update">
                        Edit work-section
                    </label>
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                        <input {{ in_array('work-section.delete', $role_permissions ) ? 'checked' : '' }}
                            type="checkbox" class="accent-violet-500 work-section-check" value='work-section.delete'
                            name="roles[]">
                        Delete work-section
                    </label>
                </div>
            </div>
            <hr>
            <div class="flex flex-col gap-4">
                <div class="flex flex-col">
                    <p class="font-normal text-base font-medium text-gray-700 xs/max:text-sm">Holiday
                        management</p>
                    <p class="font-normal text-sm text-gray-500 xs/max:text-xs">Please select access for
                        holiday</p>
                </div>
                <div class="flex flex-col flex-1 gap-1">
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100 mb-3">
                        <input type="checkbox" class="accent-violet-500"
                            onchange="onSelectAllCheckbox(this,'.holiday-check')"> Select all
                    </label>
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                        <input {{ in_array('holiday.view', $role_permissions ) ? 'checked' : '' }} type="checkbox"
                            class="accent-violet-500 holiday-check" name="roles[]" value="holiday.view">
                        View holiday
                    </label>
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                        <input {{ in_array('holiday.view', $role_permissions ) ? 'checked' : '' }} type="checkbox"
                            class="accent-violet-500 holiday-check" name="roles[]" value="holiday.create">
                        Add holiday
                    </label>
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                        <input {{ in_array('holiday.view', $role_permissions ) ? 'checked' : '' }} type="checkbox"
                            class="accent-violet-500 holiday-check" name="roles[]" value="holiday.update">
                        Edit holiday
                    </label>
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                        <input {{ in_array('holiday.delete', $role_permissions ) ? 'checked' : '' }} type="checkbox"
                            class="accent-violet-500 holiday-check" value='holiday.delete' name="roles[]"> Delete
                        holiday
                    </label>
                </div>
            </div>
            <hr>
            <div class="flex flex-col gap-2">
                <div class="flex flex-col">
                    <p class="font-normal text-base font-medium text-gray-700 xs/max:text-sm">Employee
                        management</p>
                    <p class="font-normal text-sm text-gray-500 xs/max:text-xs">Please select access for
                        employee</p>
                </div>
                <div class="flex flex-col flex-1 gap-1">
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100 mb-3">
                        <input type="checkbox" class="accent-violet-500"
                            onchange="onSelectAllCheckbox(this,'.employee-check')"> Select all
                    </label>
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                        <input {{ in_array('employee.view', $role_permissions ) ? 'checked' : '' }} type="checkbox"
                            class="accent-violet-500 employee-check" name="roles[]" value="employee.view">
                        View employee
                    </label>
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                        <input {{ in_array('employee.view', $role_permissions ) ? 'checked' : '' }} type="checkbox"
                            class="accent-violet-500 employee-check" name="roles[]" value="employee.create">
                        Add employee
                    </label>
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                        <input {{ in_array('employee.view', $role_permissions ) ? 'checked' : '' }} type="checkbox"
                            class="accent-violet-500 employee-check" name="roles[]" value="employee.update">
                        Edit employee
                    </label>
                    <label
                        class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                        <input {{ in_array('employee.delete', $role_permissions ) ? 'checked' : '' }} type="checkbox"
                            class="accent-violet-500 employee-check" value='employee.delete' name="roles[]"> Delete
                        employee
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
                class="text-white shadow bg-violet-600 hover:bg-violet-700 focus:ring-2 focus:ring-violet-700 font-medium rounded-lg xs/max:rounded-md text-sm xs/max:text-xs inline-flex items-center xs/max:px-4 px-6 xs/max:py-1.5 py-2 text-center">Done</button>
        </footer>
    </form>
</main>