<form autocomplete="off" action="{{ route('payroll-report.store') }}" method="POST" class="submit-calculation-payroll">
    @csrf
    <!-- {{ csrf_field() }} -->
    <input type="hidden" name="start_date" value="{{ $start_date->format('d-m-Y') }}">
    <input type="hidden" name="end_date" value="{{ $end_date->format('d-m-Y') }}">
    @if (is_array($department_codes ?? null))
        @foreach ($department_codes as $dept_code)
            <input type="hidden" name="department_codes[]" value="{{ $dept_code }}">
        @endforeach
    @elseif (!empty($department_codes))
        <input type="hidden" name="department_code" value="{{ $department_codes }}">
    @endif
    <section id="container-modal-calculate"
        class="flex flex-col gap-8 py-4 w-[440px] bg-white border max-h-[95vh] overflow-y-auto overflow-x-hidden relative rounded-lg duration-300">
        <div class="flex items-center gap-5 px-4 relative">
            <button id="x-icon-close"
                class="absolute top-[-5px] right-3 xs/max:top-[-6px] modal-close hover:border-gray-500  border border-transparent text-gray-500 rounded p-0.5">
                <x-icon icon="x" width=16 height=16 viewBox="20 20" />
            </button>
            <div id="content-loading-calculate" class="hidden w-full">
                @include('report.payroll_report.partials.stepper_loading_calculation', ['departments'=>
                $departments, 'dates' => $dates])
            </div>
            <div id="content-finish-calculate" class="hidden w-full">
                @include('report.payroll_report.partials.finish_calculation')
            </div>
            <div id="content-confirm-calculate">
                @include('report.payroll_report.partials.confirm_calculation')
            </div>
        </div>
    </section>
</form>