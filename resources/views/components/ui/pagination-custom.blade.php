@if ($pagination->count() != 0)
<footer class="flex justify-between items-center pb-4 pt-3">
    <p class="text-gray-700 text-sm">Page <span>{{ $pagination->currentPage() }}</span> of <span>{{ $pagination->lastPage()
            }}</span></p>
    <div class="flex gap-3 pagination-custom">
        @if (!$pagination->onFirstPage())
        <a href="{{ $pagination->previousPageUrl() }}">
            <button
                class="bg-white px-3.5 py-2 border border-gray-200 rounded-lg text-sm font-medium hover:bg-gray-100 text-gray-500">
                Prev</button>
        </a>
        @endif

        @if ($pagination->hasMorePages() )
        <a href="{{ $pagination->nextPageUrl() }}">
            <button
                class="bg-white px-3.5 py-2 border border-gray-200 rounded-lg text-sm font-medium hover:bg-gray-100 text-gray-500">
                Next</button>
        </a>
        @endif

    </div>
</footer>
@else
<div class="text-center flex-1 text-violet-600 w-full flex flex-col gap-2 justify-center items-center">
    <div class="rounded-full bg-violet-100 p-3 border-[4px] border-violet-50 box-border mr-2 text-violet-800">
        <x-icon icon="no-data" width=80 height=80 viewBox="20 20" />
    </div>
    <p class="font-medium">Data not found</p>
</div>
@endif