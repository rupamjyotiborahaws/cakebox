@extends('layout.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="{{url('/')}}/assets/vendor/css/frontend.css" rel="stylesheet" />
<style>
    table {
        border: none; /* Remove border from table */
        width: 100%;
    }
    th, td {
        border: none; /* Remove border from table cells */
        padding: 5px;
    }
</style>
<div class="custom-container">
    <div class="admin-main-window">
        <nav>
            <div class="nav-container">
                <div class="logo">CakeBox</div>
                <input type="checkbox" id="nav-toggle" />
                <label for="nav-toggle" class="nav-toggle-label">&#9776;</label>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">About</a></li>
                    <li><a href="#">Contact Us</a></li>
                    @if(Auth::check() && Auth::user()->isAdmin === 0)
                        <li><a href="{{route('order')}}">Place Order</a></li>
                        <li><a href="{{route('your_orders')}}">Your Orders</a></li>
                        <li><a href="{{route('profile')}}">Profile</a></li>
                        <!-- <li><p href="#">Last Login : <br />{{date("l, F j, Y g:i A", strtotime(Auth::user()->last_login))}}</p></li> -->
                        <li><a href="{{route('logout_user')}}">Logout</a></li>
                    @elseif(Auth::check() && Auth::user()->isAdmin === 1)
                        <li><a href="{{route('admin_dashboard')}}">Dashboard</a></li>
                        <!-- <li><p href="#">Last Login : <br />{{date("l, F j, Y g:i A", strtotime(Auth::user()->last_login))}}</p></li> -->
                        <li><button onclick="askNotificationPermission()">Enable Notifications</button></li>
                        <li><a href="{{route('logout_admin')}}">Logout</a></li>
                    @endif
                    @if(!Auth::check())
                        <li><button class="btn btn-success" onclick="window.location='{{ route('user-login') }}'">Sign In</button>
                    @endif
                </ul>
            </div>
        </nav>

    </div>
    <div class="card p-2 shadow-lg order-div">
      <div class="card-body">
          <h5 class="card-title text-center">Your orders</h5>
          <div class="table-responsive orders-data">
              <table class="table table-bordered">
                  <thead>
                      <tr>
                          <th style="width:20%;">Sl. No.</th>
                          <th style="width:40%;">Order #</th>
                          <th style="width:40%;">Status</th>
                      </tr>
                  </thead>
                  <tbody class="orders-tbody">
                      
                  </tbody>
              </table>
          </div>
          <h5 class="card-title text-center no-data">Your have not placed any order</h5>
      </div>
    </div>
</div>
    
</div>
<!-- Modal itself -->
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="feedbackModalLabel">Order No. <label class="feedback-order-no"></label></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body feedback-body">
        <p class="alert-msg" style="text-align:center;"></p>
        <textarea class="form-control feedback-text" placeholder="Write your feedback here" required></textarea><br />
        <input type="number" class="form-control rating" placeholder="Your rating" min="1" max="5" required /><br />
        <button type="button" class="btn btn-success feedback-submit" style="float:right;">Submit</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cancelModalLabel">Order No. <label class="cancel-order-no"></label></h5>
      </div>
      <div class="modal-body cancel-body">
        <p>Do you really want to cancel this order?</p><br />
        <button type="button" class="btn btn-success" data-bs-dismiss="modal" aria-label="Close" style="float:right;">No</button>
        <button type="button" class="btn btn-danger cancel_order" style="float:left;">Yes</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="cannotCancelModal" tabindex="-1" aria-labelledby="cannotCancelModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cannotCancelModalLabel">Order No. <label class="cannotcancel-order-no"></label></h5>
      </div>
      <div class="modal-body cannotcancel-body">
        <p id="cannotcancel-msg"></p><br />
        <button type="button" class="btn btn-success" data-bs-dismiss="modal" aria-label="Close" style="float:right;">Ok</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="cancelConfirmModal" tabindex="-1" aria-labelledby="cancelConfirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cancelConfirmModalLabel">Order No. <label class="cancel-order-no-text"></label></h5>
      </div>
      <div class="modal-body cancel-text-body">
        <p class="orderCancelConfirmText"></p><br />
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modifyModal" tabindex="-1" aria-labelledby="modifyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modifyModalLabel">Order No. <label class="modify-order-no"></label></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <input type="hidden" value="" name="o_id" id="o_id" />
      </div>
      <div class="modal-body modify-body">  
      </div>
    </div>
  </div>
</div>
@endsection
<script src="{{url('/')}}/assets/vendor/js/jquery.min.js"></script>
<script src="{{url('/')}}/assets/vendor/js/orderhandler.js"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    $(document).ready(function() {
        $(document).on('click', '.pay_now', function () {
            let amount = $(this).data('amount');
            let tamount = $(this).data('tamount');
            let pamount = $(this).data('pamount');
            let order_no = $(this).data('order');
            let sl_no = $(this).data('slno');
            $.ajax({
                url: '/create-payment-order',
                type: 'POST',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {amount,order_no},
                success: function (data) {
                    var options = {
                        "key": "{{ env('RAZORPAY_KEY') }}",
                        "amount": amount,
                        "currency": "INR",
                        "name": "CakeBox",
                        "description": "Payment for CakeBox",
                        "order_id": data.order_id,
                        "handler": function (response) {
                            $.post('/payment-success', {
                                order_no: order_no,
                                payment_order_id: options.order_id,
                                payment_id: response.razorpay_payment_id,
                                signature: response.razorpay_signature,
                                _token: '{{ csrf_token() }}'
                            }, function(res) {
                                if(res.status == 'success') {
                                    $('#pay_now_'+sl_no).text('Paid');
                                    $('#pay_now_'+sl_no).removeClass('pay_now');
                                    $('#payment_id_'+sl_no).text('Payment ID : '+response.razorpay_payment_id);
                                    $('#amount_paid_'+sl_no).text('Amount Paid : '+tamount);
                                    $('#amount_to_be_paid_'+sl_no).text('Amount to be paid : 0.00');
                                    setTimeout(() => {
                                      window.location.reload();
                                    }, 2000);
                                } else {
                                    alert(res.message);
                                }
                            });
                            //alert("Payment successful! ID: " + response.razorpay_payment_id);
                        },
                        "prefill": {
                            "name": "{{ Auth::user()->name }}",
                            "email": "{{ Auth::user()->email }}",
                            "contact": "{{ Auth::user()->phone_no }}"
                        },
                        "method": {
                            netbanking: false,
                            card: true,
                            upi: true,
                            wallet: false,
                            emi: false,
                            paylater: false
                        },
                        "theme": {
                            "color": "#3399cc"
                        },
                        "modal": {
                            "ondismiss": function () {
                                alert("Payment cancelled");
                            }
                        }
                    };
                    var rzp = new Razorpay(options);
                    rzp.on('payment.failed', function (response) {
                      alert("Payment Failed: " + response.error.description);
                    });
                    rzp.open();
                    // Start polling for QR/UPI payment
                    let interval = setInterval(() => {
                        fetch('/check-status?order_id='+data.order_id)
                          .then(r => r.json())
                          .then(d => {
                              if (d.status === 'paid') {
                                  alert("Payment successful (via QR)");
                                  clearInterval(interval);
                              }
                          });
                    }, 5000);
                },
                error : function(err) {
                  console.log(err);
                },
            });
        });
    });
</script>