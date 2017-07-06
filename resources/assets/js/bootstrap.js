/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo'


window.Echo = new Echo({
    broadcaster: 'socket.io',
    host: E_CONFIG.url + ':6001'
});
