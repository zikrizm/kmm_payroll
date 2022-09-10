@extends('layouts.app')
@section('title', 'Businness settings')
@section('css')
<style></style>
@endsection
@section('content')
<div class="w-full h-full flex ">
    <aside class="w-96 h-full">

    </aside>
    <main class="flex-1 h-full flex flex-col bg-white">
        <div class="flex items-center gap-1 px-4 py-2 w-full justify-end">
            <p class="text-xs">Already registered?</p>
            <a href="/login">
                <button type="button" class="text-sm px-2 hover:bg-gray-100 rounded">Sign in</button>
            </a>
        </div>
        <div class="w-full flex-1">
            @include('business.partials.register_form')
        </div>
    </main>
</div>
{{-- <div class="pt-8 h-full flex-1 pb-12 px-8 xs/max:p-4 xs/max:pb-8 overflow-y-auto overflow-x-hidden relative">
    <main class="flex flex-col gap-8 xs/max:gap-4">
        <hgroup class="flex flex-col gap-8 xs/max:gap-4">
            <header>
                <p class="text-3xl text-gray-900 font-semibold xs/max:text-2xl">Register Business</p>
                <p class="text-base text-gray-500 font-normal xs/max:text-sm">Lorem ipsum dolor sit amet consectetur
                    adipisicing elit.
                </p>
            </header>
        </hgroup>
        @include('business.partials.register_form')
    </main>
</div> --}}

<script type="application/javascript">
    window.addEventListener('DOMContentLoaded', async (event) => {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        // var res = Utils.submit('.submit-register-business', (data) => { });
    });
</script>
<script type="application/javascript">
    // var mainModal = {name: '.main-modal', content: '.content-main-modal'};
    // var deleteModal = {name: '#confirmation-modal', content: null};

    // async function compressFotoBarang(){
    //     var a = await jic.compress(document.getElementById("file-photo"),30,"jpg")
    //     $('#foto_barang_base64_id').val(a);
    //     console.log($('#foto_barang_base64_id').val())
    // }

    // function loadPic(params) {
    //     var output = document.getElementById('file-photo');
    //     var url = URL.createObjectURL(event.target.files[0]);
    //     output.setAttribute('src',url);
    //     output.onload = function() {
    //         if(!$('#foto_barang_base64_id').val()) compressFotoBarang();
    //         // output.style.display = "block";
    //         $('#photo').removeClass('hidden');
    //         $('#button-upload').addClass('hidden');
    //         URL.revokeObjectURL(output.src) // free memory
    //     }
    // }

    // function removePhoto() {
    //     $('#photo').addClass('hidden');
    //     $('#button-upload').removeClass('hidden');
    //     $('#foto_barang_base64_id').val(null);
    //     $('#button-image').val(null)
    // }
</script>
@endsection