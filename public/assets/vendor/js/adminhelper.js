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
        card += '<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">';
        card += '<div class="d-flex full-height align-items-center admin-dashboard-divs">';
        card += '<div class="card p-0 shadow-lg dashboard-cards">';
        if(status.order_status == 'Pending') {
            card += '<div class="card-title dashboard-cards-title card-color-pending">';
        } else if(status.order_status == 'Processing') {
            card += '<div class="card-title dashboard-cards-title card-color-processing">';
        } else if(status.order_status == 'Delivered') {
            card += '<div class="card-title dashboard-cards-title card-color-delivered">';
        }
        card += '<p class="dashboard-card-title-text">'+status.order_status+'</p>';
        card += '</div>';          
        card += '<div class="card-body admin-status-card" data-status="'+status.id+'">';
        card += '<p class="text-center dashboard-card-body-text">'+(order_count[status.id] == undefined ? 0 : order_count[status.id])+'</p>';              
        card += '</div>';
        card += '</div>';
        card += '</div>';
        card += '</div>';
        $('.dashboard-counts').append(card);
    });
}

$(document).on('click', '.admin-status-card', function(){
    let status_id = $(this).data('status');
    window.location.href = '/admin/order-details/'+status_id;
});

async function registerPush() {
    // if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
    //     alert('Push messaging is not supported.');
    //     return;
    // }

    if ('serviceWorker' in navigator && 'PushManager' in window) {
        navigator.serviceWorker.register('/service-worker.js')
            .then(reg => {
                console.log('Service worker registered:', reg);
            })
            .catch(err => {
                console.error('Service worker registration failed:', err);
            });
    }

    const registration = await navigator.serviceWorker.register('/service-worker.js');
    const subscription = await registration.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: urlBase64ToUint8Array("BH9E2StT6Y1ZvCdLCiUOQkdK2Ig7NReQ7PTYna_MGaQ_wj9UB4JKOI2TnDihWnA8s9Fj9D243YC9VCR1OSafGUI")
    });

    // Send subscription to server
    await fetch('/api/v1/push/subscribe', {
        method: 'POST',
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(subscription)
    });

    alert('Push subscription registered!');
}


function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding)
        .replace(/\-/g, '+')
        .replace(/_/g, '/');

    const rawData = window.atob(base64);
    return Uint8Array.from([...rawData].map(char => char.charCodeAt(0)));
}

