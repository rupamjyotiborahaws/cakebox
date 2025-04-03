"use strict"

let base_origin = window.location.origin;
$(document).ready(function() {
    $(document).on('click', '.profile-update', function(e){
        e.preventDefault();
        $(this).html('Update');
        $(this).addClass('submit-update-profile');
        $('.user-form-data').prop("disabled", false);
    });

    $(document).on('click', '.submit-update-profile', function(e){
        e.preventDefault();
        let name = $('#name').val();
        let email = $('#email').val();
        let phone_no = $('#phone_no').val();
        $.ajax({
            url: base_origin+'/api/v1/update-profile',
            type:'POST',
            xhrFields: { withCredentials: true },
            headers: {
                "Accept": "application/json"
            },
            data: {name,email,phone_no},
            success :function(resp){
                console.log(resp);
                if(resp.status == 'success'){
                    $('.alert-danger').addClass('d-none');
                    $('.alert-success').removeClass('d-none');
                    $('.success-msg').text(resp.msg);
                } else {
                    $('.alert-danger').removeClass('d-none');
                    $('.alert-success').addClass('d-none');
                    $('.error-msg').text(resp.msg);
                }
            }
        });
    });
});

