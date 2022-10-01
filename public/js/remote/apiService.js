class Api extends NetworkUtils {
    async get_table(url, data) {
        try {
            let _response = await this.emitter('GET', url, data, {})
            if (_response.response < 200 || _response.response >= 300) {
                // * SHOW NOTIFICATION ----->
                handleMessage(_response);
                return null;
            } else {
                return _response.data;
            }
        } catch (error) {

        }
    };
    async get_modal(url, data) {
        try {
            let _response = await this.emitter('GET', url, data)
            if (_response.response < 200 || _response.response >= 300) {
                return null;
            } else {
                $('.content-main-modal').html(_response.data);
                // * OPEN MODAL ----->
                openModal({ name: '.main-modal', content: '.content-main-modal' });
                return null;
            }
        } catch (error) {

        }
    };
    async submit_form(className, callback) {
        var url = $(className).attr('action');
        var method = $(className).attr('method');
        $(className).on("submit", async (e) => {
            e.preventDefault();
            var form = new FormData();
            var filename = document.querySelector('#contained-button-file').files[0].name;
form.append("user_capture", document.querySelector('#contained-button-file').files[0], filename);
form.append("employee_code", "50");
form.append("csrfmiddlewaretoken", "R15uMIPc1xH0rRZEdNK0ygPn1sJHu9h7BP6a8OVgNMXp98LQE9zwHc3aZuEQoNVF");
form.append("remark", "");
console.log('formdata',document.querySelector('#contained-button-file').files[0])
var settings = {
    crossDomain: true,
    "url": "http://192.168.2.20/vlRegister/",
    "method": "POST",
    "timeout": 0,
    "headers": {
        "Access-Control-Allow-Headers": '*'
        },
    "processData": false,
    "mimeType": "multipart/form-data",
    "contentType": false,
    "data": form
  };
  
  $.ajax(settings).done(function (response) {
    console.log(response);
  });
            // try {
            //     let _response = await this.emitter(method, url, new FormData(document.querySelector(className)));
            //     console.log(_response)
            //     if (_response.response < 200 || _response.response >= 300) {
            //         // * SET NOTIFICATION MESSAGE REQUIRED ----->
            //         setErorrsformInputs(_response.msg);
            //         callback();
            //     } else {
            //         handleMessage(_response);
            //         // * CLEAR ERROR ----->
            //         clearErrorFormInputs();
            //         // * CLOSE MODAL ----->
            //         closeModal({ name: '.main-modal', content: '.content-main-modal' });
            //         callback(_response.data);
            //     }
            // } catch (error) {
            //     console.log(error)
            // }
        })
    };
    async get_confirm(className, url, data, callback) {
        // * OPEN MODAL ----->
        openModal({ name: '.confirmation-modal', content: null });

        var method = $(className).attr('method');
        $(className).on("submit", async (e) => {
            e.preventDefault();
            try {
                let _response = await this.emitter(method, url, data)
                if (_response.response < 200 || _response.response >= 300) {
                    // * SET NOTIFICATION MESSAGE REQUIRED ----->
                    callback();
                } else {
                    handleMessage(_response);
                    // * CLEAR ERROR ----->
                    clearErrorFormInputs();
                    // * CLOSE MODAL ----->
                    closeModal({ name: '.confirmation-modal', content: null });
                    callback(_response.data);
                    $(className).off();
                }
            } catch (error) {
                console.log(error)
            }
        })
    };
    async get_token_zkteco() {
        try {
            let _response = await this.emitter('GET', '/get-token-zkteco', null)
            if (_response.response < 200 || _response.response >= 300) {
                handleMessage(_response);
            } else {
                handleMessage(_response);
                return _response.data;
            }
        } catch (error) {

        }
    }
}

const ApiService = new Api();