<form autocomplete="off" action="{{ route('device.store') }}" method="POST" class="submit-device">
    @csrf
    <!-- {{ csrf_field() }} -->
    <main
        class="flex flex-col gap-8  pt-4 w-[375px] bg-white max-h-[95vh] overflow-y-auto overflow-x-hioptionen relative rounded-lg">
        <header class="px-4 flex flex-col gap-5 pt-4 xs/max:gap-3 relative">
            <button
                class="absolute top-[-5px] right-3 xs/max:top-[-6px] modal-close hover:bg-gray-100 text-red rounded p-2">
                <x-icon icon="x" width=16 height=16 viewBox="20 20" />
            </button>
            <div class="flex flex-col gap-1">
                <div class="flex items-start gap-2">
                    <div
                        class="rounded-full bg-violet-100 p-1.5 border-[4px] border-violet-50 box-border mr-2 text-violet-800">
                        <x-icon icon="smartphone" width=18 height=18 viewBox="20 20" />
                    </div>
                    <div>
                        <p class="text-xl font-semibold text-gray-900">New device
                        </p>
                        <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                            Please provide the device's detail.
                        </p>
                    </div>
                </div>
            </div>
            <hr>
        </header>
        <div>
            <main class="px-4 flex flex-col gap-4 xs/max:gap-3 mb-8">
                <section class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Device name*</label>
                    {!! FormCustom::input('alias', null, [ "placeholder" => 'Enter new your device name']) !!}
                </section>
                <section class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Serial number*</label>
                    {!! FormCustom::input('sn', null, [ "placeholder" => 'Enter new your serial number']) !!}
                </section>
                <section class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Area*</label>
                    <select class="select2" name="area">
                        <option value="" disabled selected>Silahkan Pilih</option>
                        @foreach ($areas['data'] as $item)
                        <option value="{{ $item['id'] }}">{{ $item['area_name'] }}</option>
                        @endforeach
                    </select>
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs area hint-text"></label>
                </section>
                <section class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Attendance device*</label>
                    <select class="select2" name="is_attendance">
                        <option value="0" selected>Yes</option>
                        <option value="1">No</option>
                    </select>
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs is_attendance hint-text"></label>
                </section>
                <section class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Device IP*</label>
                    {!! FormCustom::input('ip_address', null, [ "placeholder" => 'Enter new your device IP']) !!}
                </section>
                <section class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Time zone*</label>
                    <select class="select2" name="terminal_tz">
                        <option value="-750">Etc/GMT-12:30</option>
                        <option value="-750">Etc/GMT-12:30</option>
                        <option value="-12">Etc/GMT-12</option>
                        <option value="-690">Etc/GMT-11:30</option>
                        <option value="-11">Etc/GMT-11</option>
                        <option value="-630">Etc/GMT-10:30</option>
                        <option value="-10">Etc/GMT-10</option>
                        <option value="-570">Etc/GMT-9:30</option>
                        <option value="-9">Etc/GMT-9</option>
                        <option value="-510">Etc/GMT-8:30</option>
                        <option value="-8">Etc/GMT-8</option>
                        <option value="-450">Etc/GMT-7:30</option>
                        <option value="-7">Etc/GMT-7</option>
                        <option value="-390">Etc/GMT-6:30</option>
                        <option value="-6">Etc/GMT-6</option>
                        <option value="-330">Etc/GMT-5:30</option>
                        <option value="-5">Etc/GMT-5</option>
                        <option value="-270">Etc/GMT-4:30</option>
                        <option value="-4">Etc/GMT-4</option>
                        <option value="-210">Etc/GMT-3:30</option>
                        <option value="-3">Etc/GMT-3</option>
                        <option value="-150">Etc/GMT-2:30</option>
                        <option value="-2">Etc/GMT-2</option>
                        <option value="-90">Etc/GMT-1:30</option>
                        <option value="-1">Etc/GMT-1</option>
                        <option value="-30">Etc/GMT-0:30</option>
                        <option value="0">Etc/GMT</option>
                        <option value="30">Etc/GMT+0:30</option>
                        <option value="1">Etc/GMT+1</option>
                        <option value="90">Etc/GMT+1:30</option>
                        <option value="2">Etc/GMT+2</option>
                        <option value="150">Etc/GMT+2:30</option>
                        <option value="3">Etc/GMT+3</option>
                        <option value="210">Etc/GMT+3:30</option>
                        <option value="4">Etc/GMT+4</option>
                        <option value="270">Etc/GMT+4:30</option>
                        <option value="5">Etc/GMT+5</option>
                        <option value="330">Etc/GMT+5:30</option>
                        <option value="6">Etc/GMT+6</option>
                        <option value="390">Etc/GMT+6:30</option>
                        <option value="7" selected>Etc/GMT+7</option>
                        <option value="450">Etc/GMT+7:30</option>
                        <option value="8">Etc/GMT+8</option>
                        <option value="510">Etc/GMT+8:30</option>
                        <option value="9">Etc/GMT+9</option>
                        <option value="570">Etc/GMT+9:30</option>
                        <option value="10">Etc/GMT+10</option>
                        <option value="630">Etc/GMT+10:30</option>
                        <option value="11">Etc/GMT+11</option>
                        <option value="690">Etc/GMT+11:30</option>
                        <option value="12">Etc/GMT+12</option>
                        <option value="750">Etc/GMT+12:30</option>
                        <option value="13">Etc/GMT+13</option>
                        <option value="810">Etc/GMT+13:30</option>
                    </select>
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs terminal_tz hint-text"></label>
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
        </div>
    </main>
</form>