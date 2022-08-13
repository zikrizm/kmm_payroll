<div class="flex flex-col gap-8">
    <button class="absolute top-5 right-5">
        <i class="feather-16" data-feather="x"></i>
    </button>
    <header class="px-4 flex flex-col gap-5">
        <div class="flex flex-col gap-1">
            <p class="text-2xl font-bold text-gray-900">New user</p>
            <p class="text-base font-normal text-gray-500">Lorem ipsum dolor sit, amet consectetur adipisicing
                elit.
            </p>
        </div>
        <hr>
    </header>
    <form autocomplete="off" action="{{route('userManagement.store')}}" method="POST" id="submit_user_management">
        @csrf
        <!-- {{ csrf_field() }} -->
        <main class="px-4 flex flex-col gap-12 mb-8">
            <section class="flex flex-col gap-4">
                <div class="flex flex-col gap-1">
                    <p class="text-lg font-semibold text-gray-900">User informations</p>
                    <p class="text-sm font-normal text-gray-500">
                        Lorem ipsum dolor sit, amet consectetur adipisicing elit.
                    </p>
                </div>
                <hr>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 ">Name</label>
                    <input
                        class="py-1.5 px-2.5 text-sm rounded-lg shadow-sm border border-gray-300 focus:outline-none focus:ring-2 focus:shadow focus:ring-gray-300 focus:border-transparent"
                        type="text" placeholder="Enter new your name" name="name" required />
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 ">Email</label>
                    <input
                        class="py-1.5 px-2.5 text-sm rounded-lg shadow-sm border border-gray-300 focus:outline-none focus:ring-2 focus:shadow focus:ring-gray-300 focus:border-transparent"
                        type="email" placeholder="Enter new your email" name="email" required autocomplete="off"/>
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
            <section class="flex flex-col gap-4">
                <div class="flex flex-col gap-1">
                    <p class="text-lg font-semibold text-gray-900">Role & Permissions</p>
                    <p class="text-sm font-normal text-gray-500">
                        Lorem ipsum dolor sit, amet consectetur adipisicing elit.
                    </p>
                </div>
                <hr>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 ">Status</label>
                    <div class="border rounded-xl w-max shadow-sm flex overflow-hidden">
                        <input type="hidden" name="status" id="status-user" value="">
                        <button type="button" class="text-sm text-normal text-gray-600 px-4 py-1.5 border-r btn-status" value="active">
                            Active
                        </button>
                        <button type="button" class="text-sm text-normal text-gray-600 px-4 py-1.5 btn-status" value="inactive">
                            Inactive
                        </button>
                    </div>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 ">Username</label>
                    <input
                        class="py-1.5 px-2.5 text-sm rounded-lg shadow-sm border border-gray-300 focus:outline-none focus:ring-2 focus:shadow focus:ring-gray-300 focus:border-transparent"
                        type="text" placeholder="Enter new your username" name="username" required autocomplete="off"/>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 ">Password</label>
                    <input
                        class="py-1.5 px-2.5 text-sm rounded-lg shadow-sm border border-gray-300 focus:outline-none focus:ring-2 focus:shadow focus:ring-gray-300 focus:border-transparent"
                        type="password" placeholder="Enter new your password" name="password" required autocomplete="new-password"/>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 ">Role</label>
                    <select class="select2" name="role">
                        <option value="">Silahkan Pilih</option>
                        <option value=""></option>
                        <option value=""></option>
                    </select>
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
                class="shadow-md modal-close text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:ring-gray-300 rounded-lg border border-gray-200 text-sm font-medium px-6 py-2 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600">No,
                Cancel</button>
            <button type="submit"
                class="text-white shadow-md bg-green-600 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm inline-flex items-center px-6 py-2 text-center">Done</button>
        </footer>
    </form>
</div>