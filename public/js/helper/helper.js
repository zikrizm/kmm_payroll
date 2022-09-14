const OBJECTManagement = {
    set: function (obj, propString, value) {
        var propNames = propString.split('.'),
            propLength = propNames.length - 1,
            tmpObj = obj;
        for (var i = 0; i <= propLength; i++) {
            if (i === propLength) {
                if (tmpObj[propNames[i]]) {
                    tmpObj[propNames[i]] = value;
                } else {
                    tmpObj[propNames[i]] = value;
                }
            } else {
                if (tmpObj[propNames[i]]) {
                    tmpObj = tmpObj[propNames[i]];
                } else {
                    tmpObj = tmpObj[propNames[i]] = {};
                }
            }
        }
        return obj;
    },
    get: function (obj, propString) {
        if (propString) {
            var propNames = propString.split('.'),
                propLength = propNames.length - 1,
                tmpObj = obj;

            if (propNames.length > 1) {
                for (var i = 0; i <= propLength; i++) {
                    if (tmpObj[propNames[i]] || tmpObj[propNames[i]] == '') {
                        tmpObj = tmpObj[propNames[i]];
                    } else if (tmpObj[propNames[i]] == undefined) {
                        return;
                    } else {
                        break;
                    }
                }
                return tmpObj;
            } else {
                return tmpObj[propNames[0]]
            }

        } else false
    }
};

function networkUtils(options) {
    return new Promise(function (resolve, reject) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax(options).done(resolve).fail(reject);
    });
}

function openModal(modal) {
    $(modal.name).removeClass('invisible hidden');
    $(window).on('click', function (e) {
        if ($('.inner-modal').is($(e.target)))
            $(modal.name).addClass('invisible hidden');
    });
    $('.modal-close').on('click', function (e) {
        $(modal.name).addClass('invisible hidden');
        if (modal.content) $(modal.content).html('')
    });
}

function closeModal(modal) {
    $(modal.name).addClass('invisible hidden');
    if (modal.content) $(modal.content).html('')
}

function handlerSwitchSubMenu(options, callback) {
    const { subMenuStorageName, subMenus, idBoxContent } = options;
    var local = localStorage.getItem(subMenuStorageName);
    // var defaultTypeSubMenu = '', newType = '';
    $('.btn-sub-menu').each(function (e) {
        if (local) {
            var sameValue = $(this).val().toLowerCase() == local.toLowerCase();
            if (sameValue)
                $(this).addClass('text-green-700 border-b-2 border-green-700 active');
        } else {
            var isDefault = $(this).hasClass("default");
            if (isDefault) {
                // defaultTypeSubMenu = $(this).val();
                $(this).addClass('text-green-700 border-b-2 border-green-700 active');
            }
        }
    });
    $('.btn-sub-menu').on('click', function (e) {
        $('.btn-sub-menu').each(function (e) {
            var isActive = $(this).hasClass("active");
            if (isActive)
                $(this).removeClass('text-green-700 border-b-2 border-green-700 active')

        });
        $(this).toggleClass("text-green-700 border-b-2 border-green-700 active");
        var type = $(this).val();

        localStorage.setItem(subMenuStorageName, type);
        if (type != local) {
            local = localStorage.getItem(subMenuStorageName);
            var optionPage = {
                idBoxContent: idBoxContent,
                url: getUrl('', optionObject?.subMenuStorageName),
                data: null,
            }
            getIndexPage(optionPage, callback)
        }

    });
}

function getIndexPage(optionPage, callback) {
    $.ajax({
        type: 'GET',
        url: optionPage.url,
        data: optionPage.data ?? {},
        success: function (data) {
            $(optionPage.idBoxContent).html(data);
            callback();
        }, statusCode: {
            403: function () {
                alert('Anda Tidak Berhak Mengakses Menu ini');
            }
        }
    });
}

