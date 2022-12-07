<section
    class="flex flex-col gap-8 pt-4 bg-white w-[375px] max-h-[95vh] overflow-y-auto overflow-x-hidden relative rounded-lg">
    <div class="flex items-center gap-5 px-4 relative">
        <div class="flex flex-col gap-4 pb-4 pt-2 w-full">
            <div>
                <div class="flex items-center justify-between gap-8 px-2 py-1">
                    <div class="flex items-center gap-1">
                        <x-icon icon="user" width=14 height=14 viewBox="20 20" />
                        <p class="text-xs text-gray-700">Nama</p>
                    </div>
                    <p class="text-xs text-gray-500 font-medium">{{ $kasbon->first_name }} {{ $kasbon->last_name??'' }}
                    </p>
                </div>
                <div class="flex items-center justify-between gap-8 px-2 py-1">
                    <div class="flex items-center gap-1">
                        <x-icon icon="calendar" width=14 height=14 viewBox="20 20" />
                        <p class="text-xs text-gray-700">Tanggal kasbon</p>
                    </div>
                    <p class="text-xs text-gray-500 font-medium">{{ date('j F Y',
                        strtotime($kasbon->date))
                        }}</p>
                </div>
                <hr class="my-2">
                <div class="flex items-center justify-between bg-gray-100 rounded px-2 py-1 mb-1">
                    <p class="text-xs text-gray-700">Kasbon</p>
                    <p class="text-xs text-gray-500 font-medium">@convert($kasbon->debt)</p>
                </div>
                <div class="flex items-center justify-between bg-gray-100 rounded px-2 py-1">
                    <p class="text-xs text-gray-700">Sisa</p>
                    @php
                    $sisa = $kasbon->debt - $kasbon->employee_debt_pays->sum('payment');
                    @endphp
                    <p class="text-xs text-red-500 font-medium">@convert($sisa)</p>
                </div>
            </div>
            <div class="flex flex-col gap-2">
                <p class="text-base text-gray-700 font-medium">Cicilan</p>
                <div class="w-full">
                    <main class='border border-gray-200 rounded-lg shadow-sm w-full overflow-hidden'>
                        <div class="w-full overflow-auto overflow-y-hidden">
                            <table class='table border-collapse w-full'>
                                <thead class='border-b border-gray-200 bg-gray-50'>
                                    <tr class=''>
                                        <th class='px-3 py-2 text-left w-6'>
                                            <p class="text-xs font-medium text-gray-500 truncate">No.
                                            </p>
                                        </th>
                                        <th class='px-3 py-2 text-left'>
                                            <p class="text-xs font-medium text-gray-500 truncate">Tanggal
                                            </p>
                                        </th>
                                        <th class='px-3 py-2 text-left text-right'>
                                            <p class="text-xs font-medium text-gray-500 truncate">Cicilan
                                            </p>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kasbon->employee_debt_pays as $key=> $item)
                                    <tr class='hover:bg-gray-50 border-b border-gray-200'>
                                        <td class='px-3 py-1.5 text-gray-500 text-xs w-6'>
                                            {{ $key+1 }}.
                                        </td>
                                        <td class='px-3 py-1.5 text-gray-500 text-xs'>
                                            <div class="flex items-center gap-1">
                                                <x-icon icon="calendar" width=14 height=14 viewBox="20 20" />
                                                <p class="truncate"> {{ date('d-m-Y',
                                                    strtotime($item->debt_payment_date))
                                                    }} </p>
                                            </div>
                                        </td>
                                        <td class='px-3 py-1.5 text-gray-500 text-xs text-right'>
                                            @convert($item->payment)
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </main>
                    @if (count($kasbon->employee_debt_pays) == 0)
                    <p class="text-xs text-center text-gray-500 py-4">Belum ada cicilan</p>
                    @endif
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 flex-1">
                <button type="reset"
                    class="modal-close shadow text-gray-500 bg-white hover:bg-gray-100 focus:ring-2 focus:ring-gray-300 rounded-lg xs/max:rounded-md border border-gray-200 text-sm xs/max:text-xs font-medium xs/max:px-4 px-6 xs/max:py-1.5 py-1 hover:text-gray-900 focus:z-10">Tutup</button>
            </div>
        </div>
    </div>
</section>