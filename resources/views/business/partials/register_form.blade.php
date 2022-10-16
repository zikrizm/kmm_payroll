<form action="{{ route('business.store.register') }}" method="POST" enctype="multipart/form-data"
    class="submit-register-business h-full">
    @csrf
    <!-- {{ csrf_field() }} -->
    <main class="px-8 pb-8 w-full h-full flex flex-col justify-center items-center gap-6 ">
        <section class="register-content register-step-1 flex flex-col gap-6 items-center w-full max-w-[360px]">
            <div class="flex flex-col gap-2 w-full">
                <span
                    class="h-10 w-10 rounded-full bg-violet-100 flex justify-center items-center text-violet-600 border-[8px] border-violet-50 box-content">
                    <x-icon icon="book" width=20 height=20 viewBox="20 20" />
                </span>
                <div>
                    <p class="text-3xl font-semibold">Informasi bisnis</p>
                    <p class="text-sm font-normal text-gray-500">Harap berikan informasi bisnis Anda.</p>
                </div>
            </div>
            <div class="flex flex-col gap-2.5 w-full">
                <div>
                    <p class='text-gray-700 text-sm font-medium'>Logo anda</p>
                    <p class='text-gray-500 text-sm font-normal'>Ini akan ditampilkan di profil bisnis Anda.</p>
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
                            Unggah
                        </span>
                    </label>
                    <input type="hidden" name="business_logo" id="business_logo">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Nama bisnis*</label>
                    {!! FormCustom::input('name', null, ['placeholder' => 'Masukkan nama bisnis anda']) !!}
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Tanggal mulai*</label>
                    {!! FormCustom::input('start_date', null, [
                        'placeholder' => 'Pilih tanggal mulai bisnis',
                        'readonly' => true,
                        'prefixiconname' => 'calendar',
                    ]) !!}
                </div>
                <button type="button" class="mt-3 text-left" id="add-info">
                    <p class="text-base font-medium text-gray-900">Informasi tambahan</p>
                    <div class="flex items-center justify-between text-sm font-normal text-gray-500">
                        <p> Silakan lengkapi data bisnis ini jika di butuhkan </p>
                        <x-icon icon="chevron-down" class="add-info-icon duration-300" width=18 height=18
                            viewBox="20 20" />
                    </div>
                </button>
                <div id="add-info-content" class="hidden">
                    <div class="flex flex-col gap-2.5">
                        <hr class="mb-2">
                        <div class="flex flex-col gap-1">
                            <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Website</label>
                            {!! FormCustom::input('website', null, ['placeholder' => 'Masukkan nama website anda']) !!}
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Nomor kontak bisnis</label>
                            {!! FormCustom::input('mobile', null, ['placeholder' => 'Masukkan kontak bisnis']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="register-content register-step-2 flex flex-col gap-6 items-center w-full max-w-[360px] hidden">
            <div class="flex flex-col gap-6 w-full">
                <button type="button" class="flex items-center gap-2 text-violet-600 back-button" data-back-to="0">
                    <x-icon icon="arrow-left" width=20 height=20 viewBox="20 20" />
                    <p class="text-sm">Back</p>
                </button>
                <div>
                    <p class="text-3xl font-semibold">Lokasi bisnis</p>
                    <p class="text-sm font-normal text-gray-500">Harap berikan lokasi bisnis Anda.</p>
                </div>
            </div>
            <div class="flex flex-col gap-2.5 w-full">
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Kota*</label>
                    {!! FormCustom::input('city', null, ['placeholder' => 'Masukkan kota']) !!}
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Zip code*</label>
                    {!! FormCustom::input('zip_code', null, ['placeholder' => 'Massukkan zip code']) !!}
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Alamat*</label>
                    {!! FormCustom::textarea('full_address', null, ['placeholder' => 'Masukkan alamat bisnis']) !!}
                </div>
            </div>
        </section>
        <section class="register-content register-step-3 flex flex-col gap-6 items-center w-full max-w-[360px] hidden">
            <div class="flex flex-col gap-6 w-full">
                <button type="button" class="flex items-center gap-2 text-violet-600 back-button" data-back-to="1">
                    <x-icon icon="arrow-left" width=20 height=20 viewBox="20 20" />
                    <p class="text-sm">Back</p>
                </button>
                <div>
                    <p class="text-3xl font-semibold">Informasi pemilik*</p>
                    <p class="text-sm font-normal text-gray-500">Harap berikan data pemilik bisnis Anda.</p>
                </div>
            </div>
            <div class="flex flex-col gap-2 w-full">
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Nama*</label>
                    {!! FormCustom::input('first_name', null, ['placeholder' => 'masukkan nama']) !!}
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Username*</label>
                    {!! FormCustom::input('username', null, ['placeholder' => 'Masukkan username']) !!}
                </div>
                <div class="flex flex-col gap-1">
                    <label class="font-normal text-sm text-gray-500 xs/max:text-xs">Email*</label>
                    {!! FormCustom::input('email', null, ['placeholder' => 'Masukkan email', 'type' => 'email']) !!}
                </div>
                <div id="password-content" class="flex flex-col gap-2"></div>
            </div>
        </section>
        <section
            class="register-content register-step-4 flex flex-col gap-8 items-center justify-center w-full max-w-[360px] hidden">
            <header class='flex flex-col gap-y-2.5 items-center'>
                <div
                    class='h-10 w-10 rounded-full bg-violet-100 flex justify-center items-center text-violet-600 border-[8px] border-violet-50 box-content'>
                    <x-icon icon="key" width=20 height=20 viewBox="20 20" />

                </div>
                <div class="flex flex-col text-center">
                    <p class="text-gray-900 text-2xl font-semibold">Sukses</p>
                    <p class="text-gray-500 text-sm">Pembuatan bisnis berhasil.</p>
                </div>
            </header>
            <footer class="w-full">
                <a href="/login">
                    <button type="button"
                        class='flex items-center justify-center gap-2 shadow-xs rounded-lg h-9 px-3 text-white font-normal flex items-center bg-violet-600 xs/max:text-xs xs/max:rounded w-full'>
                        Login
                    </button>
                </a>
            </footer>
        </section>
        <footer class="flex flex-col gap-6 items-center w-full max-w-[360px]" id="submit-next-button">
            <button id="next-button"
                class="flex items-center justify-center gap-2 shadow-xs rounded-lg h-9 px-3 text-white font-normal flex items-center bg-violet-600 xs/max:text-xs xs/max:rounded w-full">
                Next
            </button>
        </footer>
    </main>
</form>
<script>
    var stepper = 1,
        validation;
    var rulesInfoBusiness = {
        name: "required",
        start_date: "required",
        mobile: {
            pattern: /^(^(62)[0-9]{7,15}$|^(0)[0-9]{7,15}$)$/
        }
    }
    var rulesLocationBusiness = {
        country: "required",
        state: "required",
        city: "required",
        zip_code: {
            required: true,
            pattern: /^([1-9])[0-9]{4}$/
        },
        full_address: "required",
    }
    var rulesBusinessOwner = {
        first_name: "required",
        username: "required",
        email: {
            required: true,
            pattern: /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i,
        },
        password: {
            required: true,
            minlength: 8
        },
        password_confirm: {
            required: true,
            minlength: 8,
            equalTo: 'input[name=password]'
        }
    }

    window.addEventListener('DOMContentLoaded', async (event) => {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        validation = $(".submit-register-business").submit(function(e) {
            e.preventDefault();
        }).validate({
            ignore: [],
            onfocusout: false,
            onkeyup: false,
            rules: rulesInfoBusiness,
            messages: [],
            onclick: (e) => {},
            errorPlacement: function(error, element) {
                return true;
            },
            invalidHandler: invalidHandler,
            submitHandler: submitHandler,
        });

        $('.back-button').on('click', function(e) {
            stepper--;
            setActiveStepper(stepper);
            switch (parseInt($(this).data('back-to'))) {
                case 0:
                    $('.register-step-1').removeClass('hidden');
                    $('.register-step-2').addClass('hidden');
                    $('.register-step-3').addClass('hidden');
                    validation.settings.rules = rulesInfoBusiness;
                    break;
                case 1:
                    $('.register-step-1').addClass('hidden');
                    $('.register-step-3').addClass('hidden');
                    $('.register-step-2').removeClass('hidden');
                    validation.settings.rules = {
                        ...rulesInfoBusiness,
                        ...rulesLocationBusiness,
                    };
                default:
                    break;
            }
        })
    });

    function invalidHandler(form, validator) {
        var errors = validator.numberOfInvalids();
        if (errors) {
            // * Hide all hint text input ----->
            $('.hint-text').each(function(e) {
                $(this).text('');
            });

            validator.errorList.forEach((e) => {
                let _htmlFor = $(e.element).attr("name");
                if (_htmlFor == "name") {
                    $(e.element).parent().parent().children('.name')
                        .html(e.message)
                }
                if (_htmlFor == "start_date") {
                    $(e.element).parent().parent().children(
                        '.start_date').html(e.message)
                }
                if (_htmlFor == "mobile") {
                    if ($('#add-info-content').is(':hidden')) {
                        $('#add-info').trigger('click');
                    }
                    $(e.element).parent().parent().children('.mobile')
                        .text(e
                            .message)
                }
                if (_htmlFor == "country") {
                    $(e.element).parent().parent().children(
                        '.country').html(e.message)
                }
                if (_htmlFor == "state") {
                    $(e.element).parent().parent().children(
                        '.state').html(e.message)
                }
                if (_htmlFor == "city") {
                    $(e.element).parent().parent().children(
                        '.city').html(e.message)
                }
                if (_htmlFor == "zip_code") {
                    $(e.element).parent().parent().children(
                        '.zip_code').html(e.message)
                }
                if (_htmlFor == "full_address") {
                    $(e.element).parent().parent().children(
                        '.full_address').html(e.message)
                }
                if (_htmlFor == "first_name") {
                    $(e.element).parent().parent().children(
                        '.first_name').html(e.message)
                }
                if (_htmlFor == "username") {
                    $(e.element).parent().parent().children(
                        '.username').html(e.message)
                }
                if (_htmlFor == "email") {
                    $(e.element).parent().parent().children(
                        '.email').html(e.message)
                }
                if (_htmlFor == "password") {
                    $(e.element).parent().parent().children(
                        '.password').html(e.message)
                }
                if (_htmlFor == "password_confirm") {
                    $(e.element).parent().parent().children(
                        '.password_confirm').html(e.message)
                }
            });
        }
        validator.focusInvalid();
    }

    async function submitHandler(form) {
        // * Hide all hint text input ----->
        $('.hint-text').each(function(e) {
            $(this).text('');
        });

        let rules = validation.settings.rules;
        let addRules = {};
        switch (stepper) {
            case 0:
                stepper++;
                addRules = rulesInfoBusiness;
                $('.register-step-1').removeClass('hidden');
                $('.register-step-2').addClass('hidden');
                $('.register-step-3').addClass('hidden');
                break;
            case 1:
                stepper++;
                addRules = rulesLocationBusiness;
                $('.register-step-1').addClass('hidden');
                $('.register-step-3').addClass('hidden');
                $('.register-step-2').removeClass('hidden');
                break;
            case 2:
                stepper++;
                addRules = rulesBusinessOwner;
                $('.register-step-1').addClass('hidden');
                $('.register-step-2').addClass('hidden');
                $('.register-step-3').removeClass('hidden');
                $('#password-content').html(`
                    <div class="flex flex-col gap-1">
                        <label class="font-normal text-sm text-gray-500 xs/max:text-xs">password</label>
                        {!! FormCustom::input('password', null, ['placeholder' => 'Masukkan password', 'type' => 'password']) !!}
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="font-normal text-sm text-gray-500 xs/max:text-xs">password confirm</label>
                        {!! FormCustom::input('password_confirm', null, [
                            'placeholder' => 'Masukkan konfirmasi password',
                            'type' => 'password',
                        ]) !!}
                    </div>`);
                break;
            default:
                try {
                    $('#loading-block-document').show();
                    let _response = await (new NetworkUtils()).emitter('POST',
                        "{{ route('business.store.register') }}", new FormData(form)
                    );
                    if (_response.response < 200 || _response.response >= 300) {
                        // * SET NOTIFICATION MESSAGE REQUIRED ----->
                        setErorrsformInputs(_response.msg);
                    } else {
                        clearErrorFormInputs();
                        stepper++;
                        $('.register-step-1').addClass('hidden');
                        $('.register-step-2').addClass('hidden');
                        $('.register-step-3').addClass('hidden');
                        $('.register-step-4').removeClass('hidden');
                        $('#submit-next-button').hide();

                    }
                    $('#loading-block-document').hide();
                } catch (error) {
                    console.log(error)
                }
                break;
        }

        setActiveStepper(stepper);
        validation.settings.rules = {
            ...rules,
            ...addRules
        };

        // validation.form();
    }

    function setActiveStepper(active) {
        $('.stepper-head').each(function(e) {
            var step = parseInt($(this).data('step'));
            if (step < active) {
                $(this).children('.stepper-head-icon').html(`
                    <div class="w-8 h-8 rounded-full flex items-center justify-center border-2 border-violet-600 bg-violet-50">
                        <span class="text-violet-600">
                            <x-icon icon="checkly" width=17 height=17 viewBox="20 20" strokeWidth="1" />
                        </span>
                    </div>`);
                $(this).children('.stepper-barrier').removeClass('bg-gray-500');
                $(this).children('.stepper-barrier').addClass('bg-violet-600');
            } else if (step > active) {
                $(this).children('.stepper-head-icon').html(`
                    <div class="w-8 h-8 rounded-full flex items-center justify-center border-2 border-gray-200 bg-gray-900">
                        <span class="block w-2.5 h-2.5 rounded-full bg-gray-200"></span>
                    </div>
                `);
                $(this).children('.stepper-barrier').addClass('bg-gray-500');
                $(this).children('.stepper-barrier').removeClass('bg-violet-600');
            } else {
                $(this).children('.stepper-head-icon').html(`
                    <div class="w-8 h-8 rounded-full flex items-center justify-center bg-gray-100"><span
                        class="block w-2.5 h-2.5 rounded-full bg-violet-600"></span></div>
                `);
                $(this).children('.stepper-barrier').addClass('bg-gray-500');
                $(this).children('.stepper-barrier').removeClass('bg-violet-600');
            }
        })
    }
</script>
