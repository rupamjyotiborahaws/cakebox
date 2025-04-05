self.addEventListener('push', event => {
    const data = event.data.json();
    //console.log(data);
    const title = data.title;
    const options = {
        body: data.body,
        data: data.data || {},
        silent: false,
        vibrate: [100, 50, 100],
        actions: [
            {
                action: 'open_url',
                title: 'Open App',
            },
            {
                action: 'dismiss',
                title: 'Dismiss',
            }
        ]
    };
    event.waitUntil(
        self.registration.showNotification((title, options))
    );
});

// self.addEventListener('notificationclick', function(event) {
//     event.notification.close();

//     if(event.notification.data && event.notification.data.url) {
//         clients.openWindow(event.notification.data.url);
//     }
// });

self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    const url = event.notification.data?.url;
    if (url) {
        clients.openWindow(url);
    }
});