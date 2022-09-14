<form action="{{ route('business.store.register') }}" autocomplete="off" method="POST" enctype="multipart/form-data"
    class="submit-register-business h-full">
    @csrf
    <!-- {{ csrf_field() }} -->
    <main class="p-8 w-full h-full flex flex-col justify-center items-center gap-6">
        <section class="register-content register-step-1 flex flex-col gap-6 items-center w-full max-w-[360px]">
            <div class="flex flex-col gap-2 w-full">
                <span
                    class="h-10 w-10 rounded-full bg-violet-100 flex justify-center items-center text-violet-600 border-[8px] border-violet-50 box-content">
                    <x-icon icon="book" width=20 height=20 viewBox="20 20" />
                </span>
                <div>
                    <p class="text-3xl font-semibold">Business infomation*</p>
                    <p class="text-sm font-normal text-gray-500">Please provide your business infomation.</p>
                </div>
            </div>
            <div class="flex flex-col gap-2 w-full">
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Business name*</label>
                    {!! FormCustom::input('name', null, [ "placeholder" => 'Enter new your business name']) !!}
                </div>
                <div>
                    <p class='text-gray-700 text-sm font-medium'>Your logo</p>
                    <p class='text-gray-500 text-sm font-normal'>This will be displayed on your profile.</p>
                </div>
                <div class='flex justify-between items-start'>
                    <div class="relative">
                        <button type="button" id="remove-img"
                            class="hidden absolute right-0 bg-red-500 rounded-full text-white p-0.5"
                            onclick="removePhoto('#business_logo', '#photo_preview','#contained-button-file', this)">
                            <x-icon icon="x" width=12 height=12 viewBox="20 20" />
                        </button>
                        <img src='' class='object-contain h-16 w-16 bg-gray-50 rounded-full overflow-hidden'
                            id="photo_preview">
                    </div>
                    <label for="contained-button-file" class="flex items-center cursor-pointer">
                        <input name="business_logo" accept="image/*" id="contained-button-file" class="hidden"
                            type="file" onchange="loadPic('#business_logo', 'photo_preview', '#remove-img')" />
                        <span class='cursor-pointer text-sm font-medium text-violet-700 hover:bg-gray-100 rounded p-1'>
                            Upload
                        </span>
                    </label>
                    <input type="hidden" name="business_logo" id="business_logo">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Start date</label>
                    {!! FormCustom::input('start_date', null, [
                        'placeholder' => 'Enter new your start date',
                        'readonly' => true,
                        'prefixiconname' => 'calendar',
                    ]) !!}
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">website</label>
                    {!! FormCustom::input('website', null, [ "placeholder" => 'Enter new your website']) !!}
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">bussines contact number</label>
                    {!! FormCustom::input('mobile', null, [ "placeholder" => 'Enter new your bussines contact number'])
                    !!}
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Alternate contact number</label>
                    {!! FormCustom::input('alternate_number', null, 
                    [ "placeholder" => 'Enter new your alternate contact number'])
                    !!}
                </div>
            </div>
        </section>
        <section class="register-content register-step-2 flex flex-col gap-6 items-center w-full max-w-[360px]"
            style="display: none;">
            <div class="flex flex-col gap-6 w-full">
                <button type="button" class="flex items-center gap-2 text-violet-600 back-button">
                    <x-icon icon="arrow-left" width=20 height=20 viewBox="20 20" />
                    <p class="text-sm">Back</p>
                </button>
                <div>
                    <p class="text-3xl font-semibold">Businness location*</p>
                    <p class="text-sm font-normal text-gray-500">Please provide your business location.</p>
                </div>
            </div>
            <div class="flex flex-col gap-2 w-full">
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">country</label>
                    {!! FormCustom::input('country', null, [ "placeholder" => 'Enter new your country'])
                    !!}
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">state</label>
                    {!! FormCustom::input('state', null, [ "placeholder" => 'Enter new your state'])
                    !!}
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">city</label>
                    {!! FormCustom::input('city', null, [ "placeholder" => 'Enter new your city'])
                    !!}
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">zip code</label>
                    {!! FormCustom::input('zip_code', null, [ "placeholder" => 'Enter new your zip code'])
                    !!}
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">full address</label>
                    {!! FormCustom::textarea('full_address', null, [ "placeholder" => 'Enter new your address'])
                    !!}
                </div>
            </div>
        </section>
        <section class="register-content register-step-3 flex flex-col gap-6 items-center w-full max-w-[360px]"
            style="display: none;">
            <div class="flex flex-col gap-6 w-full">
                <button type="button" class="flex items-center gap-2 text-violet-600 back-button">
                    <x-icon icon="arrow-left" width=20 height=20 viewBox="20 20" />
                    <p class="text-sm">Back</p>
                </button>
                <div>
                    <p class="text-3xl font-semibold">Owner information*</p>
                    <p class="text-sm font-normal text-gray-500">Please provide your owner information.</p>
                </div>
            </div>
            <div class="flex flex-col gap-2 w-full">
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">prefix</label>
                    {!! FormCustom::input('surname', null, [ "placeholder" => 'Enter new your prefix'])
                    !!}
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">first name</label>
                    {!! FormCustom::input('first_name', null, [ "placeholder" => 'Enter new your first_name'])
                    !!}
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">last name</label>
                    {!! FormCustom::input('last_name', null, [ "placeholder" => 'Enter new your last_name'])
                    !!}
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">username</label>
                    {!! FormCustom::input('username', null, [ "placeholder" => 'Enter new your username'])
                    !!}
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">email</label>
                    {!! FormCustom::input('email', null, [ "placeholder" => 'Enter new your email','type' =>
                    'email'])
                    !!}
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">password</label>
                    {!! FormCustom::input('password', null, [ "placeholder" => 'Enter new your password','type' =>
                    'password'])
                    !!}
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">password confirm</label>
                    {!! FormCustom::input('password_confirm', null,
                    [ "placeholder" => 'Enter new your password confirm','type' => 'password']) !!}
                </div>
            </div>
        </section>
        <footer class="flex flex-col gap-6 items-center w-full max-w-[360px]">
            <button type="button" id="next-button"
                class="flex items-center justify-center gap-2 shadow-xs rounded-lg h-9 px-3 text-white font-normal flex items-center bg-violet-600 xs/max:text-xs xs/max:rounded w-full">
                Next
            </button>
        </footer>
    </main>
