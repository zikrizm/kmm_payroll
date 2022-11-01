@extends('layouts.app')
@section('title', 'Businness settings')
@section('css')
    <style></style>
@endsection
@section('content')
    <div class="h-full flex-1 pb-6 px-8 xs/max:p-4 xs/max:pb-8 overflow-y-auto overflow-x-hidden relative">
        <main class="flex flex-col gap-6 h-full">
            <header class="w-full flex flex-col gap-6 justify-center pt-6">
                <div class="flex flex-col gap-1">
                    <p class="text-3xl font-medium text-gray-900">Pengaturan Perusahaan</p>
                    <p class="text-base font-normal text-gray-500">Pengaturan profil perusahaan.</p>
                </div>
                <ul class="flex border-b">
                    <li>
                        <button data-ref-class-content="business-info-content"
                            class="business-menu text-gray-500 text-violet-700 border-b-2 mr-4 pt px-1 pb-[19px] border-violet-700 text-sm font-medium">
                            Bisnis
                        </button>
                    </li>
                    <li>
                        <button data-ref-class-content="business-location-content"
                            class="business-menu text-gray-500 mr-4 pt px-1 pb-[19px] border-violet-700 text-sm font-medium">
                            Lokasi
                        </button>
                    </li>
                </ul>
            </header>
            <div id="business-info-content" class="flex-1 flex flex-col">
                <form action="{{ route('business.update.settings', ['business' => $business->id]) }}" method="POST"
                    autocomplete="off" enctype="multipart/form-data"
                    class="flex-1 flex flex-col gap-8 submit-business-setting">
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
            </div>
            <div id="business-location-content" class="hidden">
                <main class='border border-gray-200 rounded-lg shadow-sm w-max overflow-hidden'>
                    <div class="w-full overflow-auto overflow-y-hidden">
                        <table class='table border-collapse w-max'>
                            <thead class='border-b border-gray-200 bg-gray-50'>
                                <tr class=''>
                                    <th class='text-left'>
                                        <div class='flex items-center'>
                                            <div class='pl-4 py-2 flex items-center'>
                                                {!! FormCustom::checkbox() !!}
                                            </div>
                                            <div class='px-6 py-3 cursor-pointer flex-1'>
                                                <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Kota
                                                </p>
                                            </div>
                                        </div>
                                    </th>
                                    <th class='px-3 py-3 text-left cursor-pointer'>
                                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Zip kode</p>
                                    </th>
                                    <th class='px-3 py-3 text-left cursor-pointer'>
                                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Alamat lengkap
                                        </p>
                                    </th>
                                    <th class='px-3 py-3 text-left cursor-pointer'>
                                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Telphone bisnis
                                        </p>
                                    </th>
                                    <th class='px-3 py-3 text-left cursor-pointer'>
                                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Mobile
                                        </p>
                                    </th>
                                    <th class='px-3 py-3 text-left cursor-pointer'>
                                        <p class="text-xs font-medium text-gray-500 truncate cursor-pointer">Email
                                        </p>
                                    </th>
                                    {{-- @canany(['business-location.update', 'business-location.delete'])
                                        <th class='px-3 py-3 text-left text-gray-500 text-xs font-medium'></th>
                                    @endcanany --}}
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($locations as $item)
                                    <tr class='hover:bg-gray-50 border-b border-gray-200'>
                                        <td class='text-left'>
                                            <div class="flex items-center">
                                                <div class="pl-4 py ">
                                                    {!! FormCustom::checkbox() !!}
                                                </div>
                                                <div class="flex gap-3 items-center px-6 py-3 cursor-pointer">
                                                    <p class="text-violet-600 text-sm font-medium truncate">
                                                        {{ $item->city }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class='px-3 py text-gray-500 text-sm'>
                                            <p class="truncate">
                                                {{ $item->zip_code }}
                                            </p>
                                        </td>
                                        <td class='px-3 py text-gray-500 text-sm'>
                                            <p class="truncate">
                                                {{ $item->full_address }}
                                            </p>
                                        </td>
                                        <td class='px-3 py text-gray-500 text-sm'>
                                            <p class="truncate">
                                                {{ $item->mobile }}
                                            </p>
                                        </td>
                                        <td class='px-3 py text-gray-500 text-sm'>
                                            <p class="truncate">
                                                {{ $item->email }}
                                            </p>
                                        </td>
                                        {{-- <td class='px-3 py'>
                                            <div class='flex gap-1'>
                                                <button onclick="open_modal_confirm('{{ $item['id'] }}')"
                                                    class='p-2.5 cursor-pointer text-gray-500 delete-btn'>
                                                    <x-icon icon="trash-2" width=18 height=18 viewBox="20 20" />
                                                </button>
                                                <button class='p-2.5 cursor-pointer text-gray-500 edit-btn'
                                                    onclick="get_modal({{ $item['emp_code'] }})">
                                                    <x-icon icon="edit" width=18 height=18 viewBox="20 20" />
                                                </button>
                                            </div>
                                        </td> --}}
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                            {{-- <tbody>
                                @forelse (($employees['data'] ?? []) as $item)
                                    <tr class='hover:bg-gray-50 border-b border-gray-200'>
                                        <td class='text-left'>
                                            <div class="flex items-center">
                                                <div class="pl-4 py ">
                                                    {!! FormCustom::checkbox() !!}
                                                </div>
                                                <div class="flex gap-3 items-center px-6 py-3 underline decoration-violet-600 cursor-pointer"
                                                    onclick="get_modal('{{ $item['emp_code'] }}')">
                                                    <p class="text-violet-600 text-sm font-medium truncate">
                                                        {{ $item['emp_code'] }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class='text-left'>
                                            <div class="flex items-center">
                                                <div class="flex gap-3 items-center px-3 py">
                                                    @if (!empty($item['photo']))
                                                        <img src="@zkPhoto({{ $item['photo'] }})" alt=""
                                                            class="w-8 object-cover h-8 min-w-[32px] min-h-[32px] rounded-full">
                                                    @else
                                                        <img src="@zkPhoto(files / nophoto . gif)" alt=""
                                                            class="w-8 object-cover h-8 min-w-[32px] min-h-[32px] rounded-full">
                                                    @endif
                                                    <div>
                                                        <p class="text-gray-900 text-sm font-medium truncate sm/max:w-12">
                                                            {{ $item['first_name'] ?? '' }} {{ $item['last_name'] ?? '' }}
                                                        </p>
                                                        <p class="text-gray-500 text-sm font-normal truncate sm/max:w-12">
                                                            {{ $item['email'] }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class='px-3 py text-gray-500 text-sm'>
                                            <p class="truncate">
                                                {{ !empty($item['department']) ? $item['department']['dept_name'] ?? '-' : '-' }}
                                            </p>
                                        </td>
                                        <td class='px-3 py text-gray-500 text-sm'>
                                            <p class="truncate">
                                                @if (!empty($item['position']))
                                                    {{ implode(', ', array_column($item['position'], 'position_name')) }}
                                                @else
                                                    -
                                                @endif
                                            </p>
                                        </td>
                                        <td class='px-3 py text-gray-500 text-sm'>
                                            <p class="truncate">
                                                {{ date('d-m-Y', strtotime($item['hire_date'])) }}
                                            </p>
                                        </td>
                                        <td class='px-3 py'>
                                            <div class='flex gap-1'>
                                                <button onclick="open_modal_confirm('{{ $item['id'] }}')"
                                                    class='p-2.5 cursor-pointer text-gray-500 delete-btn'>
                                                    <x-icon icon="trash-2" width=18 height=18 viewBox="20 20" />
                                                </button>
                                                <button class='p-2.5 cursor-pointer text-gray-500 edit-btn'
                                                    onclick="get_modal({{ $item['emp_code'] }})">
                                                    <x-icon icon="edit" width=18 height=18 viewBox="20 20" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody> --}}
                        </table>
                    </div>
                    {{-- <footer class='flex justify-between items-center px-6 pt-3 pb-4'>
                        @php
                            $page_of = ceil($employees['count'] / (int) $page_size);
                            if ($employees['next']) {
                                $parts = parse_url($employees['next']);
                                parse_str($parts['query'], $query);
                                $page = (int) $query['page'] - 1;
                            } else {
                                $page = $page_of;
                            }
                        @endphp
                        <div class="flex items-center gap-3">
                            <select class="select2-page w-14" name="" id="">
                                <option value="10" @selected($page_size == '10')>10</option>
                                <option value="20" @selected($page_size == '20')>20</option>
                                <option value="50" @selected($page_size == '50')>50</option>
                                <option value="100" @selected($page_size == '100')>100</option>
                            </select>
                            <p class='text-gray-700 text-xs'>
                                Page <span> {{ $page }} </span> of <span>{{ $page_of }}</span>
                            </p>
                        </div>
                        <div class='flex gap-3'>
                            @if (!empty($employees['previous']))
                                <button data-pagination-url="{{ $employees['previous'] }}"
                                    class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-xs hover:bg-gray-50'>Previous</button>
                            @endif
                            @if (!empty($employees['next']))
                                <button data-pagination-url="{{ $employees['next'] }}"
                                    class='pagination-button px-3.5 py-2 border border-gray-300 rounded-lg text-xs hover:bg-gray-50'>Next</button>
                            @endif
                        </div>
                    </footer> --}}
                </main>
            </div>
        </main>
    </div>
    <script type="application/javascript">
    window.addEventListener('DOMContentLoaded', async (event) => {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
       
        var resSubmit = await ApiService.submit_form('.submit-business-setting', (data) => { 
            if (_response.response < 200 || _response.response >= 300) {
                    // * SET NOTIFICATION MESSAGE REQUIRED ----->
            } else {
                $('#remove-img').addClass('hidden');
            }
        });

        $('input[name="start_date"]').daterangepicker({
            locale: { format: 'YYYY-MM-DD' },
            singleDatePicker: true,
            showDropdowns: true,
            minYear: 1945,
            drops: "auto",
            maxYear: parseInt(moment().format('YYYY'), 10)
        });

        $('*[data-ref-class-content]').on('click', function(e) {
            let _idContent = $(this).data('ref-class-content');
            $('*[data-ref-class-content]').each(function () {
                let _idContent = $(this).data('ref-class-content');
                $(this).removeClass('border-b-2 text-violet-700');
                $('#'+_idContent).addClass('hidden');
            });

            $(this).addClass('border-b-2 text-violet-700');
            $('#'+_idContent).removeClass('hidden');
        })
    });
</script>
@endsection
