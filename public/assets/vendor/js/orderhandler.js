"use strict"
const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
let base_origin = window.location.origin;
let feedbackModal = '';
let feedback_slid = '';
let cancelModal = '';
let modifyModal = '';
function loadDataIntoTable(data) {
    let html_data = '';
    let slno = 1;
    for(let i = 0;i < data.length; i++) {
        let pay_btn = '';
        let feedback_btn = '';
        let cancel_btn = '';
        let update_btn = '';
        //const o_date = new Date(data[i].order_date);
        const d_date = new Date(data[i].delivery_date_time);
        let img_url = '';
        if(data[i].design_reference != '') {
            img_url += '<p style="width:300px;"><a href='+base_origin+'/storage/'+data[i].design_reference+' target="_blank">Your uploaded design</a></p>';
        }
        if(data[i].payment_status == 'pending' && parseFloat(data[i].amount_to_be_paid) != 0.00) {
            pay_btn += '&nbsp;&nbsp;&nbsp;<button class="btn btn-success btn-sm pay_now" id="pay_now_'+slno+'" data-slno="'+slno+'" data-amount="'+data[i].amount_to_be_paid+'" data-tamount="'+data[i].total_amount+'" data-pamount="'+data[i].amount_paid+'" data-order="'+data[i].order_no+'">Pay now</button>';
        } else {
            pay_btn += '&nbsp;&nbsp;&nbsp;<button class="btn btn-success btn-sm" id="pay-btn-'+slno+'">Paid</button>';    
        }
        html_data += '<tr class="see_detail" data-id='+slno+'><td style="width:20%;">'+slno+'</td><td style="width:40%;">'+data[i].order_no+'</td>';
        if(data[i].status == 1){ html_data += '<td style="width:40%;">Pending</td></tr>';}
        else if(data[i].status == 2){ html_data += '<td style="width:40%;">Processing</td></tr>';}
        else if(data[i].status == 3){ html_data += '<td style="width:40%;">Delivered</td></tr>';}
        html_data += '<tr class="show_detail d-none" id="order_'+slno+'">';
        html_data += '<td style="width:100%;"><p style="width:300px;">Cake : '+data[i].cake_type+'</p><p style="width:300px;">Flavor : '+data[i].cake_flavor+'</p><p style="width:300px;">Weight: '+data[i].cake_weight+'</p><p style="width:300px;">Delivery on: '+d_date.getDate()+' '+months[d_date.getMonth()]+', '+d_date.getFullYear()+'</p><p style="width:300px;">Total amount : '+data[i].total_amount+'</p><p id="amount_paid_'+slno+'" style="width:300px;">Amount Paid : '+data[i].amount_paid+'</p><p id="amount_to_be_paid_'+slno+'" style="width:300px;">Amount to be paid : '+data[i].amount_to_be_paid+' '+pay_btn+'</p><p style="width:300px;" id="payment_id_'+slno+'">Payment ID : '+data[i].payment_id+'</p>';
        if(data[i].status == 1) {
            cancel_btn += '<button type="button" class="btn btn-danger btn-sm cancel" data-order="'+data[i].order_no+'" data-id="cancel_btn_'+slno+'" id="cancel_btn_'+slno+'">Cancel Order</button>';
            update_btn += '<button type="button" class="btn btn-success btn-sm modify" style="margin-left:20px;" data-order="'+data[i].order_no+'" data-id="modify_btn_'+slno+'" id="modify_btn_'+slno+'">Modify Order</button>';        
        }
        if(data[i].status == 3 && data[i].feedback == '') {
            feedback_btn += '<button type="button" class="btn btn-success btn-sm feedback" data-order="'+data[i].order_no+'" data-id="feedback_btn_'+slno+'" id="feedback_btn_'+slno+'">Give Feedback</button>';        
        } else if(data[i].status == 3 && data[i].feedback != '') {
            feedback_btn += '<p style="width:300px;">Your Feedback : '+data[i].feedback+'</p>';    
        }
        html_data += cancel_btn+update_btn+feedback_btn+img_url+'</td></tr>';
        slno++;
    }
    $('.orders-tbody').html(html_data);
}

$(document).ready(function() {
    $.ajax({
        url: '/api/v1/get-my-orders',
        type:'GET',
        xhrFields: { withCredentials: true },
        headers: {
            "Accept": "application/json"
        },
        success :function(resp){
            if(resp.status == 'success'){
                if(resp.data.length == 0) {
                    $('.orders-data').addClass('d-none');
                } else {
                    $('.no-data').addClass('d-none');
                    loadDataIntoTable(resp.data);
                }
            }
        }
    });
});

$(document).on('click', '.see_detail', function(){
    let order_id = $(this).data('id');
    if($('#order_'+order_id).hasClass('d-open')) {
        $('#order_'+order_id).removeClass('d-open');
        $('#order_'+order_id).addClass('d-none');        
    } else if($('#order_'+order_id).hasClass('d-none')) {
        $('#order_'+order_id).removeClass('d-none');
        $('#order_'+order_id).addClass('d-open');
        for(let i=1;i<=$('.show_detail').length;i++){
            if(i!=order_id) {
                $('#order_'+i).removeClass('d-open');
                $('#order_'+i).addClass('d-none');    
            }
        }
    }
});

