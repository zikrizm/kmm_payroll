@extends('layouts.app')
@section('title', 'Businness settings')
@section('css')
<style></style>
@endsection
@section('content')
<div class="h-full flex-1 pb-12 px-8 xs/max:p-4 xs/max:pb-8 overflow-y-auto overflow-x-hidden relative">
    <main class="flex flex-col gap-8 h-full">
        <header class="w-full flex flex-col gap-6 justify-center pt-6">
            <div>
                <p class="font-semibold text-3xl text-gray-700 xs/max:text-xl">Business settings</p>
                <p class="font-normal text-sm text-gray-500">Update your logo and business details here.</p>
            </div>
            <ul class="flex border-b">
                <li>
                    <button data-ref-class-content="business-info-content"
                        class="business-menu text-violet-700 border-b-2 mr-4 pt px-1 pb-[19px] border-violet-700 text-sm font-medium">
                        Business
                    </button>
                </li>
                <li>
                    <button data-ref-class-content="business-dash-content"
                        class="business-menu text-gray-500 mr-4 pt px-1 pb-[19px] border-violet-700 text-sm font-medium">
                        Dashboard
                    </button>
                </li>
            </ul>
        </header>
        <form action="{{ route('business.update.settings', ['business' => $business->id]) }}" method="POST"
            autocomplete="off" enctype="multipart/form-data" class="flex-1 flex flex-col gap-8 submit-business-setting">
            @csrf
            <!-- {{ csrf_field() }} -->
            <div class="flex-1">
                @include('business.partials.settings_business')
            </div>
            <footer class="flex justify-end items-center gap-3 border-t pt-3">
                <button type="submit"
                    class="text-white shadow bg-violet-600 hover:bg-violet-700 focus:ring-2 focus:ring-violet-700 font-medium rounded-lg xs/max:rounded-md text-sm xs/max:text-xs inline-flex items-center xs/max:px-4 px-6 xs/max:py-1.5 py-2 text-center">Done</button>
            </footer>
        </form>
    </main>
</div>
<script type="application/javascript">
    window.addEventListener('DOMContentLoaded', async (event) => {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
       
        var resSubmit = Utils.submit('.submit-business-setting', (data) => { 
            $('#remove-img').addClass('hidden');
        });

        $(function() {
            $('input[name="start_date"]').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                minYear: 1901,
                maxYear: parseInt(moment().format('YYYY'), 10),
                drops: "auto",
            });
        })

        $(function() {
            $('.business-menu').on('click', function() {
                $('.business-menu').each(function (e) {
                    var classContent = $(this).attr("data-ref-class-content");
                    $(this).removeClass('text-violet-700 border-b-2');
                    $(this).addClass('text-gray-500');
                    $(`.${classContent}`).hide();
                });
                var classContent = $(this).attr("data-ref-class-content");
                $(this).addClass('text-violet-700 border-b-2');
                $(`.${classContent}`).show();
            });
        })
    });
</script>
@endsection