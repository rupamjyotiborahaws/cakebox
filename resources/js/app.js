import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    if(window.Echo) {
        window.Echo.private('admin-notifications')
        .notification((notification) => {
            //console.log(notification);
            let myModal = new bootstrap.Modal(document.getElementById('myModal'));
            $('.message_text').text(notification.message);
            myModal.show();
        })
        .error((error) => {
            console.log("Error subscribing to admin-notifications:", error);
        });
    }

    // Echo.channel('admin-notifications') // Listen for the public admin channel
    //     .listen('.App\\Notifications\\NewOrderNotification', (event) => {
    //         console.log('Admin Notification:', event);
    //         alert('Admin Notification: ' + event.message);
    //         // Update your admin dashboard UI with the new user information
    //     });
});
