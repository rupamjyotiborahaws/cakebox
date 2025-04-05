@extends('layout.app')

@section('content')
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
<div class="row">
    <div class="col-md-12 col-lg-12 col-xs-12 nav-div">
        @extends('frontend.navbar')
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
        <div class="d-flex justify-content-center align-items-center full-height">
            <div class="card p-2 shadow-lg order-div">
                <div class="card-body">
                    <h5 class="card-title text-center">Your orders</h5>
                    <div class="table-responsive orders-data">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width:20%;">Sl. No.</th>
                                    <th style="width:40%;">Order Date</th>
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
</div>
    
</div>
@endsection
<script src="{{url('/')}}/assets/vendor/js/jquery.min.js"></script>
<script src="{{url('/')}}/assets/vendor/js/orderhandler.js"></script>