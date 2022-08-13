@extends('layouts.app')
@section('css')
<style></style>
@endsection
@section('content')
<main class="flex flex-col gap-8">
    <hgroup class="flex flex-col gap-8">
        <header>
            <p class="text-3xl text-gray-900 font-semibold">User management</p>
            <p class="text-base text-gray-500 font-normal">Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
        </header>
        <header>
            <ul class="flex border-b">
                <li>
                    <button
                        class="text-grey-500 mr-4 pt px-1 pb-[19px] text-green-500 text-sm font-medium">Users</button>
                </li>
                <li>
                    <button
                        class="text-green-700 border-b-2 mr-4 pt px-1 pb-[19px] border-green-700 text-sm font-medium">Access
                        control</button>
                </li>
            </ul>
        </header>
    </hgroup>
    <section class="border rounded-xl shadow-md w-max">
        <header class="px-6 py-5 flex items-center gap-3">
            <button
                class="flex gap-2 shadow-xs rounded-lg py-2 px-3.5 text-white text-sm font-medium flex items-center bg-green-600">
                <i class="feather-16" data-feather="plus"></i>
                New user</button>
        </header>
        <hr>
        <section class="px-6 py-5 flex items-center gap-3">
            <select class="select2" name="Status">
                <option value="Kerja">
                    <i class="feather-16" data-feather="edit-2"></i>
                    All Role
                </option>
                <option value="Berhenti">Berhenti</option>
            </select>
        </section>
        <table class="table border-collapse w-full">
            <thead class="border-y border-gray-200 bg-gray-50">
                <tr class="text-left">
                    <th class="py-3 px-6 text-xs font-medium text-gray-500">Name & Email</th>
                    <th class="py-3 px-6 text-xs font-medium text-gray-500">Mobile number</th>
                    <th class="py-3 px-6 text-xs font-medium text-gray-500">Role</th>
                    <th class="py-3 px-6 text-xs font-medium text-gray-500">Status</th>
                    <th class="py-3 px-6"></th>
                </tr>
            </thead>

            <tbody class="text-sm font-normal text-gray-700">
                <tr class="border-b border-grey/200 cursor-pointer hover:bg-grey/50  ">
                    <td class="px-6 py-4 text-left">
                        <div class="flex gap-3 items-center">
                            <button type="button">
                                <img src="https://pixlr.com/studio/template/6264364c-b8cc-4f4f-92d8-28c69a2b756w/thumbnail.webp"
                                    alt="" class="w-10 object-contain h-10 min-w-[40px] min-h-[40px]">
                            </button>
                            <div>
                                <p class="text-gray-900 text-sm font-medium truncate xl/max:w-12 lg/max:w-8">Zikri
                                    Marifatullah</p>
                                <p class="text-gray-500 text-sm font-normal truncate xl/max:w-12 lg/max:w-8">
                                    zikrizm@gmail.com</p>
                            </div>
                        </div>

                    </td>
                    <td class="px-6 py-3 text-left">
                        <p class="text-gray-500 text-sm font-normal truncate lg/max:w-12">081578925512</p>
                    </td>
                    <td class="px-6 py-3 text-left">
                        <p class="text-gray-500 text-sm font-normal truncate lg/max:w-12">Dashboard, User management</p>
                    </td>
                    <td class="px-6 py-3 text-left ">
                        <div class="rounded-xl bg-green-50 pl-2 pr-1.5 py-0.5 w-max">
                            <p class="text-green-700 text-xs font-normal flex items-center gap-1"> Active</p>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex gap-1">
                            <button class="p-2.5 cursor-pointer text-grey/500 delete-btn">
                                <i class="feather-16" data-feather="trash-2"></i>
                            </button>
                            <button class="p-2.5 cursor-pointer text-grey/500">
                                <i class="feather-16" data-feather="edit-2"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <footer class="flex justify-between items-center px-6 pt-3 pb-4">
            <p class="text-gray-700 text-sm">Page <span>1</span> of <span>7</span></p>
            <div class="flex gap-3"><button
                    class="px-3.5 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 text-gray-700">Next</button>
            </div>
        </footer>
    </section>
</main>
@endsection
@section('javascript')
<script type="module">
    $('.select2').select2();
        // $('#select2-dropdown').on('change', function (e) {
        //     // var data = $('#select2-dropdown').select2("val");
        //     // @this.set('ottPlatform', data);
        // });
</script>
@endsection