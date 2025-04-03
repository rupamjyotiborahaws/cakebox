@extends('layout.app')

<link href="{{url('/')}}/assets/vendor/css/frontend.css" rel="stylesheet" />
@section('content')
<div class="row">
    <div class="col-md-12 col-lg-12 col-xs-12 nav-div">
        @extends('frontend.navbar')
    </div>
    
    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
        <div class="row">
            <div class="col-md-3 col-lg-3">

            </div>
            <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6">
                <div class="d-flex full-height">
                    <div class="card p-4 shadow-lg order-div">
                        <div class="card-body">
                            <div class="alert alert-danger alert-dismissible fade show alert-msg-box d-none" role="alert">
                                <p class="error-msg"></p>
                                <button type="button" class="btn-close close-msg" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        
                            <div class="alert alert-success alert-dismissible fade show alert-msg-box d-none" role="alert">
                                <p class="success-msg"></p>
                                <button type="button" class="btn-close close-msg" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <form id="myForm">
                                <h5 class="card-title text-center">Your Profile</h5>
                                <div class="form-group order-form">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control user-form-data" id="name" name="name" value="{{$user['name']}}" disabled/>
                                </div>
                                <div class="form-group order-form">
                                    <label for="email">Email ID</label>
                                    <input type="email" class="form-control user-form-data" id="email" name="email" value="{{$user['email']}}" disabled/>
                                </div>
                                <div class="form-group order-form">
                                    <label for="phone_no">Phone No.</label>
                                    <input type="text" class="form-control user-form-data" id="phone_no" name="phone_no" value="{{$user['phone_no']}}" disabled />
                                </div>
                                <button type="submit" class="btn btn-primary profile-update">Update Profile</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-lg-3">

            </div>
        </div>
    </div>
</div>
@endsection
<script src="{{url('/')}}/assets/vendor/js/jquery.min.js"></script>
<script src="{{url('/')}}/assets/vendor/js/users.js"></script>