</form>
<script>
    window.addEventListener('DOMContentLoaded', async (event) => {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        $(function() {
            $('input[name="start_date"]').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                minYear: 1901,
                maxYear: parseInt(moment().format('YYYY'), 10)
            });
        })
        $(function() {
            let counterStepper = 1;
            $('#next-button').on('click', async function() {
                let configV = [];
                switch (counterStepper) {
                    case 1:
                        configV = ['name']
                        break;
                    case 2:
                        configV = ['country', 'state', 'city', 'zip_code', 'full_address']
                        break;
                    case 3:
                        configV = ['first_name', 'username', 'email', 'password', 'password_confirm']
                        break;
                        configV = [];
                    default:
                        break;
                }
                var isValid = validateForm(configV);
                if(isValid) {
                    if(counterStepper == 3) {
                        var isSame = checkIsSamePassword('password','password_confirm');
                        if(isSame.valid) {
                            setErorrsformInputs({});
                            $(this).attr('type', 'submit');
                            Utils.submit('.submit-register-business', (data) => { 
                                $('.submit-register-business').trigger("reset");
                                window.location.href = '/login';
                            });
                        }
                        else setErorrsformInputs(isSame.msg)
                    } else {
                        $('.register-content').each(function (e) {
                            $(this).hide();
                        });
                        counterStepper++;
                        $('.register-step-'+ counterStepper).show()
                    }
                }
            })
            $('.back-button').on('click', function() {
                $(this).attr("type", "button");
                $('.register-content').each(function (e) {
                    $(this).hide();
                });
                counterStepper--;
                $('.register-step-'+ counterStepper).show()
            })
        });
    });

    function validateForm(config) {
        var msg = {}, isValid = true;
        if(config.length != 0) { 
            config.forEach(element => {
                var nodeNameInput = '';
                if(element == 'full_address') {
                    nodeNameInput = 'textarea[name='+element+']';
                } else {
                    nodeNameInput = 'input[name='+element+']';
                }
                if(!$(nodeNameInput).val()) {
                    isValid = false;
                    msg[element] = ['The '+element+' field is required'];
                }
            });
            setErorrsformInputs(msg)
        }
        return isValid;
       
        // $(".submit-register-business").submit(function(e) {
        //     e.preventDefault();
        // }).validate({
        //     ignore: [],
        //     onfocusout: false,
        //     onkeyup: false,
        //     rules: config.rules,
        //     messages: config.messages,
        //     errorPlacement: function(error, element) {
        //         cb(false);
        //         var htmlFor = element.attr("name");
        //         if(htmlFor == "name")
        //         {
        //             error.appendTo($(element).parent().parent().children('.name'))
        //         }
        //         if(htmlFor == "country")
        //         {
        //             error.appendTo($(element).parent().parent().children('.country'))
        //         }
        //         if(htmlFor == "state")
        //         {
        //             error.appendTo($(element).parent().parent().children('.state'))
        //         }
        //         if(htmlFor == "city")
        //         {
        //             error.appendTo($(element).parent().parent().children('.city'))
        //         }
        //         if(htmlFor == "zip_code")
        //         {
        //             error.appendTo($(element).parent().parent().children('.zip_code'))
        //         }
        //         if(htmlFor == "full_address")
        //         {
        //             error.appendTo($(element).parent().parent().children('.full_address'))
        //         }
        //         if(htmlFor == "first_name")
        //         {
        //             error.appendTo($(element).parent().parent().children('.first_name'))
        //         }
        //         if(htmlFor == "username")
        //         {
        //             error.appendTo($(element).parent().parent().children('.username'))
        //         }
        //         if(htmlFor == "email")
        //         {
        //             error.appendTo($(element).parent().parent().children('.email'))
        //         }
        //         if(htmlFor == "password")
        //         {
        //             error.appendTo($(element).parent().parent().children('.password'))
        //         }
        //     },
        //     success: function (form) {
        //         console.log("dfsdfsdf")
        //         cb(true)
        //     },
        // });
    }

    // function validationStep1() {
    //     return {
    //         rules: { name: "required" },
    //         messages: { name: "The name field is required"},
    //     }
    // }
    // function validationStep2() {
    //     return {
    //         rules: {
    //             country: "required",
    //             state: "required",
    //             city: "required",
    //             zip_code: "required",
    //             full_address: "required",
    //         },
    //         messages: { 
    //             country: "The country field is required",
    //             state: "The state field is required",
    //             city: "The city field is required",
    //             zip_code: "The zip_code field is required",
    //             full_address: "The full_address field is required",
    //         },
    //     }
    // }
    // function validationStep3() {
    //     return {
    //         rules: {
    //             country: "required",
    //             state: "required",
    //             city: "required",
    //             zip_code: "required",
    //             full_address: "required",
    //         },
    //         messages: { 
    //             country: "The country field is required",
    //             state: "The state field is required",
    //             city: "The city field is required",
    //             zip_code: "The zip_code field is required",
    //             full_address: "The full_address field is required",
    //         },
    //     }
    // }
</script>