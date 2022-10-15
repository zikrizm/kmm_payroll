<div class="flex flex-col gap-8 xs/max:gap-6 pt-8 pb-8 xs/max:pt-6 xs/max:pb-6">
    <header class="px-4 flex flex-col gap-5 xs/max:gap-3">
        <div class="flex flex-col gap-1">
            <p class="text-xl font-semibold text-gray-900">New user</p>
            <p class="text-base font-normal text-gray-500 xs/max:text-sm">Lorem ipsum dolor sit, amet consectetur
                adipisicing
                elit.
            </p>
        </div>
        <hr>
    </header>

    <form autocomplete="off" action="{{ route('user.update', ['user'=>$user->id]) }}" method="PUT" class="submitUserM">
        @method('PUT')
        @csrf
        <!-- {{ csrf_field() }} -->
        <main class="px-4 flex flex-col xs/max:gap-6 gap-12 mb-8">
            <section class="flex flex-col gap-4 xs/max:gap-3">
                <div class="flex flex-col gap-1 gap-1">
                    <p class="text-lg font-semibold text-gray-900 xs/max:text-sm">User informations</p>
                    <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                        Lorem ipsum dolor sit, amet consectetur adipisicing elit.
                    </p>
                </div>
                <hr>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Name*</label>
                    <input
                        class="py-1.5 px-2.5 text-sm xs/max:text-xs rounded-lg xs/max:rounded shadow-sm border border-gray-300 focus:outline-none focus:ring-2 focus:shadow focus:ring-gray-300 focus:border-transparent"
                        type="text" placeholder="Enter new your name" name="name" required value="{{ $user->name }}" />
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs name text-error"></label>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Email*</label>
                    <input
                        class="py-1.5 px-2.5 text-sm xs/max:text-xs rounded-lg xs/max:rounded shadow-sm border border-gray-300 focus:outline-none focus:ring-2 focus:shadow focus:ring-gray-300 focus:border-transparent"
                        type="email" placeholder="Enter new your email" name="email" required autocomplete="off"
                        value="{{ $user->email }}" />
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs email text-error"></label>
                </div>
            </section>
            <section class="flex flex-col gap-4 xs/max:gap-3">
                <div class="flex flex-col gap-1">
                    <p class="text-lg font-semibold text-gray-900 xs/max:text-sm">Role & Permissions</p>
                    <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                        Lorem ipsum dolor sit, amet consectetur adipisicing elit.
                    </p>
                </div>
                <hr>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Status*</label>
                    <div class="border rounded-xl xs/max:rounded-lg w-max shadow-sm flex overflow-hidden">
                        <input type="hidden" name="status" id="status-user" value="{{ $user->status }}">
                        <button type="button"
                            class="text-sm xs/max:text-xs text-normal text-gray-600 px-4 py-1.5 border-r btn-status {{ $user->status == 'active' ? 'bg-gray-100': '' }}"
                            value="active">
                            Active
                        </button>
                        <button type="button"
                            class="text-sm xs/max:text-xs text-normal text-gray-600 px-4 py-1.5 btn-status {{ $user->status == 'inactive' ? 'bg-gray-100': '' }}"
                            value="inactive">
                            Inactive
                        </button>
                    </div>
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs status text-error"></label>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Username*</label>
                    <input
                        class="py-1.5 px-2.5 text-sm xs/max:text-xs rounded-lg xs/max:rounded shadow-sm border border-gray-300 focus:outline-none focus:ring-2 focus:shadow focus:ring-gray-300 focus:border-transparent"
                        type="text" placeholder="Enter new your username" name="username" required autocomplete="off"
                        value="{{ $user->username }}" />
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs username text-error"></label>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm xs/max:text-xs text-gray-500 xs/max:text-xs">Password*</label>
                    <input
                        class="py-1.5 px-2.5 text-sm xs/max:text-xs rounded-lg xs/max:rounded shadow-sm border border-gray-300 focus:outline-none focus:ring-2 focus:shadow focus:ring-gray-300 focus:border-transparent"
                        type="password" placeholder="Enter new your password" name="password" required
                        autocomplete="new-password" />
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs password text-error"></label>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Role*</label>
                    <select class="select2" name="role" required>
                        <option value="" selected>Silahkan Pilih</option>
                        @foreach ($roles as $role)
                        @if ($role->id == $user->roles[0]->id)
                        <option value="{{ $role->id }}" selected>{{ $role->name }}</option>
                        @else
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endif
                        @endforeach
                    </select>
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs role text-error"></label>
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