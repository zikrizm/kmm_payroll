@extends('layouts.app')
@section('title', 'Businness register')
@section('css')
    <style></style>
@endsection
@section('content')
    <div class="w-full h-full flex items-center justify-center">
        <div class="max-h-[960px] w-full max-w-[1440px] h-full flex ">
            <aside class="w-1/3  h-full bg-gray-800 pt-12 flex flex-col gap-y-10">
                <header class="pr-8 pl-12 flex flex-col gap-1">
                    <p class="text-white font-semibold text-3xl">Tambah bisnis</p>
                    <dd class="pb-8 text-sm font-normal text-white">Harap berikan informasi bisnis anda sampai selesai untuk
                        membuat bisnis baru</dd>
                </header>
                <main class="flex-1 pr-8 pl-12">
                    <ul class="stepper" data-stepper="stepper">
                        <li class="stepper-step flex gap-x-4">
                            <div class="stepper-head flex flex-col items-center" data-step='1'>
                                <div class="stepper-head-icon">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center bg-gray-100">
                                        <span class="block w-2.5 h-2.5 rounded-full bg-violet-600"></span>
                                    </div>
                                </div>
                                <div class="stepper-barrier flex-1 w-0.5 rounded my-1 bg-gray-500"></div>
                            </div>
                            <div class="stepper-content pt-1">
                                <p class="pb-0.5 text-base font-medium text-white">Informasi bisnis</p>
                                <dd class="pb-8 text-sm font-normal text-white">Harap berikan informasi bisnis Anda.</dd>
                            </div>
                        </li>
                        <li class="stepper-step flex gap-x-4">
                            <div class="stepper-head flex flex-col items-center" data-step='2'>
                                <div class="stepper-head-icon">
                                    <div
                                        class="w-8 h-8 rounded-full flex items-center justify-center border-2 border-gray-200 bg-gray-900">
                                        <span class="block w-2.5 h-2.5 rounded-full bg-gray-200"></span>
                                    </div>
                                </div>
                                <div class="stepper-barrier flex-1 w-0.5 rounded my-1 bg-gray-500"></div>
                            </div>
                            <div class="stepper-content pt-1">
                                <p class="pb-0.5 text-base font-medium text-white">Lokasi bisnis</p>
                                <dd class="pb-8 text-sm font-normal text-white">Harap berikan lokasi bisnis Anda.</dd>
                            </div>
                        </li>
                        <li class="stepper-step flex gap-x-4">
                            <div class="stepper-head flex flex-col items-center" data-step='3'>
                                <div class="stepper-head-icon">
                                    <div
                                        class="w-8 h-8 rounded-full flex items-center justify-center border-2 border-gray-200 bg-gray-900">
                                        <span class="block w-2.5 h-2.5 rounded-full bg-gray-200"></span>
                                    </div>
                                </div>
                                <div class="stepper-barrier flex-1 w-0.5 rounded my-1 bg-gray-500"></div>
                            </div>
                            <div class="stepper-content pt-1">
                                <p class="pb-0.5 text-base font-medium text-white">Informasi pemilik</p>
                                <dd class="pb-8 text-sm font-normal text-white">Harap berikan infomasi pemilik bisnis Anda.
                                </dd>
                            </div>
                        </li>
                        <li class="stepper-step flex gap-x-4">
                            <div class="stepper-head flex flex-col items-center" data-step='4'>
                                <div class="stepper-head-icon">
                                    <div
                                        class="w-8 h-8 rounded-full flex items-center justify-center border-2 border-gray-200 bg-gray-900">
                                        <span class="block w-2.5 h-2.5 rounded-full bg-gray-200"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="stepper-content pt-1">
                                <p class="pb-0.5 text-base font-medium text-white">Selesai</p>
                                <dd class="pb-8 text-sm font-normal text-white">Pembuatan bisnis telah selesai.</dd>
                            </div>
                        </li>
                    </ul>
                </main>
            </aside>
            <main class="flex-1 h-full flex flex-col bg-white overflow-auto">
                <div class="flex items-center gap-1 px-4 py-2 w-full justify-end">
                    <p class="text-xs mt-0.5">Already registered?</p>
                    <a href="/login">
                        <button type="button" class="text-sm px-2 hover:bg-gray-100 rounded">Sign in</button>
                    </a>
                </div>
                <div class="w-full flex-1">
                    @include('business.partials.register_form')
                </div>
            </main>
        </div>
    </div>

    <script type="application/javascript">
    window.addEventListener('DOMContentLoaded', async (event) => {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        $('input[name="start_date"]').daterangepicker({
            autoUpdateInput: false,
            locale: { format: 'YYYY-MM-DD', cancelLabel: 'Clear' },
            singleDatePicker: true,
            showDropdowns: true,
            minYear: 1945,
            drops: "auto",
            maxYear: parseInt(moment().format('YYYY'), 10)
        });

        $('input[name="start_date"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
        });

        $('input[name="start_date"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        $('#add-info').on('click', function(e) {
            $('.add-info-icon').toggleClass('rotate-180');
            $('#add-info-content').toggle('hidden');
        })
        // var res = Utils.submit('.submit-register-business', (data) => { });
    });
</script>
@endsection
