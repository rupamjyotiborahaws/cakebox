"use strict"

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

    //Sign Up
    /*$(document).on("click", ".sign-up", function (e) {
        e.preventDefault();
        let name = $('#name').val();
        let email = $('#email').val();
        let phone_no = $('#phone_no').val();
        if(validate(name,email,phone_no)) {
            // $.ajax({
            //     url: '/api/user-reg',
            //     type:'POST',
            //     dataType: 'application/json',
            //     data: JSON.stringify({name,email,phone_no}),
            //     success :function(resp){
            //         if(resp.status == 201){ 
            //             console.log(resp);
            //         }
            //     },
            //     error : function(err) {
            //         console.log(err);
            //     },
            //     complete :function() {
                    
            //     }
            // });
            fetch("http://127.0.0.1:8000/api/register", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json"  // Add this line
                },
                body: JSON.stringify({
                    name: "John Doe",
                    email: "john@example.com",
                    password: "password123"
                })
            });
        }
        // if (url !== undefined) {
        //     //revokeCookie("__ajxd");
        //     let prev_url = 'https://cakebox.site';
        //     window.location.replace(`${url}?url=${btoa(prev_url)}`);
        // }
    });*/
});

function validate(name,email,phone_no) {
    return true;
}
