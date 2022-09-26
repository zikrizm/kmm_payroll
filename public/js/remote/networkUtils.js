class NetworkUtils {
    emitter(method, path_name, data) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: method,
                url: path_name,
                data: data,
                cache: false,
                success: function (_response) {
                    resolve(_response);
                    // **
                    // * SHOW NOTIFICATION ----->
                    // *
                    if (_response.msg) {
                        for (const msg in _response.msg) {
                            (_response.status == 'error') ?
                                toastr.error(_response.msg[msg], 'Error information') :
                                toastr.success(_response.msg[msg], 'Successfully information');
                        }
                    }
                }, error: function (error) {
                    reject(error);
                    // **
                    // * SHOW NOTIFICATION ----->
                    // *
                    toastr.error(error.msg, 'Error information');
                }, statusCode: {
                    403: () => toastr.warning('Warning information', 'You are not allowed to access this menu'),
                    404: () => toastr.warning('Warning information', '404 not found'),
                }
            });
        })
    }
}