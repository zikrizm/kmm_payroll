@extends('layouts.app')
@section('css')
<style></style>
@endsection
@section('content')
<div class="pt-8 h-full flex-1 pb-12 px-8 xs/max:p-4 xs/max:pb-8 overflow-y-auto overflow-x-hidden relative">
    {{-- <div
        class="py-12 absolute top-0 h-screen bg-white shadow-md border-l border-gray-200 max-w-[375px] overflow-y-auto no-autobar duration-700"
        style="z-index: 99;" id="aside_user_management">
        <button class="absolute top-5 right-5">
            <i class="feather-16" data-feather="x"></i>
        </button>
        <div id="contentMainModal"></div>
    </div> --}}
    <main class="flex flex-col gap-8 xs/max:gap-4">
        <hgroup class="flex flex-col gap-8 xs/max:gap-4">
            <header>
                <p class="text-3xl text-gray-900 font-semibold xs/max:text-2xl">User management</p>
                <p class="text-base text-gray-500 font-normal xs/max:text-sm">Lorem ipsum dolor sit amet consectetur
                    adipisicing elit.
                </p>
            </header>
            <header>
                <ul class="flex border-b">
                    <li>
                        <button value="user"
                            class="xs/max:pb-2.5 mr-4 pt px-1 pb-[19px] text-sm font-medium text-gray-500 btn-sub-menu default">User</button>
                    </li>
                    <li>
                        <button value="access-control"
                            class="xs/max:pb-2.5 mr-4 pt px-1 pb-[19px] text-sm font-medium text-gray-500 btn-sub-menu">Access
                            control</button>
                    </li>
                </ul>
            </header>
        </hgroup>
        <div id="content_page" class="w-full">
            {{-- @include('page.user.index', ['users' => $users]) --}}
        </div>
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

<div class="fixed z-10 inset-0  min-h-screen duration-300 invisible hidden" aria-labelledby="modal-title" role="dialog"
    aria-modal="true" id="mainModal">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-50 transition-opacity inner-modal" aria-hidden="true">
    </div>
    <div class="flex items-center justify-center px-4 h-full">
        <div
            class="flex bg-white rounded-lg text-left max-h-[95vh] overflow-y-auto overflow-x-hidde transform transition-all py-4">
            <div class="sm:flex sm:items-start relative ">
                <button
                    class="absolute top-0 right-3 xs/max:top-[-6px] modal-close hover:bg-gray-100 text-red rounded p-2">
                    <x-icon icon="x" width=16 height=16 viewBox="20 20" />
                </button>
                <div id="contentMainModal"></div>
            </div>
        </div>
    </div>
</div>
</div>
<x-modal-confirmation classSubmit='submitDelete'></x-modal-confirmation>
<script type="application/javascript">
    var optionObject = {
        subMenuStorageName: 'userMSubMenu',
        classSubmitData: '.submitUserM',
        classSubmitDelete: 'submitDelete',
        idBoxContent: '#content_page',
        subMenus: { 
            user: {name: '/user/', data: null}, 
            access_control: {name: '/access-control/', data: null},
        },
        modals: {
            mainModal: {name: '#mainModal', content: '#contentMainModal'},
            deleteModal: {name: '#deleteModal', content: null},
        } 
    }
</script>
<script type="module">
    $('.select2').select2();

    handlerSwitchSubMenu(optionObject, () => {
        $('.select2').select2();
    })