$(document).on('click', '.feedback', function() {
    $('.feedback-order-no').text('');
    $('.feedback-text').val('');
    $('.rating').val('');
    let order_id = $(this).data('order');
    feedback_slid = $(this).data('id');
    $('.feedback-order-no').text(order_id);
    $('.alert-msg').addClass('d-none');
    feedbackModal = new bootstrap.Modal(document.getElementById('feedbackModal'));
    feedbackModal.show();
});

$(document).on('click', '.feedback-submit', function() {
    let order_no = $('.feedback-order-no').text();
    let feedback = $('.feedback-text').val();
    let rating = $('.rating').val();
    if(feedback != "" && rating != "") {
        $.ajax({
            url: '/api/v1/submit-feedback',
            type:'POST',
            xhrFields: { withCredentials: true },
            headers: {
                "Accept": "application/json"
            },
            data: {order_no,feedback,rating},
            success :function(resp){
                if(resp.status == 'success'){
                    $('.alert-msg').text(resp.message);
                    $('.alert-msg').removeClass('d-none');    
                } else {
                    $('.alert-msg').text(resp.message);
                    $('.alert-msg').removeClass('d-none');    
                }
            },
            error : function(err) {
                console.log(err);
            },
            complete : function() {
                setTimeout(() => {
                    feedbackModal.hide();
                    $('#'+feedback_slid).hide(300);
                }, 1500);
            }
        });
    } else {
        if(feedback == '' && rating == ''){
            $('.alert-msg').text('Please write your feedback and provide rating between 1 to 5');
        } else if(feedback == ''){
            $('.alert-msg').text('Please write your feedback');
        } else if(rating == ''){
            $('.alert-msg').text('Please provide a rating between 1 to 5');
        }
        $('.alert-msg').removeClass('d-none');
        setTimeout(() => {
            $('.alert-msg').addClass('d-none');    
        }, 5000);     
    }
});

$(document).on('click', '.cancel', function() {
    $('.cancel-order-no').text('');
    $('.cancel-body').find('.cancel_order').remove();
    let order_id = $(this).data('order');
    let id = $(this).data('id');
    $('.cancel-order-no').text(order_id);
    let yes_btn = '<button type="button" class="btn btn-danger cancel_order" data-order="'+order_id+'" data-id="'+id+'" style="float:left;">Yes</button>';
    $('.cancel-body').append(yes_btn);
    cancelModal = new bootstrap.Modal(document.getElementById('cancelModal'));
    cancelModal.show();
});

$(document).on('click', '.cancel_order', function() {
    let order_no = $(this).data('order');
    $('.cancel-order-no-text').text('');
    $('.orderCancelConfirmText').text('');
    $.ajax({
        url: '/api/v1/cancel-order',
        type:'POST',
        xhrFields: { withCredentials: true },
        headers: {
            "Accept": "application/json"
        },
        data: {order_no},
        success :function(resp){
            if(resp.status == 'success'){
                cancelModal.hide();
                $('.cancel-order-no-text').text(order_no);
                $('.orderCancelConfirmText').text(resp.message);
                let cancelConfirmModal = new bootstrap.Modal(document.getElementById('cancelConfirmModal'));
                cancelConfirmModal.show();    
            } else {
                cancelModal.hide();
                $('.cancel-order-no-text').text(order_no);
                $('.orderCancelConfirmText').text(resp.message);
                let cancelConfirmModal = new bootstrap.Modal(document.getElementById('cancelConfirmModal'));
                cancelConfirmModal.show();    
            }
        },
        error : function(err) {
            console.log(err);
        },
        complete : function() {
            setTimeout(() => {
                window.location.reload();
            }, 3000);
        }
    });
});

