<main class="flex flex-col gap-8  pt-4 w-[375px] xs/max:w-[280px]">
    <header class="px-4 flex flex-col gap-5 pt-4 xs/max:gap-3 relative">
        <button class="absolute top-[-5px] right-3 xs/max:top-[-6px] modal-close hover:bg-gray-100 
        text-red rounded p-2">
            <x-icon icon="x" width=16 height=16 viewBox="20 20" />
        </button>
        <div class="flex flex-col gap-1">
            <div class="flex items-start gap-2">
                <div class="rounded-full bg-violet-50 p-2.5 box-border mr-2 text-violet-800">
                    <x-icon icon="user" width=20 height=20 viewBox="20 20" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 xs/max:text-xl xs/max:font-semibold">New user</p>
                    <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                        Please provide the user's detail.
                    </p>
                </div>
            </div>
        </div>
        <hr>
    </header>
    <form autocomplete="off" action="{{ route('users.store') }}" method="POST" class="submit-user">
        @csrf
        <!-- {{ csrf_field() }} -->
        <main class="px-4 flex flex-col gap-4 xs/max:gap-3 mb-8">
            <div>
                <p class='text-gray-700 text-sm font-medium'>Your photo</p>
                <p class='text-gray-500 text-sm font-normal'>This will be displayed on your profile.</p>
            </div>
            <div class='flex justify-between items-start'>
                <div class="relative">
                    <button type="button" id="remove-img"
                        class="hidden absolute right-0 bg-red-500 rounded-full text-white p-0.5"
                        onclick="removePhoto('#photo', '#photo_preview','#contained-button-file', this)">
                        <x-icon icon="x" width=12 height=12 viewBox="20 20" />
                    </button>
                    <img src='' class='object-contain h-16 w-16 bg-gray-50 rounded-full overflow-hidden'
                        id="photo_preview">
                </div>
                <label for="contained-button-file" class="flex items-center cursor-pointer">
                    <input name="photo" accept="image/*" id="contained-button-file" class="hidden" type="file"
                        onchange="loadPic('#photo', 'photo_preview', '#remove-img')" />
                    <span class='cursor-pointer text-sm font-medium text-violet-700 hover:bg-gray-100 rounded p-1'>
                        Upload
                    </span>
                </label>
                <input type="hidden" name="photo" id="photo">
            </div>
            <section class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">prefix</label>
                {!! FormCustom::input('surname', null, [ "placeholder" => 'Enter new your prefix'])
                !!}
            </section>
            <section class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">first name*</label>
                {!! FormCustom::input('first_name', null, [ "placeholder" => 'Enter new your first_name']) !!}
            </section>
            <section class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">last name</label>
                {!! FormCustom::input('last_name', null, [ "placeholder" => 'Enter new your last name']) !!}
            </section>
            <section class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Username*</label>
                {!! FormCustom::input('username', null, [ "placeholder" => 'Enter new your username']) !!}
            </section>
            <section class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Email*</label>
                {!! FormCustom::input('email', null, [ "placeholder" => 'Enter new your email', 'type' => 'email'])
                !!}
            </section>
            <section class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Password*</label>
                {!! FormCustom::input('password', null, [ "placeholder" => 'Enter new your passowrd', 'type' =>
                'password']) !!}
            </section>
            <section class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Status*</label>
                <div class="border rounded-xl xs/max:rounded-lg w-max shadow-sm flex overflow-hidden">
                    <input type="hidden" name="status" id="status-user" value="">
                    <button type="button"
                        class="text-sm xs/max:text-xs text-normal text-gray-600 px-4 py-1.5 border-r btn-status"
                        value="active">
                        Active
                    </button>
                    <button type="button"
                        class="text-sm xs/max:text-xs text-normal text-gray-600 px-4 py-1.5 btn-status"
                        value="inactive">
                        Inactive
                    </button>
                </div>
                <label class="font-normal text-xs text-red-500 xs/max:text-xs status text-error"></label>
            </section>
            <section class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Role*</label>
                <select class="select2" name="role" required>
                    <option value="" selected>Silahkan Pilih</option>
                    @foreach ($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
                <label class="font-normal text-xs text-red-500 xs/max:text-xs role text-error"></label>
            </section>
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
    </div>
</main>