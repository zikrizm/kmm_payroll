<div class="flex flex-col gap-8 xs/max:gap-6 pt-8 pb-8 xs/max:pt-6 xs/max:pb-6">
    <header class="px-4 flex flex-col gap-5 xs/max:gap-3">
        <div class="flex flex-col gap-1">
            <p class="text-2xl font-bold text-gray-900 xs/max:text-xl xs/max:font-semibold">New user</p>
            <p class="text-base font-normal text-gray-500 xs/max:text-sm">Lorem ipsum dolor sit, amet consectetur
                adipisicing
                elit.
            </p>
        </div>
        <hr>
    </header>

    <form autocomplete="off" action="{{ route('user.store') }}" method="POST" class="submitUserM">
        @csrf
        <!-- {{ csrf_field() }} -->
        <main class="px-4 flex flex-col xs/max:gap-6 gap-12 mb-8">
            <section class="flex flex-col gap-4 xs/max:gap-3">
                <div class="flex flex-col gap-1 gap-1">
                    <p class="text-lg font-semibold text-gray-900 xs/max:text-sm">User informations</p>
                    <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                        Lorem ipsum dolor sit, amet consectetur adipisicing elit.
                    </p>
                </div>
                <hr>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Name*</label>
                    <input
                        class="py-1.5 px-2.5 text-sm xs/max:text-xs rounded-lg xs/max:rounded shadow-sm border border-gray-300 focus:outline-none focus:ring-2 focus:shadow focus:ring-gray-300 focus:border-transparent"
                        type="text" placeholder="Enter new your name" name="name" required />
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs name text-error"></label>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Email*</label>
                    <input
                        class="py-1.5 px-2.5 text-sm xs/max:text-xs rounded-lg xs/max:rounded shadow-sm border border-gray-300 focus:outline-none focus:ring-2 focus:shadow focus:ring-gray-300 focus:border-transparent"
                        type="email" placeholder="Enter new your email" name="email" required autocomplete="off" />
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs email text-error"></label>
                </div>
                {{-- <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 ">Mobile number</label>
                    <input
                        class="py-1.5 px-2.5 text-sm rounded-lg shadow-sm border border-gray-300 focus:outline-none focus:ring-2 focus:shadow focus:ring-gray-300 focus:border-transparent"
                        type="text" placeholder="Enter new your mobile number" name="phone" required />
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 ">Gender</label>
                    <select class="select2" name="gender" required>
                        <option value="">Silahkan Pilih</option>
                        <option value="male">Male</option>
                        <option value="famale">Famale</option>
                        <option value="others">Others</option>
                    </select>
                </div> --}}
            </section>
            <section class="flex flex-col gap-4 xs/max:gap-3">
                <div class="flex flex-col gap-1">
                    <p class="text-lg font-semibold text-gray-900 xs/max:text-sm">Role & Permissions</p>
                    <p class="text-sm font-normal text-gray-500 xs/max:text-xs">
                        Lorem ipsum dolor sit, amet consectetur adipisicing elit.
                    </p>
                </div>
                <hr>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Status*</label>
                    <div class="border rounded-xl xs/max:rounded-lg w-max shadow-sm flex overflow-hidden">
                        <input type="hidden" name="status" id="status-user" value="">
                        <button type="button"
                            class="text-sm xs/max:text-xs text-normal text-gray-600 px-4 py-1.5 border-r btn-status"
                            value="active">
                            Active
                        </button>
                        <button type="button"
                            class="text-sm xs/max:text-xs text-normal text-gray-600 px-4 py-1.5 btn-status"
                            value="inactive">
                            Inactive
                        </button>
                    </div>
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs status text-error"></label>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Username*</label>
                    <input
                        class="py-1.5 px-2.5 text-sm xs/max:text-xs rounded-lg xs/max:rounded shadow-sm border border-gray-300 focus:outline-none focus:ring-2 focus:shadow focus:ring-gray-300 focus:border-transparent"
                        type="text" placeholder="Enter new your username" name="username" required autocomplete="off" />
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs username text-error"></label>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm xs/max:text-xs text-gray-500 xs/max:text-xs">Password*</label>
                    <input
                        class="py-1.5 px-2.5 text-sm xs/max:text-xs rounded-lg xs/max:rounded shadow-sm border border-gray-300 focus:outline-none focus:ring-2 focus:shadow focus:ring-gray-300 focus:border-transparent"
                        type="password" placeholder="Enter new your password" name="password" required
                        autocomplete="new-password" />
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs password text-error"></label>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Role*</label>
                    <select class="select2" name="role" required>
                        <option value="" selected>Silahkan Pilih</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                    <label class="font-normal text-xs text-red-500 xs/max:text-xs role text-error"></label>
                </div>
            </section>
            {{-- <section class="flex flex-col gap-4">
                <div class="flex flex-col gap-1">
                    <p class="text-lg font-semibold text-gray-900">Bank Details</p>
                    <p class="text-sm font-normal text-gray-500">
                        Lorem ipsum dolor sit, amet consectetur adipisicing elit.
                    </p>
                </div>
                <hr>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 ">Account holder's name</label>
                    <input
                        class="py-1.5 px-2.5 text-sm rounded-lg shadow-sm border border-gray-300 focus:outline-none focus:ring-2 focus:shadow focus:ring-gray-300 focus:border-transparent"
                        type="text" placeholder="Enter new your account holder's name" name="account_name" />
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 ">Account number</label>
                    <input
                        class="py-1.5 px-2.5 text-sm rounded-lg shadow-sm border border-gray-300 focus:outline-none focus:ring-2 focus:shadow focus:ring-gray-300 focus:border-transparent"
                        type="text" placeholder="Enter new your account number" name="account_number" />
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 ">Bank name</label>
                    <input
                        class="py-1.5 px-2.5 text-sm rounded-lg shadow-sm border border-gray-300 focus:outline-none focus:ring-2 focus:shadow focus:ring-gray-300 focus:border-transparent"
                        type="text" placeholder="Enter new your bank name" name="bank_name" />
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 ">Branch</label>
                    <input
                        class="py-1.5 px-2.5 text-sm rounded-lg shadow-sm border border-gray-300 focus:outline-none focus:ring-2 focus:shadow focus:ring-gray-300 focus:border-transparent"
                        type="text" placeholder="Enter new your branch" name="branch" />
                </div>
            </section>
            <section class="flex flex-col gap-4">
                <div class="flex flex-col gap-1">
                    <p class="text-lg font-semibold text-gray-900">Payroll</p>
                    <p class="text-sm font-normal text-gray-500">
                        Lorem ipsum dolor sit, amet consectetur adipisicing elit.
                    </p>
                </div>
                <hr>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 ">Basic salary</label>
                    <input
                        class="py-1.5 px-2.5 text-sm rounded-lg shadow-sm border border-gray-300 focus:outline-none focus:ring-2 focus:shadow focus:ring-gray-300 focus:border-transparent"
                        type="text" placeholder="Enter new your basic salary" name="basic_salary" required />
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 ">Pay periodic</label>
                    <select class="select2" name="pay_periodic">
                        <option value="">Silahkan Pilih</option>
                        <option value="month">Per Month</option>
                        <option value="week">Per Week</option>
                        <option value="day">Per Day</option>
                    </select>
                </div>
            </section> --}}
        </main>
        <hr>
        <footer class="flex justify-end items-center gap-3 p-4">
            <button type="reset"
                class="modal-close shadow text-gray-500 bg-white hover:bg-gray-100 focus:ring-2 focus:ring-gray-300 rounded-lg xs/max:rounded-md border border-gray-200 text-sm xs/max:text-xs font-medium xs/max:px-4 px-6 xs/max:py-1.5 py-2 hover:text-gray-900 focus:z-10">
                Cancel</button>
            <button type="submit"
                class="text-white shadow bg-green-600 hover:bg-green-700 focus:ring-2 focus:ring-green-700 font-medium rounded-lg xs/max:rounded-md text-sm xs/max:text-xs inline-flex items-center xs/max:px-4 px-6 xs/max:py-1.5 py-2 text-center">Done</button>
        </footer>
    </form>
</div>