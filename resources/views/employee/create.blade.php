<main class="flex flex-col gap-8 pt-4 w-full">
    <header class="px-4 flex flex-col gap-5 pt-4 xs/max:gap-3 relative">
        <button
            class="absolute top-[-5px] right-3 xs/max:top-[-6px] modal-close hover:bg-gray-100 
        text-red rounded p-2">
            <x-icon icon="x" width=16 height=16 viewBox="20 20" />
        </button>
        <div class="flex flex-col gap-1">
            <div class="flex items-start gap-2">
                <div
                    class="rounded-full bg-violet-100 p-1.5 border-[4px] border-violet-50 box-border mr-2 text-violet-800">
                    <x-icon icon="user" width=18 height=18 viewBox="20 20" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 xs/max:text-xl xs/max:font-semibold">New employee</p>
                    <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                        Please provide the employee's detail.
                    </p>
                </div>
            </div>
        </div>
        <hr>
    </header>
    <form autocomplete="off" action="{{ route('employees.store') }}" method="POST" class="submit-employee">
        @csrf
        <!-- {{ csrf_field() }} -->
        <main class="px-4 flex flex-col gap-4 xs/max:gap-3 mb-8">
            {{-- <div>
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
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Status*</label>
                {!! FormCustom::togglebutton('status', null, ['Active', 'Inactive'], []) !!}
            </section>
            <section class="flex gap-3">
                <section class="flex flex-1 flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">First_name*</label>
                    {!! FormCustom::input('first_name', null, ['placeholder' => 'Enter new your first name']) !!}
                </section>
                <section class="flex flex-1 flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Last name</label>
                    {!! FormCustom::input('last_name', null, ['placeholder' => 'Enter new your last name']) !!}
                </section>
                <section class="flex flex-1 flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Nickname</label>
                    {!! FormCustom::input('nickname', null, ['placeholder' => 'Enter new your nickname']) !!}
                </section>
            </section>
            <section class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Gender*</label>
                {!! FormCustom::togglebutton('gender', null, ['Pria', 'Wanita'], []) !!}
            </section>
            <section class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Payment period*</label>
                <select class="select2" name="payment_period" required>
                    <option value="" disabled selected>Silahkan Pilih</option>
                    <option value="mounthly">Mounthly</option>
                    <option value="weekly">Weekly</option>
                    <option value="daily">Daily</option>
                </select>
                <label class="font-normal text-xs text-red-500 xs/max:text-xs payment_period hint-text"></label>
            </section>
            <section class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Work section*</label>
                <select class="select2" name="work_section_id" required>
                    <option value="" disabled selected>Silahkan Pilih</option>
                    @foreach ($work_sections as $work_section)
                        <option value="{{ $work_section->id }}">{{ $work_section->name }}</option>
                    @endforeach
                </select>
                <label class="font-normal text-xs text-red-500 xs/max:text-xs work_section_id hint-text"></label>
            </section>
            <section class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Group*</label>
                <select class="select2" name="group_id" required>
                    <option value="" disabled selected>Silahkan Pilih</option>
                    @foreach ($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
                <label class="font-normal text-xs text-red-500 xs/max:text-xs group_id hint-text"></label>
            </section>
            <section class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Daily salary*</label>
                {!! FormCustom::input('daily_salary', null, ['class' => 'number', 'placeholder' => '-', 'prefixtext' => 'Rp']) !!}
            </section>
            <section class="flex flex-col gap-1">
                <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Pay component*</label>
                {!! FormCustom::input('pay_component', null, ['class' => 'number', 'placeholder' => '-', 'prefixtext' => 'Rp']) !!}
            </section> --}}
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