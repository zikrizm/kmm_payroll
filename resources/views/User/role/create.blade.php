<form autocomplete="off" action="{{ route('role.store') }}" method="POST" class="submit-role">
    @csrf
    <!-- {{ csrf_field() }} -->
    <section
        class="flex flex-col gap-8  pt-4 w-[975px] bg-white max-h-[90vh] overflow-y-auto overflow-x-hidden relative rounded-lg">
        <header class="px-4 flex flex-col gap-5 pt-4 xs/max:gap-3 relative">
            <button
                class="absolute top-[-5px] right-3 xs/max:top-[-6px] modal-close hover:bg-gray-100 text-red rounded p-2">
                <x-icon icon="x" width=16 height=16 viewBox="20 20" />
            </button>
            <div class="flex flex-col gap-1">
                <div class="flex items-start gap-2">
                    <div
                        class="rounded-full bg-violet-100 p-1.5 border-[4px] border-violet-50 box-border mr-2 text-violet-800">
                        <x-icon icon="settings" width=18 height=18 viewBox="20 20" />
                    </div>
                    <div>
                        <p class="text-xl font-semibold text-gray-900">New manage role</p>
                        <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                            Please provide the role's detail.
                        </p>
                    </div>
                </div>
            </div>
            <hr>
        </header>
        <div>
            <main class="px-4 flex flex-col gap-4 xs/max:gap-3 mb-8">
                <section class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">name*</label>
                    {!! FormCustom::input('name', null, [ "placeholder" => 'Enter new your role name'])
                    !!}
                </section>
                <div class="grid grid-cols-3 gap-4">
                    @foreach ($permissions as $item)
                    <div class="flex flex-col gap-4">
                        <hr>
                        <div class="flex flex-col gap-4">
                            <div class="flex flex-col">
                                <p class="font-normal text-base font-medium text-gray-700 xs/max:text-sm">
                                    {{ $item['title'] }}
                                </p>
                                <p class="font-normal text-sm text-gray-500 xs/max:text-xs">{{ $item['subtitle'] }}</p>
                            </div>
                            <div class="flex flex-col flex-1 gap-1">
                                <label
                                    class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100 mb-3">
                                    <input type="checkbox" class="accent-violet-500"
                                        onchange="onSelectAllCheckbox(this,'.{{ $item['name'] }}-check')"> Select all
                                </label>
                                @foreach ($item['roles'] as $item_role)
                                <label
                                    class="text-sm xs/max:text-xs text-gray-500 flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1 cursor-pointer hover:bg-gray-100">
                                    <input type="checkbox" class="accent-violet-500 {{ $item['name'] }}-check"
                                        name="roles[]" value="{{ $item_role['name'] }}">
                                    {{ ucfirst(join(" ",array_reverse(explode('.', $item_role['name'])))) }}
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
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