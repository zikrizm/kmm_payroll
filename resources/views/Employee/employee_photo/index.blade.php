@extends('layouts.app')
@section('title', 'Employee photo')
@section('css')
<style></style>
@endsection
@section('content')
<div class="flex items-center justify-center flex-1 h-full overflow-auto bg-gray-50  px-8 xs/max:px-4 pt-8 pb-12">
    <form action="{{ route('employee-photo.store') }}" method="post" class="employee-photo-submit w-full max-w-[320px] ">
        <main
            class="rounded-xl flex flex-col items-center gap-12 border py-6 px-4 shadow-lg bg-white relative">
            <div class="absolute top-[-70px] text-center">
                <p class="text-3xl font-medium truncate w-[320px]">{{ Auth::user()->business->name }}</p>
                <p class="text-sm text-gray-500 font-normal truncate">Here to upload employee photo.</p>
            </div>
            <section>
                <label for="contained-button-file" class="flex items-center cursor-pointer">
                    <input name="user_capture" accept="image/*" id="contained-button-file" class="hidden" type="file"
                        onchange="loadPic('#photo', 'photo_preview', '#remove-img')" />
                    <div
                        class='cursor-pointer overflow-hidden flex justify-center item-center w-32 h-32 rounded-full border-2 shadow text-gray-300'>
                        <span class="icon-default-image flex justify-center items-center"><x-icon icon="camera" width=70% height=70% viewBox="20 20" /></span>
                        <img src="" class='object-cover w-full h-full hidden' id="photo_preview">
                    </div>
                </label>
            </section>
            <div class="flex flex-col gap-2 w-full">
                <section class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500">Employee code*</label>
                    {!! FormCustom::input('employee_code', null,
                    [ "placeholder" => 'Enter new your employee code'])
                    !!}
                </section>
                <footer class="flex flex-col gap-6 items-center w-full mt-2">
                    <button
                        class="flex items-center justify-center gap-2 shadow-xs rounded-lg h-9 px-3 text-white font-normal flex items-center bg-violet-600 w-full">
                        Done
                    </button>
                </footer>
            </div>
        </main>
    </form>
</div>

<script type="application/javascript">
    window.addEventListener('DOMContentLoaded', async (event) => {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        // **
        // * submit form ----->
        // *
        var resSubmit = await ApiService.submit_form('.employee-photo-submit', (data) => {
            if(data.status == 'success') {
                $('.employee-photo-submit').trigger("reset");
                $('#photo_preview').attr('src', '');
                $('#photo_preview').addClass('hidden');
                $('.icon-default-image').removeClass('hidden');
            }
        });
    });
</script>
@endsection