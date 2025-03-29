@extends('layout.app')

@section('content')
<div class="row">
    <div class="col-md-12 col-lg-12 col-xs-12 nav-div">
        @extends('frontend.navbar')
    </div>
    <!-- justify-content-center align-items-center -->
    <div class="col-md-2 col-lg-2">
        
    </div>
    <div class="col-md-10 col-lg-10 col-xs-12 col-sm-12">
        <div class="d-flex full-height register-div">
            <div class="card text-center p-4 shadow-lg" style="width: 24rem;">
                <div class="card-body">
                    <h5 class="card-title">Dashboard</h5> 
                </div>
            </div>
        </div>
    </div>
</div>
@endsection