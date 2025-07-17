"use strict"

$(document).ready(function() {

    $.ajax({
        url: '/api/v1/orders-for-admin-dashboard',
        type:'GET',
        xhrFields: { withCredentials: true },
        headers: {
            "Accept": "application/json"
        },
        success :function(resp){
            if(resp.status == 'success'){ 
                loadDashboard(resp.all_status,resp.order_count);
            } else {
                console.log('No Data Found');        
            }
        },
        error : function(err) {
            console.log(err);
        },
        complete :function() {
            
        }
    });
});

function loadDashboard(all_status, order_count) {
    all_status.forEach(status => {
        let card = '';
        //card += '<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">';
        card += '<div class="admin-dashboard-divs">';
        card += '<div class="card p-0 shadow-lg dashboard-cards">';
        if(status.order_status == 'Pending') {
            card += '<div class="card-title dashboard-cards-title card-color-pending">';
        } else if(status.order_status == 'Processing') {
            card += '<div class="card-title dashboard-cards-title card-color-processing">';
        } else if(status.order_status == 'Delivered') {
            card += '<div class="card-title dashboard-cards-title card-color-delivered">';
        } else if(status.order_status == 'Cancelled') {
            card += '<div class="card-title dashboard-cards-title card-color-cancelled">';
        }
        card += '<p class="dashboard-card-title-text">'+status.order_status+'</p>';
        card += '</div>';          
        card += '<div class="card-body admin-status-card" data-status="'+status.id+'">';
        card += '<p class="text-center dashboard-card-body-text">'+(order_count[status.id] == undefined ? 0 : order_count[status.id])+'</p>';              
        card += '</div>';
        card += '</div>';
        card += '</div>';
        //card += '</div>';
        $('.dashboard-counts').append(card);
    });
}

$(document).on('click', '.admin-status-card', function(){
    let status_id = $(this).data('status');
    window.location.href = '/admin/order-details/'+status_id;
});

// function askNotificationPermission() {
//     if(!('Notification' in window)) {
//         alert('This browser does not support notifications.');
//         return;
//     }
//     Notification.requestPermission().then(function (permission) {
//         if (permission === 'granted') {
//             console.log('Notifications allowed!');
//             registerPush();
//         } else if (permission === 'denied') {
//             console.warn('Notifications denied by the user.');
//         } else {
//             console.log('Notification permission dismissed.');
//         }
//     });
// }

