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
                    <div class="row">
                        <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                            The cakes we make <br /> <strong>Chocolate & Vannila</strong><br />
                            We take orders for following occassions <br />
                            Birthday<br />Wedding<br />Wedding Anniversary<br />Engagement<br />Opening of new store/office/institution/organization
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