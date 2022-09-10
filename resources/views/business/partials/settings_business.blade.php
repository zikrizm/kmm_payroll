<div class="flex flex-col gap-3">
    <div class="flex flex-col gap-1">
        <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Business name</label>
        <input
            class="py-1.5 px-2.5 text-sm xs/max:text-xs rounded-lg xs/max:rounded shadow-sm border border-gray-300 focus:outline-none focus:ring-2 focus:shadow focus:ring-gray-300 focus:border-transparent"
            type="text" placeholder="Enter new your bnusiness name" name="name" />
        <label class="font-normal text-xs text-red-500 xs/max:text-xs"></label>
    </div>
    <div class="flex flex-col gap-1">
        <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Start date</label>
        <input type="text" name="start_date" value="10/24/1984"
            class="py-1.5 px-2.5 text-sm xs/max:text-xs rounded-lg xs/max:rounded shadow-sm border border-gray-300 focus:outline-none focus:ring-2 focus:shadow focus:ring-gray-300 focus:border-transparent" />
        <label class="font-normal text-xs text-red-500 xs/max:text-xs"></label>
    </div>
    <div class="flex flex-col gap-1">
        <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Upload logo</label>
        <input
            class="block w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 cursor-pointer dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
            id="file_input" type="file" name="business_logo">
        <label class="font-normal text-xs text-red-500 xs/max:text-xs"></label>
    </div>
    <button class="border bg-green-700 text-white m-3 px-3 rounded-lg">done</button>
</div>