const response = () => {
    return {
        response: res,
        status: status,
        data: data,
        msg: msg
    }
}

const remote = new NetworkUtils()
const ApiService = {
    get_table: async () => {

    },
    get_modal: async () => {

    },
    submit_form: async () => {

    },
    get_token_zkteco: async () => {
        try {
            let _response = await remote.emitter('GET', '/get-token-zkteco', null)
            if (_response.response < 200 || _response.response >= 300) {
                return null;
            } else {
                return _response.data;
            }
        } catch (error) {

        }
    }
}