@extends('layouts.app')
@section('title', 'Businness settings')
@section('css')
<style></style>
@endsection
@section('content')
<div class="pt-8 h-full flex-1 pb-12 px-8 xs/max:p-4 xs/max:pb-8 overflow-y-auto overflow-x-hidden relative">
    <main class="flex flex-col gap-8 xs/max:gap-4">
        <hgroup class="flex flex-col gap-8 xs/max:gap-4">
            <header>
                <p class="text-3xl text-gray-900 font-semibold xs/max:text-2xl">Business settings</p>
                <p class="text-base text-gray-500 font-normal xs/max:text-sm">Lorem ipsum dolor sit amet consectetur
                    adipisicing elit.
                </p>
            </header>
            <header class="flex border-b">
                <button value="business"
                    class="xs/max:pb-2.5 mr-4 pt px-1 pb-[19px] text-sm font-medium text-gray-500 btn-sub-menu default">
                    Business</button>
            </header>
        </hgroup>
        <form action="{{ route('business.update.settings') }}" method="POST" enctype="multipart/form-data"
            class="submit-update-settings-business">
            @include('business.partials.settings_business')
        </form>
    </main>
</div>
<script type="application/javascript">
    window.addEventListener('DOMContentLoaded', async (event) => {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        var res = Utils.submit('.submit-update-settings-business', (data) => { 
            console.log('data',data)
        });
    });
</script>
<script type="application/javascript">
    $(function() {
        $('input[name="start_date"]').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            minYear: 1901,
            maxYear: parseInt(moment().format('YYYY'),10)
        }, function(start, end, label) {
            var years = moment().diff(start, 'years');
            alert("You are " + years + " years old!");
        });
    });
</script>
<script type="application/javascript">
</script>
@endsection