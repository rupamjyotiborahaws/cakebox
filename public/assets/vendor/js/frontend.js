"use strict"
let otp_id = '';
let phone_verified = false;
$(document).ready(function() {
    $(document).on("click", ".socialLogin", function (e) {
        e.preventDefault();
        let url = $(this).attr("data-url");
        if (url !== undefined) {
            //revokeCookie("__ajxd");
            let prev_url = 'https://cakebox.site';
            window.location.replace(`${url}?url=${btoa(prev_url)}`);
        }
    });

    // $("#phone_no").keyup(function (e) {
    //     e.preventDefault();
    //     let phone_no = $(this).val();
    //     if(phone_no.length == 10) {
    //         $.ajax({
    //             url: '/api/v1/send-otp',
    //             type:'POST',
    //             xhrFields: { withCredentials: true },
    //             headers: {
    //                 "Accept": "application/json"
    //             },
    //             data: {phone_no},
    //             success :function(resp){
    //                 if(resp.status == 'success'){ 
    //                     otp_id = resp.data;
    //                     $('.otp').removeClass('d-none');
    //                 } else {
    //                     $('.otp').addClass('d-none');    
    //                 }
    //             },
    //             error : function(err) {
    //                 $('.otp').addClass('d-none');
    //             },
    //             complete :function() {
                    
    //             }
    //         });
    //     }
    // });

    // $("#otp").keyup(function (e) {
    //     e.preventDefault();
    //     let otp = $(this).val();
    //     if(otp.length == 6) {
    //         $.ajax({
    //             url: '/api/v1/validate-otp',
    //             type:'POST',
    //             xhrFields: { withCredentials: true },
    //             headers: {
    //                 "Accept": "application/json"
    //             },
    //             data: {otp,otp_id},
    //             success :function(resp){
    //                 otp_id = resp.data;
    //                 if(resp.status == 'success'){ 
    //                     $('.otp_verification_text').addClass('success-msg');
    //                     $('.otp_verification_text').removeClass('error-msg');
    //                     $('.otp_verification_text').text('OTP is correct.');
    //                     //$('.otp').append('<input type="hidden" value='+btoa(otp_id)+' name="otp_id">');
    //                     setTimeout(() => {
    //                         $('.otp').hide();
    //                         $('.sign-up').removeClass('d-none');
    //                         $('.sign-up').addClass('d-open');
    //                     }, 1000);
    //                 } else {
    //                     $('.otp_verification_text').addClass('error-msg');
    //                     $('.otp_verification_text').removeClass('success-msg');
    //                     $('.otp_verification_text').text('OTP is incorrect.');    
    //                 }
    //             },
    //             error : function(err) {
    //                 $('.otp').addClass('d-none');
    //             },
    //             complete :function() {
                    
    //             }
    //         });
    //     }
    // });

    $('.sign-up').on('click', function() {
        $('.alert-danger').addClass('d-none');
        $('.loading_msg').prepend('Creating the account');
        $('.loadingzone').removeClass('d-none');
        let name = $('#name').val();
        let email = $('#email').val();
        let phone_no = $('#phone_no').val();
        let password = $('#password').val();
        $.ajax({
            url: '/api/v1/register',
            type:'POST',
            xhrFields: { withCredentials: true },
            headers: {
                "Accept": "application/json"
            },
            data: {name,email,phone_no,password,otp_id},
            success :function(resp){
                if(resp.status == 'success') {
                    $('.error-msg').text('');
                    $('.alert-danger').addClass('d-none');
                    $('.loading_msg').text('');
                    $('.loading_msg').prepend('Account created. Redirecting to Dashboard');
                    if(resp.landing) {
                        setTimeout(() => {
                            window.location.href = '/'+resp.landing;
                        }, 2000);
                    } else {
                        window.location.href = '/';
                    }
                } else {
                    $('.loading_msg').text('');
                    $('.loadingzone').addClass('d-none');
                    $('.error-msg').text(resp.message);
                    $('.alert-danger').removeClass('d-none');
                }                
            },
            error : function(err) {
                console.log(err);
            },
            complete :function() {
                
            }
        });        
    });

    $('.sign-in').on('click', function() {
        $('.alert-danger').addClass('d-none');
        $('.loading_msg').prepend('Authenticating');
        $('.loadingzone').removeClass('d-none');
        let email = $('#email').val();
        let password = $('#password').val();
        $.ajax({
            url: '/api/v1/login',
            type:'POST',
            xhrFields: { withCredentials: true },
            headers: {
                "Accept": "application/json"
            },
            data: {email,password},
            success :function(resp){
                if(resp.status == 'success') {
                    $('.error-msg').text('');
                    $('.alert-danger').addClass('d-none');
                    $('.loading_msg').text('');
                    $('.loading_msg').prepend('Redirecting to Dashboard');
                    if(resp.landing) {
                        setTimeout(() => {
                            window.location.href = resp.landing;
                        }, 2000);
                    } else {
                        window.location.href = '/';
                    }
                } else {
                    $('.loading_msg').text('');
                    $('.loadingzone').addClass('d-none');
                    $('.error-msg').text(resp.message);
                    $('.alert-danger').removeClass('d-none');
                }                
            },
            error : function(err) {
                console.log(err);
            },
        });        
    });
});