function getModalPage(options, callback) {
    var optionMenu = filterOption(options);
    $.ajax({
        type: 'GET',
        url: optionMenu?.url,
        data: optionMenu?.data ?? {},
        success: function (data) {
            $(options?.modal?.content).html(data);
            openModal(options?.modal);
            callback(optionMenu);
        }, statusCode: {
            403: function () {
                alert('Anda Tidak Berhak Mengakses Menu ini');
            }
        }
    });
}



function filterOption(options) {
    const { localStorageName, listSubMenu } = options;
    var local = localStorage.getItem(localStorageName);
    var dataOp = listSubMenu.find((menu) => menu.type == local);
    return dataOp;
}

function onSelectAllCheckbox(event, nodeName) {
    $(nodeName).each(function (e) {
        $(this).prop('checked', $(event).is(':checked'));
    });
}

function clearError() {
    $('.text-error').each(function (e) {
        $(this).text('');
    });
}
function checkIsSamePassword(mainPass, confPass) {
    if ($('input[name=' + mainPass + ']').val() != $('input[name=' + confPass + ']').val()) {
        return {
            valid: false, msg: {
                [confPass]: ['Confirm password not match']
            }
        };
    }
    return { valid: true, msg: null };
}
function loadPic(inputHid, imgID, removeImg) {
    var output = document.getElementById(imgID);
    var url = URL.createObjectURL(event.target.files[0]);
    output.setAttribute('src', url);
    output.onload = function () {
        if (!$(inputHid).val()) compressFotoBarang(inputHid, imgID);
        $(removeImg).removeClass('hidden');
        URL.revokeObjectURL(output.src);
    }
}
async function compressFotoBarang(inputHid, imgID) {
    var a = await jic.compress(document.getElementById(imgID), 30, "jpg")
    $(inputHid).val(a);
}

function removePhoto(inputHid, imgID, inputFile, node) {
    $(node).addClass('hidden');
    $(imgID).attr('src', '');
    $(inputHid).val(null);
    $(inputFile).val(null)
}


function handletogglebutton(defautlValue) {
    if (defautlValue) {
        $('.btn-status').each(function (e) {
            var value = $(this).val();
            if (value.toLowerCase() == defautlValue.toLowerCase()) {
                $(this).toggleClass("bg-gray-100");
                $('#togglebutton').val($(this).val());
            }
        });
    }

    $('.btn-status').on('click', function (e) {
        $('.btn-status').each(function (e) {
            var hasClass = $(this).hasClass("bg-gray-100");
            if (hasClass)
                $(this).removeClass('bg-gray-100')
        });
        $(this).toggleClass("bg-gray-100");
        $('#togglebutton').val($(this).val());
    });
}


function getUrl(type, subMenuStorageName, id) {
    try {
        var typeLocal = localStorage.getItem(subMenuStorageName);
        var url = '';
        switch (type) {
            case 'create':
                url = `/${typeLocal}/create`;
                break;
            case 'edit':
                if (!id) throw "Error! id tidak boleh kosong";    // throw a text
                url = `/${typeLocal}/${id}/edit`;
                break;
            case 'delete':
                if (!id) throw "Error! id tidak boleh kosong";    // throw a text
                url = `/${typeLocal}/${id}`;
                break;
            default:
                url = `/${typeLocal}`;
                break;
        }
        return url;
    } catch (error) {
        console.error(error)
    }
}


