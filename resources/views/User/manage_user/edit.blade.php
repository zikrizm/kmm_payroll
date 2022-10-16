<form autocomplete="off" action="{{ route('users.update', ['user' => $user->id]) }}" method="POST" class="submit-user">
    @csrf
    <!-- {{ csrf_field() }} -->
    <section
        class="flex flex-col gap-8 pt-4 w-[375px] bg-white max-h-[95vh] overflow-y-auto overflow-x-hidden relative rounded-lg">
        <header class="px-4 flex flex-col gap-5 pt-4 xs/max:gap-3 relative">
            <button
                class="absolute top-[-5px] right-3 xs/max:top-[-6px] modal-close hover:bg-gray-100 text-red rounded p-2">
                <x-icon icon="x" width=16 height=16 viewBox="20 20" />
            </button>
            <div class="flex flex-col gap-1">
                <div class="flex items-start gap-2">
                    <div
                        class="rounded-full bg-violet-100 p-1.5 border-[4px] border-violet-50 box-border mr-2 text-violet-800">
                        <x-icon icon="user" width=18 height=18 viewBox="20 20" />
                    </div>
                    <div>
                        <p class="text-xl font-semibold text-gray-900">Pengguna</p>
                        <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                            Harap berikan detail pengguna.
                        </p>
                    </div>
                </div>
            </div>
            <hr>
        </header>
        <div>
            <main class="px-4 flex flex-col gap-2.5 xs/max:gap-3 mb-8">
                <div>
                    <p class='text-gray-700 text-sm font-medium'>Foto Anda</p>
                    <p class='text-gray-500 text-sm font-normal'>Ini akan ditampilkan di profil Anda.</p>
                </div>
                <div class='flex justify-between items-start'>
                    <div class="relative">
                        <button type="button" id="remove-img"
                            class="hidden absolute right-0 bg-red-500 rounded-full text-white p-0.5"
                            onclick="removePhoto('#photo', '#photo_preview','#contained-button-file', this)">
                            <x-icon icon="x" width=12 height=12 viewBox="20 20" />
                        </button>
                        <div class="h-16 w-16 bg-gray-50 rounded-full overflow-hidden">
                            <img src="{{ $user->photo }}" class="object-contain w-full h-full" id="photo_preview">
                        </div>
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
                <div class="flex items-start gap-3">
                    <section class="flex flex-col gap-1 flex-1">
                        <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Nama</label>
                        {!! FormCustom::input('name', $user->name, ['placeholder' => 'Masukkan nama']) !!}
                    </section>
                    <section class="flex flex-col gap-1 flex-2">
                        <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Username*</label>
                        {!! FormCustom::input('username', $user->username, ['placeholder' => 'Masukkan username']) !!}
                    </section>
                </div>
                <section class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Email*</label>
                    {!! FormCustom::input('email', $user->email, ['placeholder' => 'Masukkan email', 'type' => 'email']) !!}
                </section>
                <section class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Password*</label>
                    {!! FormCustom::input('password', null, ['placeholder' => 'Masukkan password', 'type' => 'password']) !!}
                </section>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Wewenang*</label>
                    <select class="select2" name="role">
                        <option value="" selected>Silahkan Pilih</option>
                        @foreach ($roles as $role)
                            @if (count($user->roles) != 0 && $role->id == $user->roles[0]->id)
                                <option value="{{ $role->id }}" selected>{{ $role->name }}</option>
                            @else
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endif
                        @endforeach
                    </select>
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs role hint-text"></label>
                </div>
                <section class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Status*</label>
                    {!! FormCustom::togglebutton('status', $user->status, ['Active', 'Inactive'], []) !!}
                </section>
            </main>
            <hr>
            <footer class="flex justify-end items-center gap-3 p-4 pb-6">
                <button type="reset"
                    class="modal-close shadow text-gray-500 bg-white hover:bg-gray-100 focus:ring-2 focus:ring-gray-300 rounded-lg xs/max:rounded-md border border-gray-200 text-sm xs/max:text-xs font-medium xs/max:px-4 px-6 xs/max:py-1.5 py-2 hover:text-gray-900 focus:z-10">
                    Cancel</button>
                <button type="submit"
                    class="text-white shadow bg-violet-600 hover:bg-violet-700 focus:ring-2 focus:ring-violet-700 font-medium rounded-lg xs/max:rounded-md text-sm xs/max:text-xs inline-flex items-center xs/max:px-4 px-6 xs/max:py-1.5 py-2 text-center">Done</button>
            </footer>
        </div>
    </section>
</form>
