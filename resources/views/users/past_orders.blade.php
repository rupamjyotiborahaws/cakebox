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
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show alert-msg-box" role="alert">
                                    <p class="error-msg">{{ session('error') }}</p>
                                    <button type="button" class="btn-close close-msg" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show alert-msg-box" role="alert">
                                    <p class="success-msg">{!! session('success') !!}</p>
                                    <button type="button" class="btn-close close-msg" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            <h5 class="card-title text-center">Your orders</h5>
                            <div class="table-responsive orders-data">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sl. No.</th>
                                            <th>Order Date</th>
                                            <th>Status</th>
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
            <div class="col-md-3 col-lg-3">

            </div>
        </div>
    </div>
</div>
@endsection
<script src="{{url('/')}}/assets/vendor/js/jquery.min.js"></script>
<script src="{{url('/')}}/assets/vendor/js/orderhandler.js"></script>