function redirectToLoginIfUnauthorized() {
    if (window.location.pathname === '/login') {
        return;
    }
    window.location.href = '/login';
}

if (typeof $ !== 'undefined') {
    $.ajaxSetup({
        statusCode: {
            401: redirectToLoginIfUnauthorized,
            419: redirectToLoginIfUnauthorized,
        }
    });
}

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
                ...options,
                success: function (_response) {
                    resolve(_response);
                }, error: function (error) {
                    reject(error);
                    // **
                    // * SHOW NOTIFICATION ----->
                    // *
                    // toastr.error('Something wrong', 'Error information');
                }, statusCode: {
                    401: redirectToLoginIfUnauthorized,
                    403: () => toastr.warning('Warning information', 'You are not allowed to access this menu'),
                    404: () => toastr.warning('Warning information', '404 not found'),
                    419: redirectToLoginIfUnauthorized,
                }
            });
        })
    };
}
