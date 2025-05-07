<!DOCTYPE html>
<html>
<head>
    <title>Razorpay Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h2>Razorpay Payment Test</h2>
    <button id="pay-btn">Pay ₹500</button>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

    <script>
        $('#pay-btn').click(function () {
            $.ajax({
                url: '/create-order',
                type: 'POST',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    var options = {
                        "key": "{{ env('RAZORPAY_KEY') }}",
                        "amount": 50000,
                        "currency": "INR",
                        "name": "CakeBox Demo",
                        "description": "Test Transaction",
                        "order_id": data.order_id,
                        "handler": function (response) {
                            console.log(response);
                            alert("Payment successful! ID: " + response.razorpay_payment_id);
                            // Optionally send response.razorpay_payment_id to your server
                        },
                        "prefill": {
                            "name": "Test User",
                            "email": "test@example.com",
                            "contact": "9999999999"
                        },
                        "theme": {
                            "color": "#3399cc"
                        }
                    };
                    var rzp = new Razorpay(options);
                    rzp.open();
                }
            });
        });
    </script>
</body>
</html>
