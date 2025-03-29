"use strict"

function loadDataIntoTable(data) {
    let html_data = '';
    let slno = 1;
    for(let i = 0;i < data.length; i++) {
        html_data += '<tr><td scope="row">'+slno+'</td><td scope="row">'+data[i].order_date+'</td>';
        if(data[i].status == 1){ html_data += '<td scope="row">Pending</td>';}
        else if(data[i].status == 2){ html_data += '<td scope="row">Processing</td>';}
        else if(data[i].status == 3){ html_data += '<td scope="row">Delivered</td>';}
        html_data += '<td scope="row" class="see_detail" data-id='+slno+'>See Details</td></tr>';
        html_data += '<tr class="show_detail d-none" id="order_'+slno+'"><td>Cake : '+data[i].cake_type+'<br />Flavor : '+data[i].cake_flavor+'<br />Flavor : '+data[i].cake_weight+'</td></tr>';
        slno++;
    }
    $('.orders-tbody').html(html_data);
}

$(document).ready(function() {
    $.ajax({
        url: '/api/v1/get-my-orders',
        type:'GET',
        dataType: 'json',
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
