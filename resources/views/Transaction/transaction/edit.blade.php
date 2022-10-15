<form autocomplete="off" action="{{ route('employee.update', ['employee' => $emp['id']]) }}" method="POST"
    class="submit-employee flex items-start gap-5 justify-center">
    @csrf
    <!-- {{ csrf_field() }} -->
    <section
        class="flex flex-col gap-8 pt-4 bg-white w-[850px] max-h-[90vh] overflow-y-auto overflow-x-hidden relative rounded-lg">
        <header class="px-4 flex flex-col gap-5 pt-4 xs/max:gap-3 relative">
            <button
                class="absolute top-[-5px] right-3 xs/max:top-[-6px] modal-close hover:bg-gray-100 text-red rounded p-2">
                <x-icon icon="x" width=16 height=16 viewBox="20 20" />
            </button>
            <div class="flex flex-col gap-1">
                <div class="flex items-start gap-2">
                    <div
                        class="rounded-full bg-violet-100 p-1.5 border-[4px] border-violet-50 box-border mr-2 text-violet-800">
                        <x-icon icon="users" width=18 height=18 viewBox="20 20" />
                    </div>
                    <div>
                        <p class="text-xl font-semibold text-gray-900">New employee
                        </p>
                        <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                            Please provide the employee's detail.
                        </p>
                    </div>
                </div>
            </div>
            <hr>
        </header>
        <div>
            <main class="px-4 flex flex-col gap-8 xs/max:gap-3 mb-8">
                <div class="flex flex-col gap-4">
                    <div>
                        <p class="text-lg font-medium text-gray-900
                        xs/max:font-semibold"> Profil karyawan
                        </p>
                        <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                            Tolong lengkapi data-data karyawan ini
                        </p>
                    </div>
                    <hr>
                    <div class="flex gap-3">
                        <div class="flex justify-start items-start flex-col gap-3 flex-1">
                            <section class="flex flex-col gap-1 w-full">
                                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Employee code*</label>
                                {!! FormCustom::input('emp_code', $emp['emp_code'],
                                [ "placeholder" => 'Enter new your employee code'])!!}
                            </section>
                            <section class="flex flex-col gap-1 w-full">
                                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Department*</label>
                                <select class="select2" name="department">
                                    <option value="" disabled selected>Silahkan Pilih</option>
                                    @foreach ($departments['data'] as $item)
                                    <option value="{{ $item['id'] }}" {{ (!empty($emp['department'])
                                        &&($item['id']==$emp['department']['id'])) ? 'selected' : '' }}>{{
                                        $item['dept_name'] }}</option>
                                    @endforeach
                                </select>
                                <label class="font-normal text-xs text-red-500 xs/max:text-xs 
                                department hint-text"></label>
                            </section>
                            <section class="flex flex-col gap-1 w-full">
                                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Position</label>
                                <select class="select2" name="position">
                                    <option value="" disabled selected>Silahkan Pilih</option>
                                    @foreach ($positions['data'] as $item)
                                    <option value="{{ $item['id'] }}" {{ (!empty($emp['position'])
                                        &&($item['id']==$emp['position']['id'])) ? 'selected' : '' }}>{{
                                        $item['position_name'] }}</option>
                                    @endforeach
                                </select>
                                <label class="font-normal text-xs text-red-500 xs/max:text-xs 
                                position hint-text"></label>
                            </section>
                            <section class="flex flex-col gap-1 w-full">
                                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Employment type</label>
                                <select class="select2" name="emp_type">
                                    <option value="" disabled selected>Silahkan Pilih</option>
                                    <option value="1" {{ $emp['emp_type']==1 ? 'selected' : '' }}>Official</option>
                                    <option value="2" {{ $emp['emp_type']==2 ? 'selected' : '' }}>Temporary
                                    </option>
                                    <option value="3" {{ $emp['emp_type']==3 ? 'selected' : '' }}>Probation
                                    </option>
                                </select>
                                <label class="font-normal text-xs text-red-500 xs/max:text-xs 
                                emp_type hint-text"></label>
                            </section>
                        </div>
                        <div class="flex justify-start flex-col gap-3 flex-1">
                            <section class="flex flex-col gap-1">
                                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">First name*</label>
                                {!! FormCustom::input('first_name', $emp['first_name'],
                                [ "placeholder" => 'Enter new your first name'])
                                !!}
                            </section>
                            <section class="flex flex-col gap-1">
                                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Last name</label>
                                {!! FormCustom::input('last_name', $emp['last_name'],
                                [ "placeholder" => 'Enter new your last name'])
                                !!}
                            </section>
                            <div class="flex flex-col gap-1">
                                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Hired date</label>
                                {!! FormCustom::input('hired_date', $emp['hire_date'],
                                [ 'placeholder' => 'Enter new your start date', 'class' => 'date_input',
                                'readonly' => true, 'prefixiconname' => 'calendar' ]) !!}
                            </div>
                            <section class="flex flex-col gap-1 flex-1">
                                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Area*</label>
                                <select class="select2" name="area[]" multiple="multiple">
                                    <option value="all" {{ empty($emp['area']) ? 'selected' : '' }}>
                                        Select all
                                    </option>
                                    @foreach ($areas['data'] as $item)
                                    <option value="{{ $item['id'] }}" {{ (!empty($emp['area'])) &&
                                        array_search($item['id'], array_column($emp['area'], 'id' )) !==false
                                        ? 'selected' : '' }}>{{ $item['area_name'] }}</option>
                                    @endforeach
                                </select>
                                <label class="font-normal text-xs text-red-500 xs/max:text-xs 
                                area hint-text"></label>
                            </section>
                        </div>
                        <div class="flex justify-center items-center flex-1">
                            <label for="contained-button-file" class="flex items-center cursor-pointer bg-gray-50">
                                <input name="photo" accept="image/*" id="contained-button-file" class="hidden"
                                    type="file" onchange="loadPic('#photo', 'photo_preview', '#remove-img')" />
                                <span class='cursor-pointer flex w-32 h-32 border border-dashed p-2'>
                                    @if (!empty($emp['photo']))
                                    <img src="@zkPhoto({{ $emp['photo'] }})"
                                        class='object-cover h-full w-full overflow-hidden' id="photo_preview">
                                    @else
                                    <img src='@zkPhoto(files/nophoto.gif)'
                                        class='object-cover h-full w-full overflow-hidden' id="photo_preview">
                                    @endif
                                </span>
                            </label>
                            <input type="hidden" name="photo" id="photo">
                        </div>
                    </div>
                </div>
                <hr>
                <div class="flex flex-col gap-4">
                    <div>
                        <p class="text-lg font-medium text-gray-900
                            xs/max:font-semibold">Private information
                        </p>
                        <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                            Tolong lengkapi data-data karyawan ini
                        </p>
                    </div>
                    <hr>
                    <div class="flex gap-3">
                        <div class="flex flex-col gap-3 flex-1">
                            <section class="flex flex-col gap-1">
                                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Local name</label>
                                {!! FormCustom::input('nickname', $emp['nickname'] ?? null, [ "placeholder" =>
                                'Enter new your local name'])
                                !!}
                            </section>
                            <section class="flex flex-col gap-1">
                                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Contact tel</label>
                                {!! FormCustom::input('contact_tel', $emp['contact_tel'], [ "placeholder" =>
                                'Enter new your contact name']) !!}
                            </section>
                            <section class="flex flex-col gap-1">
                                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">National</label>
                                {!! FormCustom::input('national', $emp['national'],
                                [ "placeholder" => 'Enter new your national']) !!}
                            </section>
                            <section class="flex flex-col gap-1">
                                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Address</label>
                                {!! FormCustom::input('address', $emp['address'],
                                [ "placeholder" => 'Enter new your address']) !!}
                            </section>
                        </div>
                        <div class="flex flex-col gap-3 flex-1">
                            <section class="flex flex-col gap-1 flex-1">
                                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Gender</label>
                                <select class="select2" name="gender">
                                    <option value="" disabled selected>Silahkan Pilih</option>
                                    <option value="M">Male</option>
                                    <option value="F">Famale</option>
                                </select>
                                <label class="font-normal text-xs text-red-500 xs/max:text-xs gender hint-text"></label>
                            </section>
                            <section class="flex flex-col gap-1">
                                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Office tel</label>
                                {!! FormCustom::input('office_tel', $emp['office_tel'], [ "placeholder" =>
                                'Enter new your office tel']) !!}
                            </section>
                            <section class="flex flex-col gap-1">
                                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Religion</label>
                                {!! FormCustom::input('religion', $emp['religion'],
                                [ "placeholder" => 'Enter new your religion']) !!}
                            </section>
                            <section class="flex flex-col gap-1">
                                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Postcode</label>
                                {!! FormCustom::input('postcode', $emp['postcode'],
                                [ "placeholder" => 'Enter new your postcode']) !!}
                            </section>
                        </div>
                        <div class="flex flex-col gap-3 flex-1">
                            <section class="flex flex-col gap-1">
                                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Birthday</label>
                                {!! FormCustom::input('birthday', $emp['birthday'],
                                [ 'placeholder' => 'Enter new your birthday',
                                'class' => 'date_input', 'readonly' => true, 'prefixiconname' => 'calendar' ]) !!}
                            </section>
                            <section class="flex flex-col gap-1">
                                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Mobile</label>
                                {!! FormCustom::input('mobile', $emp['mobile'],
                                [ "placeholder" => 'Enter new your mobile']) !!}
                            </section>
                            <section class="flex flex-col gap-1">
                                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">City</label>
                                {!! FormCustom::input('city', $emp['city'],
                                [ "placeholder" => 'Enter new your city']) !!}
                            </section>
                            <section class="flex flex-col gap-1">
                                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Email</label>
                                {!! FormCustom::input('email', $emp['email'],
                                [ "placeholder" => 'Enter new your email']) !!}
                            </section>
                        </div>
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
        </div>
    </section>
</form>