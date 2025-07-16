@extends('layout.app')

<link href="{{url('/')}}/assets/vendor/css/frontend.css" rel="stylesheet" />
@section('content')
<div class="row">
    <div class="col-md-12 col-lg-12 col-xs-12 nav-div">
        @extends('frontend.navbar')
    </div>
    <div class="col-md-12 col-lg-12 col-xs-12">
        <div class="d-flex justify-content-center align-items-center full-height register-div">
            <div class="card text-center p-4 shadow-lg" style="width: 24rem;">
                <div class="card-body">
                    <div class="alert alert-danger alert-dismissible fade show alert-msg-box d-none" role="alert">
                        <p class="error-msg"></p>
                    </div>                   
                    <h5 class="card-title">Create a new account</h5>
                    <div class="mb-3 loadingzone d-none">
                        <span class="loading_msg"></span> &nbsp;&nbsp;<span class="loader"></span>
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" id="name" name="name" placeholder="Your name" required>
                    </div>
                    <div class="mb-3">
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" id="phone_no" name="phone_no" placeholder="Phone No." required>
                    </div>
                    <div class="mb-3 otp d-none">
                        <input type="text" class="form-control" id="otp" name="otp" placeholder="One Time Password" required>
                        <label class="otp_verification_text"></label>
                    </div>
                    <div class="mb-3">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    </div>
                    <div class="mb-3">
                        <input type="submit" class="btn btn-success sign-up" value="Sign Up">
                    </div>                    
                    <div class="mb-3">
                        <p>OR</p>
                    </div>
                    <div class="mb-3">
                        <button class="btn btn-primary socialLogin" data-url="auth/google">
                            Sign up with Google
                        </button>
                        <!-- <img src="{{url('/')}}/assets/vendor/imgs/facebook.png" alt="facebook" width="32" height="32" class="socialLogin" data-url="auth/facebook" /> -->
                    </div>
                    <div class="mb-3">
                        <p>Already have an account? <a href="{{route('user-login')}}">Sign In Here</a></p>
                    </div> 
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<script src="{{url('/')}}/assets/vendor/js/jquery.min.js"></script>
<script src="{{url('/')}}/assets/vendor/js/frontend.js"></script>