</script>
<script>
    function onChangeBtnStatus(defautlValue) {
        if(defautlValue) {
            $('.btn-status').each(function(e) {
                var value = $(this).val();
                if (value.toLowerCase() == defautlValue.toLowerCase()){
                    $(this).toggleClass("bg-gray-100");
                    $('#status-user').val($(this).val());
                }
            });
        }

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
    function getModal(id) {
        var typeUrl    = (id) ? 'edit': 'create';
        var typeMethod = (id) ? 'PUT': 'POST';
        var url        = getUrl(typeUrl, optionObject?.subMenuStorageName, id);
        var typeLocal  = localStorage.getItem(optionObject?.subMenuStorageName);
        
        if(url) {
            $.ajax({
                type: 'GET', url,
                success: function (data) {
                    $(optionObject?.modals?.mainModal?.content).html(data);
                    openModal(optionObject?.modals.mainModal);
                    $('.select2').select2();
                    switch (typeLocal) {
                        case 'user':
                            onChangeBtnStatus('active');
                            break;
                        default:
                            break;
                    }

                    //** Listerner submit data
                    /* 
                    */ submitData(typeMethod);

                }, statusCode: {
                    403: function () {
                        alert('Anda Tidak Berhak Mengakses Menu ini');
                    }
                }
            });
        }
    }

    function submitData(typeM) {
        var url = $(optionObject.classSubmitData).attr('action');
        $(optionObject.classSubmitData).on("submit", function(e) {
            e.preventDefault();
            clearError();
            $.ajax({
                type:typeM,
                url:url,
                data: $(this).serialize(),
                processData: true,
                success:function(data) {
                    console.log("data", data);
                    if(data.error) {
                        handleError(data.error);
                    } else {
                        closeModal(optionObject.modals.mainModal);
                        // getPage(type, () => {
                        //     $('.select2').select2();
                        // })
                    }
                }, statusCode: {
                    403: function() { 
                        alert('Anda Tidak Berhak Mengakses Menu ini');
                    }
                }
            });
        })
    }

    function getModalDelete(id) {
        openModal(optionObject.modals.deleteModal);
        var url = getUrl('delete', optionObject?.subMenuStorageName, id);
        $(".submitDelete").submit(function(e) {
            e.preventDefault();
            $.ajax({
                type:'DELETE',
                url: url,
                success:function(data) {
                    closeModal(optionObject.modals.deleteModal);
                    // modalClose(deleteM);
                    // handler($('.datepicker').val(), data.msg);
                }, statusCode: {
                    403: function() { 
                        alert('Anda Tidak Berhak Mengakses Menu ini');
                    }
                }
            });
            
        });
    }

    
    // function submitData(option, ) {
    //     // var url = $('.form_pendapatan_lain').attr('action');
    //     $(".form_pendapatan_lain").on("submit", function(e) {
    //         e.preventDefault();
    //         $.ajax({
    //             type:'POST',
    //             url:url,
    //             data: new FormData(this),
    //             processData: false,
    //             contentType: false,
    //             success:function(data) {
                    
    //             }, statusCode: {
    //                 403: function() { 
    //                     alert('Anda Tidak Berhak Mengakses Menu ini');
    //                 }
    //             }
    //         });
    //     })
    // }


    // function getModalComponent(type, typeAction, id) {
    //     var url = '';
    //     var data = {type};

    //     switch (typeAction) {
    //         case 'create':
    //             url = '/user-management/create';
    //             break;
    //         case 'update':
    //             data.id = id;
    //             url = '/user-management/edit';
    //             break;
    //         case 'delete':
    //             data.id = id;
    //             url = '/user-management/delete';
    //             break;
    //         default:
    //             break;
    //     }
     

    //     networkUtils({
    //         type:'GET',
    //         url:url,
    //         data: data,
    //     }).then(
    //         function fulfillHandler(data) {
    //             $('#contentMainModal').html(data);
    //             $('.select2').select2();

    //             openModal('#contentMainModal');
    //             stroreUserManagement(type);

    //             switch (type) {
    //                 case 'user':
    //                     onChangeBtnStatus();
    //                     break;
    //                 default:
    //                     break;
    //             }
    //         },
    //         function rejectHandler(jqXHR, textStatus, errorThrown) {

    //         }
    //     ).catch(function errorHandler(error) {
    //         console.log("error", error)
    //     })
    // }

    // function stroreUserManagement(type) {
    //     var url = $('#submit_user_management').attr('action');
    //     $("#submit_user_management").submit(function(e) {
    //         e.preventDefault();
    //         clearError();
    //         networkUtils({
    //             type:'POST',
    //             url:url,
    //             data: new FormData(this),
    //             processData: false,
    //             contentType: false,
    //         }).then(
    //             function fulfillHandler(data) {
    //                 console.log("data", data);
    //                 if(data.error) {
    //                     handleError(data.error);
    //                 } else {
    //                     closeModal('#contentMainModal');
    //                     getPage(type, () => {
    //                         $('.select2').select2();
    //                     })
    //                 }
    //             },
    //             function rejectHandler(jqXHR, textStatus, errorThrown) {
    //             }
    //         ).catch(function errorHandler(error) {
    //             console.log("error", error)
    //         })
    //     });
    // }
</script>
@endsection