class Api extends NetworkUtils {
    async get_table(url, data) {
        try {
            let _response = await this.emitter('GET', url, data)
            if (_response.response < 200 || _response.response >= 300) {
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
                openModal({ name: '.main-modal', content: '.content-main-modal' });
                return null;
            }
        } catch (error) {

        }
    };
    async submit_form() {
        console.log('jalan kesini');
        // try {
        //     let _response = await this.emitter('GET', url, data)
        //     if (_response.response < 200 || _response.response >= 300) {
        //         return null;
        //     } else {
        //         return _response.data;
        //     }
        // } catch (error) {

        // }
    };
    async get_token_zkteco() {
        try {
            let _response = await this.emitter('GET', '/get-token-zkteco', null)
            if (_response.response < 200 || _response.response >= 300) {
                return null;
            } else {
                return _response.data;
            }
        } catch (error) {

        }
    }
}

const ApiService = new Api();