self.addEventListener('push', event => {
    const data = event.data.json();
    console.log(data);
    event.waitUntil(
        self.registration.showNotification(data.title, {
            body: data.body,
            actions: [
                {
                    action: 'open_url',
                    title: 'Open App',
                },
                {
                    action: 'dismiss',
                    title: 'Dismiss',
                }
            ],
            vibrate: [200, 100, 200],
            data: data.data || {}
        })
    );
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    const data = event.notification.data || {};
    const url = data.url || '/';
    if (event.action === 'open_url') {
        event.waitUntil(clients.openWindow(url));
    } else if (event.action === 'dismiss') {
        // No action — the notification is already closed
    } else {
        // Clicked on the body of the notification (not one of the buttons)
        event.waitUntil(clients.openWindow(url));
    }

    // event.notification.close();
    // const url = event.notification.data?.url;
    // if (url) {
    //     clients.openWindow(url);
    // }
});