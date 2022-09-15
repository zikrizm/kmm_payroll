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
    get_token_zkteco: async () => {
        try {
            let _response = await remote.emitter('GET', '/get-token-zkteco', null)
            if (_response.response < 200 || _response.response >= 300) {
                for (const msg in _response.msg) {
                    toastr.error(_response.msg[msg][0], 'Error information');
                }
                return null;
            } else {
                toastr.success(_response.msg, 'Successfully information');
                return _response.data;
            }
        } catch (error) {

        }
    }
}