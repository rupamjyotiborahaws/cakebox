@extends('layout.app')

<link href="{{url('/')}}/assets/vendor/css/frontend.css" rel="stylesheet" />
@section('content')
<div class="row">
    <div class="col-md-12 col-lg-12 col-xs-12 nav-div">
        @extends('frontend.navbar')
    </div>
    <div class="col-md-12 col-lg-12 col-xs-12">
        <div class="d-flex justify-content-center align-items-center full-height register-div">
            <div class="card p-4 shadow-lg" style="width: 35rem;">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 text-center">
                            The price list of the cakes are available below
                        </div>
                        <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                            <h4>Chocolate Cake</h4>
                            <ul>
                                <li>250 GM : Rs. 300</li>
                                <li>500 GM : Rs. 450</li>
                                <li>1 KG : Rs. 900</li>
                                <li>1.5 KG : Rs. 1350</li>
                                <li>2 KG : Rs. 1750</li>
                            </ul>
                            <h4>Vanilla Cake</h4>
                            <ul>
                                <li>250 GM : Rs. 250</li>
                                <li>500 GM : Rs. 400</li>
                                <li>1 KG : Rs. 800</li>
                                <li>1.5 KG : Rs. 1150</li>
                                <li>2 KG : Rs. 1500</li>
                            </ul>
                        </div>
                        <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 text-center">
                            <a class="btn btn-primary" href="{{route('order')}}">Order Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<script src="{{url('/')}}/assets/vendor/js/jquery.min.js"></script>
<script src="{{url('/')}}/assets/vendor/js/frontend.js"></script>