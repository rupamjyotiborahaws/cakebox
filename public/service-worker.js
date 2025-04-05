self.addEventListener('push', event => {
    const data = event.data.json();
    console.log(data);
    event.waitUntil(
        self.registration.showNotification(data.title, {
            body: data.body
        })
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