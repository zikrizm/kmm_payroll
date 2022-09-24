import _ from 'lodash';
import $ from 'jquery';
import 'bootstrap';

window._ = _;


/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.jQuery = window.$ = $;
} catch (e) { }

// import 'jszip';
// import 'pdfmake';
// import 'datatables.net';
// import 'datatables.net-autofill-zf';
// import 'datatables.net-buttons-zf';
// import 'datatables.net-buttons/js/buttons.colVis.js';
// import 'datatables.net-buttons/js/buttons.html5.js';
// import 'datatables.net-buttons/js/buttons.print.js';
// import 'datatables.net-colreorder-zf';
// import 'datatables.net-datetime';
// import 'datatables.net-fixedcolumns-zf';
// import 'datatables.net-fixedheader-zf';
// import 'datatables.net-keytable-zf';
// import 'datatables.net-responsive-zf';
// import 'datatables.net-rowgroup-zf';
// import 'datatables.net-rowreorder-zf';
// import 'datatables.net-scroller-zf';
// import 'datatables.net-searchbuilder-zf';
// import 'datatables.net-searchpanes-zf';
// import 'datatables.net-select-zf';
// import 'datatables.net-staterestore-zf';

// import validate from 'jquery-validation';
// import moment from 'moment';
// window.moment = moment;
// console.log(typeof window.moment);
// import daterangepicker from 'daterangepicker';
// import AutoNumeric from 'autonumeric';
// window.AutoNumeric = AutoNumeric;

// import feather from 'feather-icons';
// feather.replace()

// import select2 from 'select2';
// import 'select2/dist/css/select2.css';
// select2($);

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// import Pusher from 'pusher-js';
// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     wsHost: import.meta.env.VITE_PUSHER_HOST ?? `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
//     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });
