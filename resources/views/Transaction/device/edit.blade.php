<form autocomplete="off" action="{{ route('device.update', ['device' => $device['id']]) }}" method="POST"
    class="submit-device">
    @csrf
    <!-- {{ csrf_field() }} -->
    <main
        class="flex flex-col gap-8  pt-4 w-[375px] bg-white max-h-[90vh] overflow-y-auto overflow-x-hidden no-scrollbar relative rounded-lg">
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
                    {!! FormCustom::input('alias', $device['alias'], [ "placeholder" => 'Enter new your device name'])
                    !!}
                </section>
                <section class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Serial number*</label>
                    {!! FormCustom::input('sn', $device['sn'], [ "placeholder" => 'Enter new your serial number']) !!}
                </section>
                <section class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Area*</label>
                    <select class="select2" name="area">
                        <option value="" disabled selected>Silahkan Pilih</option>
                        @foreach ($areas['data'] as $item)
                        <option value="{{ $item['id'] }}" {{ !empty($device['area']) &&
                            $item['id']==$device['area']['id'] ? 'selected' : '' }}>{{ $item['area_name'] }}</option>
                        @endforeach
                    </select>
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs area hint-text"></label>
                </section>
                <section class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Attendance device*</label>
                    <select class="select2" name="is_attendance">
                        <option value="0" {{ $device['is_attendance']==0 ? 'selected' : '' }}>Yes</option>
                        <option value="1" {{ $device['is_attendance']==1 ? 'selected' : '' }}>No</option>
                    </select>
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs is_attendance hint-text"></label>
                </section>
                <section class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Device IP*</label>
                    {!! FormCustom::input('ip_address', $device['ip_address'], [ "placeholder" => 'Enter new your device
                    IP']) !!}
                </section>
                <section class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Time zone*</label>
                    <select class="select2" name="terminal_tz">
                        <option value="-750" {{ $device['terminal_tz']==-750 ? 'selected' : '' }}>Etc/GMT-12:30</option>
                        <option value="-750" {{ $device['terminal_tz']==-750 ? 'selected' : '' }}>Etc/GMT-12:30</option>
                        <option value="-12" {{ $device['terminal_tz']==-12 ? 'selected' : '' }}>Etc/GMT-12</option>
                        <option value="-690" {{ $device['terminal_tz']==-690 ? 'selected' : '' }}>Etc/GMT-11:30</option>
                        <option value="-11" {{ $device['terminal_tz']==-11 ? 'selected' : '' }}>Etc/GMT-11</option>
                        <option value="-630" {{ $device['terminal_tz']==-630 ? 'selected' : '' }}>Etc/GMT-10:30</option>
                        <option value="-10" {{ $device['terminal_tz']==-10 ? 'selected' : '' }}>Etc/GMT-10</option>
                        <option value="-570" {{ $device['terminal_tz']==-570 ? 'selected' : '' }}>Etc/GMT-9:30</option>
                        <option value="-9" {{ $device['terminal_tz']==-9 ? 'selected' : '' }}>Etc/GMT-9</option>
                        <option value="-510" {{ $device['terminal_tz']==-510 ? 'selected' : '' }}>Etc/GMT-8:30</option>
                        <option value="-8" {{ $device['terminal_tz']==-8 ? 'selected' : '' }}>Etc/GMT-8</option>
                        <option value="-450" {{ $device['terminal_tz']==-450 ? 'selected' : '' }}>Etc/GMT-7:30</option>
                        <option value="-7" {{ $device['terminal_tz']==-7 ? 'selected' : '' }}>Etc/GMT-7</option>
                        <option value="-390" {{ $device['terminal_tz']==-390 ? 'selected' : '' }}>Etc/GMT-6:30</option>
                        <option value="-6" {{ $device['terminal_tz']==-6 ? 'selected' : '' }}>Etc/GMT-6</option>
                        <option value="-330" {{ $device['terminal_tz']==-330 ? 'selected' : '' }}>Etc/GMT-5:30</option>
                        <option value="-5" {{ $device['terminal_tz']==-5 ? 'selected' : '' }}>Etc/GMT-5</option>
                        <option value="-270" {{ $device['terminal_tz']==-270 ? 'selected' : '' }}>Etc/GMT-4:30</option>
                        <option value="-4" {{ $device['terminal_tz']==-4 ? 'selected' : '' }}>Etc/GMT-4</option>
                        <option value="-210" {{ $device['terminal_tz']==-210 ? 'selected' : '' }}>Etc/GMT-3:30</option>
                        <option value="-3" {{ $device['terminal_tz']==-3 ? 'selected' : '' }}>Etc/GMT-3</option>
                        <option value="-150" {{ $device['terminal_tz']==-150 ? 'selected' : '' }}>Etc/GMT-2:30</option>
                        <option value="-2" {{ $device['terminal_tz']==-2 ? 'selected' : '' }}>Etc/GMT-2</option>
                        <option value="-90" {{ $device['terminal_tz']==-90 ? 'selected' : '' }}>Etc/GMT-1:30</option>
                        <option value="-1" {{ $device['terminal_tz']==-1 ? 'selected' : '' }}>Etc/GMT-1</option>
                        <option value="-30" {{ $device['terminal_tz']==-30 ? 'selected' : '' }}>Etc/GMT-0:30</option>
                        <option value="0" {{ $device['terminal_tz']==0 ? 'selected' : '' }}>Etc/GMT</option>
                        <option value="30" {{ $device['terminal_tz']==30 ? 'selected' : '' }}>Etc/GMT+0:30</option>
                        <option value="1" {{ $device['terminal_tz']==1 ? 'selected' : '' }}>Etc/GMT+1</option>
                        <option value="90" {{ $device['terminal_tz']==90 ? 'selected' : '' }}>Etc/GMT+1:30</option>
                        <option value="2" {{ $device['terminal_tz']==2 ? 'selected' : '' }}>Etc/GMT+2</option>
                        <option value="150" {{ $device['terminal_tz']==150 ? 'selected' : '' }}>Etc/GMT+2:30</option>
                        <option value="3" {{ $device['terminal_tz']==3 ? 'selected' : '' }}>Etc/GMT+3</option>
                        <option value="210" {{ $device['terminal_tz']==210 ? 'selected' : '' }}>Etc/GMT+3:30</option>
                        <option value="4" {{ $device['terminal_tz']==4 ? 'selected' : '' }}>Etc/GMT+4</option>
                        <option value="270" {{ $device['terminal_tz']==270 ? 'selected' : '' }}>Etc/GMT+4:30</option>
                        <option value="5" {{ $device['terminal_tz']==5 ? 'selected' : '' }}>Etc/GMT+5</option>
                        <option value="330" {{ $device['terminal_tz']==330 ? 'selected' : '' }}>Etc/GMT+5:30</option>
                        <option value="6" {{ $device['terminal_tz']==6 ? 'selected' : '' }}>Etc/GMT+6</option>
                        <option value="390" {{ $device['terminal_tz']==390 ? 'selected' : '' }}>Etc/GMT+6:30</option>
                        <option value="7" {{ $device['terminal_tz']==7 ? 'selected' : '' }}>Etc/GMT+7</option>
                        <option value="450" {{ $device['terminal_tz']==450 ? 'selected' : '' }}>Etc/GMT+7:30</option>
                        <option value="8" {{ $device['terminal_tz']==8 ? 'selected' : '' }}>Etc/GMT+8</option>
                        <option value="510" {{ $device['terminal_tz']==510 ? 'selected' : '' }}>Etc/GMT+8:30</option>
                        <option value="9" {{ $device['terminal_tz']==9 ? 'selected' : '' }}>Etc/GMT+9</option>
                        <option value="570" {{ $device['terminal_tz']==570 ? 'selected' : '' }}>Etc/GMT+9:30</option>
                        <option value="10" {{ $device['terminal_tz']==10 ? 'selected' : '' }}>Etc/GMT+10</option>
                        <option value="630" {{ $device['terminal_tz']==630 ? 'selected' : '' }}>Etc/GMT+10:30</option>
                        <option value="11" {{ $device['terminal_tz']==11 ? 'selected' : '' }}>Etc/GMT+11</option>
                        <option value="690" {{ $device['terminal_tz']==690 ? 'selected' : '' }}>Etc/GMT+11:30</option>
                        <option value="12" {{ $device['terminal_tz']==12 ? 'selected' : '' }}>Etc/GMT+12</option>
                        <option value="750" {{ $device['terminal_tz']==750 ? 'selected' : '' }}>Etc/GMT+12:30</option>
                        <option value="13" {{ $device['terminal_tz']==13 ? 'selected' : '' }}>Etc/GMT+13</option>
                        <option value="810" {{ $device['terminal_tz']==810 ? 'selected' : '' }}>Etc/GMT+13:30</option>
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