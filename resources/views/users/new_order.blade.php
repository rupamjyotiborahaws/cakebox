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
                            <h5 class="card-title text-center">Place a new order</h5>
                            <p class="form-instruction">Fields marked with * are mandatory</p>                            
                            <form name="place-order" action="{{route('place_order')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                                <div class="form-group order-form">
                                    <label for="occassion">Occassion <label style="color:red;">*</label></label>
                                    <select class="form-control" id="occassion" name="occassion">
                                        <option value="Birthday">Birthday</option>
                                        <option value="Engagement">Engagement</option>
                                        <option value="Wedding">Wedding</option>
                                        <option value="Wedding Anniversary">Wedding Anniversary</option>
                                        <option value="New store/office/institute/organization opening">New store/office/institute/organization opening</option>
                                    </select>
                                </div>
                                <div class="form-group order-form">
                                    <label for="cake_type">Cake Type <label style="color:red;">*</label></label>
                                    <select class="form-control" id="cake_type" name="cake_type">
                                    <option value="0">---Select---</option>
                                    @foreach($types as $type)
                                        <option value="{{$type['id']}}">{{$type['cake_type']}}</option>
                                    @endforeach
                                    </select>
                                </div>
                                <div class="form-group order-form">
                                    <label for="cake_flavor">Flavor <label style="color:red;">*</label></label>
                                    <select class="form-control" id="cake_flavor" name="cake_flavor">
                                    <option value="0">---Select---</option>
                                    @foreach($flavors as $flavor)
                                        <option value="{{$flavor['id']}}">{{$flavor['flavor_name']}}</option>
                                    @endforeach
                                    </select>
                                </div>
                                <div class="form-group order-form">
                                    <label for="cake_weight">Weight <label style="color:red;">*</label></label>
                                    <select class="form-control" id="cake_weight" name="cake_weight">
                                    <option value="0">---Select---</option>
                                    @foreach($weights as $weight)
                                        <option value="{{$weight['id']}}">{{$weight['cake_weight']}}</option>
                                    @endforeach
                                    </select>
                                </div>
                                <div class="form-group order-form">
                                    <label for="cake_instruction">Special instruction</label>
                                    <textarea class="form-control" id="cake_instruction" name="cake_instruction" rows="3" placeholder="Write here if you have any special instruction for us"></textarea>
                                </div>
                                <div class="form-group order-form">
                                    <label for="cake_delivery_date">Delivery Date <label style="color:red;">*</label></label>
                                    <input type="date" class="form-control" id="cake_delivery_date" name="cake_delivery_date" />
                                </div>
                                <div class="form-group order-form">
                                    <label for="cake_delivery_time">Delivery Time <label style="color:red;">*</label></label>
                                    <input type="time" class="form-control" id="cake_delivery_time" name="cake_delivery_time" />
                                </div>
                                <div class="form-group order-form">
                                    <label for="cake_reference_photo">Upload your design</label>
                                    <input type="file" class="form-control" id="cake_reference_photo" name="image" />
                                </div>
                                <button type="submit" class="btn btn-primary place-order">Place Order</button>
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
<script src="{{url('/')}}/assets/vendor/js/orderhandler.js"></script>