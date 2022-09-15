class NetworkUtils {
    emitter(method, path_name, data) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: method,
                url: path_name,
                data: data,
                cache: false,
                success: function (res) {
                    resolve(res);
                }, error: function (error) {
                    toastr.error(error.msg, 'Error information');
                }, statusCode: {
                    403: () => toastr.warning('Warning information', 'You are not allowed to access this menu'),
                    404: () => toastr.warning('Warning information', '404 not found'),
                }
            });
        })
    }
}