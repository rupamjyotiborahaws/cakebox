"use strict"
const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
let base_origin = window.location.origin;
function loadDataIntoTable(data) {
    let html_data = '';
    let slno = 1;
    for(let i = 0;i < data.length; i++) {
        let pay_btn = '';
        const o_date = new Date(data[i].order_date);
        const d_date = new Date(data[i].delivery_date_time);
        let img_url = '';
        if(data[i].design_reference != '') {
            img_url += '<p style="width:300px;"><a href='+base_origin+'/storage/'+data[i].design_reference+' target="_blank">Your uploaded design</a></p>';
        }
        if(data[i].total_amount != data[i].amount_paid) {
            pay_btn += '&nbsp;&nbsp;&nbsp;<button class="btn btn-success btn-sm">Pay now</button>';
        }
        html_data += '<tr class="see_detail" data-id='+slno+'><td style="width:25%;">'+slno+'</td><td style="width:45%;">'+o_date.getDate()+' '+months[o_date.getMonth()]+', '+o_date.getFullYear()+'</td>';
        if(data[i].status == 1){ html_data += '<td style="width:30%;">Pending</td></tr>';}
        else if(data[i].status == 2){ html_data += '<td style="width:30%;">Processing</td></tr>';}
        else if(data[i].status == 3){ html_data += '<td style="width:30%;">Delivered</td></tr>';}
        html_data += '<tr class="show_detail d-none" id="order_'+slno+'">';
        html_data += '<td style="width:25%;"></td><td style="width:45%;"><p style="width:300px;">Cake : '+data[i].cake_type+'</p><p style="width:300px;">Flavor : '+data[i].cake_flavor+'</p><p style="width:300px;">Weight: '+data[i].cake_weight+'</p><p style="width:300px;">Delivery on: '+d_date.getDate()+' '+months[d_date.getMonth()]+', '+d_date.getFullYear()+'</p><p style="width:300px;">Total amount : '+data[i].total_amount+'</p><p style="width:300px;">Amount paid : '+data[i].amount_paid+'</p><p style="width:300px;">Amount to be paid : '+(data[i].total_amount-data[i].amount_paid).toFixed(2)+' '+pay_btn+'</p><p style="width:300px;">Payment ID : '+data[i].payment_id+'</p>'+img_url+'</td>';
        html_data += '<td style="width:30%;"></td></tr>';
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
                console.log(resp.data);
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
