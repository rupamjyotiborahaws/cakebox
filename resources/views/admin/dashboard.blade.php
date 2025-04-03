@extends('layout.admin')

@section('content')
<div class="row">
    <div class="col-md-12 col-lg-12 col-xs-12 nav-div">
        @extends('frontend.navbar')
    </div>
    <!-- justify-content-center align-items-center -->    
    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
        <div class="d-flex full-height justify-content-center align-items-center register-div">
            <div class="card text-center p-4 shadow-lg" style="width: 24rem;">
                <div class="card-body">
                    Welcome to CakeBox          
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Button to Open Modal -->
<!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal">
    Open Modal
</button> -->

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