function setErorrsformInputs(errors) {
    $('.hint-text').each(function (e) {
        $(this).css('opacity', '0');
    });
    if (typeof errors != 'string') {
        if (Object.keys(errors).length != 0) {
            for (const error in errors) {
                var textError = errors[error][0];
                $(`.${error}`).text(textError);
                $(`.${error}`).css('opacity', '1');
            }
            var firstErorr = Object.keys(errors)[0];
            var firstElementParent = document.querySelector(`.${firstErorr}`).parentNode.parentNode;
            const intersectionObserver = new IntersectionObserver((entries) => {
                let [entry] = entries;
                if (entry.isIntersecting) {
                    var nodeNameInput = '';
                    if (firstErorr == 'full_address') {
                        nodeNameInput = 'textarea[name=' + firstErorr + ']';
                    } else {
                        nodeNameInput = 'input[name=' + firstErorr + ']';
                    }

                    $(`.${firstErorr}`).parent().children().find(nodeNameInput).focus();
                }
            });
            intersectionObserver.observe(firstElementParent);
            firstElementParent.scrollIntoView({ behavior: "smooth" });
        }
    }
}

function debounce(func, wait, immediate) {
    var timeout;

    return function executedFunction() {
        var context = this;
        var args = arguments;

        var later = function () {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };

        var callNow = immediate && !timeout;

        clearTimeout(timeout);

        timeout = setTimeout(later, wait);

        if (callNow) func.apply(context, args);
    };
};


const Utils = {
    table(url, data) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: 'GET',
                url: url,
                data: data,
                cache: false,
                success: function (res) {
                    if (res.response < 200 || res.response >= 300) {
                        // handle Request Error
                        console.log(res);
                    } else {
                        resolve(res.data)
                    }
                }, error: function (error) {
                    console.log(error);
                }, statusCode: {
                    403: function () {
                        alert('Anda Tidak Berhak Mengakses Menu ini');
                    }
                }
            });
        })
    },
    modal(url, data) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: 'GET',
                data: data,
                url: url,
                cache: false,
                success: function (res) {
                    console.log(res.status);
                    if (res.response < 200 || res.response >= 300) {
                        // handle Request Error
                        console.log(res);
                    } else {
                        $('.content-main-modal').html(res.data);
                        openModal({ name: '.main-modal', content: '.content-main-modal' });
                        resolve()
                    }
                }, error: function (error) {
                    console.log(error);
                }, statusCode: {
                    403: () => alert('Anda Tidak Berhak Mengakses Menu ini')
                }
            });
        })
    },
    modal_confirm(node_name, url, data, callback) {
        const Method = $(node_name).attr('method');
        openModal({ name: '#confirmation-modal', content: null });
        $(node_name).submit(function (e) {
            e.preventDefault();
            $.ajax({
                type: Method,
                url: url,
                data: new FormData(this),
                cache: false,
                processData: false,
                contentType: false,
                success: function (res) {
                    if (res.response < 200 || res.response >= 300) {
                        // handle Request Error
                        if (typeof res.msg == 'string')
                            toastr.error('Error information', res.msg);
                    } else {
                        closeModal({ name: '#confirmation-modal', content: null });
                        toastr.success('Successfully information', res.msg);
                        callback()
                    }
                },error: function (error) {
                    toastr.error('Error information', error.msg);
                },  statusCode: {
                    403: () => toastr.warning('Warning information', 'You are not allowed to access this menu'),
                }
            });

        });
    },
    submit(node_name, callback) {
        const Url = $(node_name).attr('action'),
            Method = $(node_name).attr('method');

        $(node_name).on("submit", function (e) {
            e.preventDefault();
            $.ajax({
                type: Method,
                url: Url,
                data: new FormData(this),
                cache: false,
                processData: false,
                contentType: false,
                success: function (res) {
                    if (res.response < 200 || res.response >= 300) {
                        setErorrsformInputs(res.msg);
                        if (typeof res.msg == 'string')
                            toastr.error('Error information', res.msg);
                    } else {
                        clearError();
                        closeModal({ name: '.main-modal', content: '.content-main-modal' });
                        toastr.success('Successfully information', res.msg);
                        callback(res);
                    }
                }, error: function (error) {
                    toastr.error('Error information', error.msg);
                }, statusCode: {
                    403: () => toastr.warning('Warning information', 'You are not allowed to access this menu'),
                }
            });
        })

    }
}