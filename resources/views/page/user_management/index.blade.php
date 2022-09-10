@extends('layouts.app')
@section('css')
    <style></style>
@endsection
@section('content')
    <div class="pt-8 h-full flex-1 pb-12 px-8 overflow-y-scroll overflow-x-hidden relative">
        {{-- <div
        class="py-12 absolute top-0 h-screen bg-white shadow-md border-l border-gray-200 max-w-[375px] overflow-y-scroll no-scrollbar duration-700"
        style="z-index: 99;" id="aside_user_management">
        <button class="absolute top-5 right-5">
            <i class="feather-16" data-feather="x"></i>
        </button>
        <div id="context_aside_user_management"></div>
    </div> --}}
        <main class="flex flex-col gap-8">
            <hgroup class="flex flex-col gap-8">
                <header>
                    <p class="text-3xl text-gray-900 font-semibold">User management</p>
                    <p class="text-base text-gray-500 font-normal">Lorem ipsum dolor sit amet consectetur adipisicing elit.
                    </p>
                </header>
                <header>
                    <ul class="flex border-b">
                        <li>
                            <button value="user"
                                class="mr-4 pt px-1 pb-[19px] text-sm font-medium text-gray-500 btn-sub-menu">User</button>
                        </li>
                        <li>
                            <button value="access_control"
                                class=" mr-4 pt px-1 pb-[19px] text-sm font-medium text-gray-500 btn-sub-menu">Access
                                control</button>
                        </li>
                    </ul>
                </header>
            </hgroup>
            <div id="content_page" class="w-full"></div>
            {{-- <section class="border rounded-xl shadow-md w-max">
            <header class="px-6 py-5 flex items-center gap-3">
                <button onclick="getCreateComponent(null)"
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
                            <p class="text-gray-500 text-sm font-normal truncate lg/max:w-12">Dashboard, User management
                            </p>
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
        </section> --}}
        </main>
    </div>

    <div class="fixed z-10 inset-0 invisible min-h-screen hidden duration-300" aria-labelledby="modal-title" role="dialog"
        aria-modal="true" id="interestModal">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-50 transition-opacity inner-modal" aria-hidden="true">
        </div>
        <div class="flex items-center justify-center p-4 h-full">
            <div
                class="flex bg-white rounded-lg text-left h-[95vh] overflow-y-scroll overflow-x-hidde transform transition-all py-4">
                <div class="sm:flex sm:items-start relative pt-8 pb-8 xs/max:pt-6 xs/max:pb-6">
                    <button class="absolute top-0 right-3 xs/max:top-[-6px] modal-close hover:bg-gray-100 text-red rounded p-2">
                        <x-icon icon="x" width=16 height=16 viewBox="20 20" strokeWidth=0 />
                    </button>
                    <div id="context_aside_user_management"></div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script type="module">
    onChangeBtnSubMenu('local_sub_menu_user', (type) => {
        $('.select2').select2();
    })
</script>
    <script>
        function onChangeBtnStatus() {
            $('.btn-status').on('click', function(e) {
                $('.btn-status').each(function(e) {
                    var hasClass = $(this).hasClass("bg-gray-100");
                    if (hasClass)
                        $(this).removeClass('bg-gray-100')
                });
                $(this).toggleClass("bg-gray-100");
                $('#status-user').val($(this).val());
            });
        }
    </script>
    <script type="application/javascript">
    function getCreateComponent(type, id) {
        var url = '/user-management/show';
        var data = {type};
        if(id != null) data.id = id;

        networkUtils({
            type:'GET',
            url:url,
            data: data,
        }).then(
            function fulfillHandler(data) {
                $('#context_aside_user_management').html(data);
                $('.select2').select2();
                stroreUserManagement(type)
                onChangeBtnStatus()
                openModal('#context_aside_user_management')
            },
            function rejectHandler(jqXHR, textStatus, errorThrown) {
            }
        ).catch(function errorHandler(error) {
            console.log("error", error)
        })
    }

    function stroreUserManagement(type) {
        var url = $('#submit_user_management').attr('action');
        $("#submit_user_management").submit(function(e) {
            e.preventDefault();
          
            networkUtils({
                type:'POST',
                url:url,
                data: new FormData(this),
                processData: false,
                contentType: false,
            }).then(
                function fulfillHandler(data) {
            console.log("url", url)
            console.log("ress",data)
                },
                function rejectHandler(jqXHR, textStatus, errorThrown) {
                }
            ).catch(function errorHandler(error) {
                console.log("error", error)
            })
        });
    }
</script>
@endsection
