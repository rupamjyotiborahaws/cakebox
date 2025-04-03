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

import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
    disableStats: true,
    authEndpoint: '/broadcasting/auth',  // Required for private channels
    auth: {
        withCredentials: true,
        headers: {
            // Authorization: 'Bearer ' + localStorage.getItem('token'), // Include user token if needed
            'X-CSRF-TOKEN': csrfToken
        }
    }
});

// window.Echo.connector.pusher.connection.bind('connected', function () {
//     console.log("Pusher connected!");
// });

// window.Echo.connector.pusher.connection.bind('error', function (err) {
//     console.log("Pusher error:", err);
// });