$(document).on('click', '.modify', function() {
    $('.modify-body').html('');
    $('.modify-order-no').text('');
    let order_no = $(this).data('order');
    $('.modify-order-no').text(order_no);
    modifyModal = new bootstrap.Modal(document.getElementById('modifyModal'));
    $.ajax({
        url: '/api/v1/get-order-info',
        type:'GET',
        xhrFields: { withCredentials: true },
        headers: {
            "Accept": "application/json"
        },
        data: {order_no},
        success :function(resp){
            if(resp.status == 'success'){
                console.log(resp.data);
                let result = resp.data.result[0];
                $('#o_id').val(result.oid);
                let html = '<p style="font-size:16px; font-weight:500; color: green; text-align:center;" class="msg-success d-none;"></p>';
                html += '<p style="font-size:16px; font-weight:500; color: red; text-align:center;" class="msg-error d-none;"></p>';
                html += '<div class="row" style="margin-bottom:10px;"><div class="col-md-4 col-lg-4 col-sm-4 col-xs-12">Occassion</div><div class="col-md-8 col-lg-8 col-sm-8 col-xs-12"><input type="text" class="form-control" name="occassion" id="occassion" value="'+result.occassion+'" /></div></div>';
                html += '<div class="row" style="margin-bottom:10px;">';
                html += '<div class="col-md-4 col-lg-4 col-sm-4 col-xs-12">Cake Type</div><div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">';
                html += '<select class="form-control" name="cake_type" id="cake_type">';
                html += '<option value="'+result.typeid+'">'+result.cake_type+'</option>';
                resp.data.types.forEach(ctype => {
                    if(ctype.id != result.typeid) {
                        html += '<option value="'+ctype.id+'">'+ctype.cake_type+'</option>';    
                    }
                });
                html += '</select></div></div>';

                html += '<div class="row" style="margin-bottom:10px;">';
                html += '<div class="col-md-4 col-lg-4 col-sm-4 col-xs-12">Flavor</div><div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">';
                html += '<select class="form-control" name="cake_flavor" id="cake_flavor">';
                html += '<option value="'+result.flavorid+'">'+result.flavor_name+'</option>';
                resp.data.flavors.forEach(flavor => {
                    if(flavor.id != result.flavorid) {
                        html += '<option value="'+flavor.id+'">'+flavor.flavor_name+'</option>';    
                    }
                });
                html += '</select></div></div>';

                html += '<div class="row" style="margin-bottom:10px;">';
                html += '<div class="col-md-4 col-lg-4 col-sm-4 col-xs-12">Weight</div><div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">';
                html += '<select class="form-control" name="cake_weight" id="cake_weight">';
                html += '<option value="'+result.weightid+'">'+result.cake_weight+'</option>';
                resp.data.weights.forEach(weight => {
                    if(weight.id != result.weightid) {
                        html += '<option value="'+weight.id+'">'+weight.cake_weight+'</option>';    
                    }
                });
                html += '</select></div></div>';

                html += '<div class="row" style="margin-bottom:10px;">';
                html += '<div class="col-md-4 col-lg-4 col-sm-4 col-xs-12">Instruction</div><div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">';
                html += '<textarea class="form-control" name="cake_instruction" id="cake_instruction">'+result.instruction+'</textarea>';
                html += '</div></div>';

                html += '<div class="row" style="margin-bottom:10px;">';
                html += '<div class="col-md-4 col-lg-4 col-sm-4 col-xs-12">Delivery Date</div><div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">';
                html += '<input type="date" class="form-control" name="delivery_date" id="delivery_date" value="'+resp.data.del_date+'">';
                html += '</div></div>';

                html += '<div class="row" style="margin-bottom:10px;">';
                html += '<div class="col-md-4 col-lg-4 col-sm-4 col-xs-12">Delivery Time</div><div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">';
                html += '<input type="time" class="form-control" name="delivery_time" id="delivery_time" value="'+resp.data.del_time+'">';
                html += '</div></div>';

                html += '<div class="row" style="margin-bottom:10px;">';
                html += '<div class="col-md-4 col-lg-4 col-sm-4 col-xs-12">Design Preference</div><div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">';
                if(result.design_reference == '') {
                    html += '<input type="file" class="form-control" name="design_reference" id="design_reference">';    
                } else {
                    html += '<img src="" width="200" height="200" alt="cake_design"/><br /> Change design reference photo : <input type="file" class="form-control" name="design_reference" id="design_reference">';    
                }
                html += '</div></div>';

                html += '<div class="row" style="margin-bottom:10px;">';
                html += '<div class="col-md-4 col-lg-4 col-sm-4 col-xs-12"><button class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close" style="float:left;">Cancel</button></div>';
                html += '<div class="col-md-8 col-lg-8 col-sm-8 col-xs-12"><button class="btn btn-success update_order" style="float:right;">Update</button></div>';
                html += '</div></div>';

                $('.modify-body').append(html);
                modifyModal.show();    
            } else {
                
                modifyModal.show();    
            }
        },
        error : function(err) {
            console.log(err);
        }
    });
});

$(document).on('click', '.update_order', function() {
    let o_id = $('#o_id').val();
    let occassion = $('#occassion').val();
    let cake_type = $('#cake_type').val();
    let flavor = $('#cake_flavor').val();
    let weight = $('#cake_weight').val();
    let instruction = $('#cake_instruction').val();
    let delivery_date = $('#delivery_date').val();
    let delivery_time = $('#delivery_time').val();
    let file = $('#design_reference')[0].files[0];
    $.ajax({
        url: '/api/v1/update-order',
        type:'POST',
        xhrFields: { withCredentials: true },
        data: {
            'o_id' : o_id,
            'occassion' : occassion,
            'cake_type' : cake_type,
            'cake_flavor' : flavor,
            'cake_weight' : weight,
            'cake_instruction' : instruction,
            'cake_delivery_date' : delivery_date,
            'cake_delivery_time' : delivery_time,
            'image' : file
        },
        success :function(resp){
            if(resp.status == 'success'){
                $('.msg-success').text(resp.message);
                $('.msg-success').removeClass('d-none');
                $('.msg-error').addClass('d-none');   
            } else {
                $('.msg-error').text(resp.message);
                $('.msg-error').removeClass('d-none');
                $('.msg-success').addClass('d-none');   
            }
        },
        error : function(err) {
            console.log(err);
        },
        complete : function() {
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        }
    });
});
