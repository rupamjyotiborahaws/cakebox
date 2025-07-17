@extends('layout.app')

@section('content')
<link href="{{url('/')}}/assets/vendor/css/admin.css" rel="stylesheet" />
<link href="{{url('/')}}/assets/vendor/css/frontend.css" rel="stylesheet" />
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
    <div class="order-counts">
        <div class="dashboard-counts">
            <!-- justify-content-center align-items-center -->    
            
        </div>
    </div>
</div>

<!-- Bootstrap Modal -->
<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel"> <!-- aria-hidden="true" -->
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">New Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="message_text"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Open Order</button>
            </div>
        </div>
    </div>
</div>

@endsection
<script src="{{url('/')}}/assets/vendor/js/jquery.min.js"></script>
<script src="{{url('/')}}/assets/vendor/js/adminhelper.js"></script>