class NetworkUtils {
    emitter(method, url, data, options = {
        processData: false,
        contentType: false,
    }) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: method,
                url: url,
                data: data,
                cache: false,
                "mimeType": "multipart/form-data",
                ...options,
                success: function (_response) {
                    console.log(_response);
                    resolve(_response);
                }, error: function (error) {
                    console.log(error);
                    reject(error);
                    // **
                    // * SHOW NOTIFICATION ----->
                    // *
                    toastr.error('Something wrong', 'Error information');
                }, statusCode: {
                    403: () => toastr.warning('Warning information', 'You are not allowed to access this menu'),
                    404: () => toastr.warning('Warning information', '404 not found'),
                }
            });
        })
    };
}