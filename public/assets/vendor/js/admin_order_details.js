"use strict"
let feedbackModal = '';
$(document).ready(function() {

    $(document).on('click', '.see_detail', function(){
        let order_id = $(this).data('id');
        if($('#order_'+order_id).hasClass('d-open')) {
            $('#order_'+order_id).removeClass('d-open');
            $('#order_'+order_id).addClass('d-none');
            //$(this).html('More');        
        } else if($('#order_'+order_id).hasClass('d-none')) {
            $('#order_'+order_id).removeClass('d-none');
            $('#order_'+order_id).addClass('d-open');
            //$(this).html('Less');
            for(let i=1;i<=$('.show_detail').length;i++){
                if(i!=order_id) {
                    $('#order_'+i).removeClass('d-open');
                    $('#order_'+i).addClass('d-none');    
                }
            }
        }
    });

    $(document).on('click', '.process-order', function(){
        let order_no = $(this).data('ord_no');
        let slno = $(this).data('id');
        $.ajax({
            url: '/api/v1/process-order',
            type:'GET',
            xhrFields: { withCredentials: true },
            data: {order_no},
            headers: {
                "Accept": "application/json"
            },
            success :function(resp){
                if(resp.status == 'success'){ 
                    $('#message_'+slno).text(resp.message);
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    $('#message_'+slno).text(resp.message);        
                }
            },
            error : function(err) {
                console.log(err);
            }
        });
    });

    $(document).on('click', '.deliver-order', function(){
        let order_no = $(this).data('ord_no');
        let slno = $(this).data('id');
        $.ajax({
            url: '/api/v1/deliver-order',
            type:'GET',
            xhrFields: { withCredentials: true },
            data: {order_no},
            headers: {
                "Accept": "application/json"
            },
            success :function(resp){
                if(resp.status == 'success'){ 
                    $('#message_'+slno).text(resp.message);
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    $('#message_'+slno).text(resp.message);        
                }
            },
            error : function(err) {
                console.log(err);
            }
        });
    });

    $(document).on('click', '.view-feedback', function(){
        let order_no = $(this).data('ord_no');
        $('.feedback-order-no').text('');
        $('.feedback-txt').text('');
        $('.feedback-rating').text('');
        feedbackModal = new bootstrap.Modal(document.getElementById('feedbackModal'));
        $.ajax({
            url: '/api/v1/get-order-feedback',
            type:'GET',
            xhrFields: { withCredentials: true },
            data: {order_no},
            headers: {
                "Accept": "application/json"
            },
            success :function(resp){
                let data = JSON.parse(resp.data);
                if(resp.status == 'success'){ 
                    $('.feedback-order-no').text(order_no);
                    $('.feedback-txt').text(data.feedback);
                    $('.feedback-rating').text(data.rating);
                    feedbackModal.show();
                } else {
                    $('.feedback-order-no').text(order_no);
                    $('.feedback-txt').text(resp.message);
                    $('.feedback-rating').text(resp.message);
                    feedbackModal.show();
                }
            },
            error : function(err) {
                console.log(err);
            }
        });
    });
    
});

