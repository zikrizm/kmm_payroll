const response = () => {
    return {
        response: res,
        status: status,
        data: data,
        msg: msg
    }
}
const ApiService = {
    getTable: (URL, data) => {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: 'GET',
                url: URL,
                success: function (res) {
                    if (res.response < 200 || res.response >= 300) {
                        // handle Request Error
                        console.log(res);
                    } else {
                        resolve(res.data)
                    }
                }, error: function (error) {
                    reject(error)
                }, statusCode: {
                    403: function () {
                        alert('Anda Tidak Berhak Mengakses Menu ini');
                    }
                }
            });
        })
    },
    getModal: (URL, data) => {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: 'GET',
                url: URL,
                success: function (res) {
                    if (res.response < 200 || res.response >= 300) {
                        // handle Request Error
                    } else {
                        resolve(res.data)
                    }
                }, error: function (error) {
                    reject(error)
                }, statusCode: {
                    403: () => alert('Anda Tidak Berhak Mengakses Menu ini')
                }
            });
        })
    },
}