@extends('layout.admin')

@section('content')
<link href="{{url('/')}}/assets/vendor/css/admin.css" rel="stylesheet" />
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
        @extends('admin.admin-nav')
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
        <div class="d-flex justify-content-center align-items-center full-height">
            <div class="card p-2 shadow-lg orders-div">
                <div class="card-body">
                    @if(count($order_data) > 0)
                        <div class="table-responsive orders-data">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width:20%;">Sl. No.</th>
                                        <th style="width:40%;">Order #</th>
                                        <th style="width:40%;">Del. Date</th>
                                    </tr>
                                </thead>
                                <tbody class="orders-tbody">
                                    <?php $slno = 1; ?>
                                    @foreach($order_data as $order)
                                        <tr class="see_detail" data-id="{{$slno}}" id="{{$slno}}">
                                            <td style="width:20%;">{{$slno}}</td>
                                            <td style="width:40%;">{{$order['order_no']}}</td>
                                            <td style="width:40%;">{{$order['delivery_date_time']}}</td>
                                        </tr>
                                        <tr class="show_detail d-none" id="order_{{$slno}}">
                                            <td style="width:100%;">
                                                <p style="width:auto;">Cake : {{$order['cakeType']}}</p>
                                                <p style="width:auto;">Flavor : {{$order['flavorName']}}</p>
                                                <p style="width:auto;">Weight: {{$order['cakeWeight']}}</p>
                                                <p style="width:auto;">Instruction (if any): {{$order['instruction']}}</p>
                                                <p style="width:auto;">Total amount : {{$order['total_amount']}}</p>
                                                <p style="width:auto;">Amount paid : {{$order['amount_paid']}}</p>
                                                @if($status_id == 1)
                                                    <button class="btn btn-warning process-order" data-id="{{$slno}}" data-ord_no="{{$order['order_no']}}" style="width:150px;">Process Order</button><br />
                                                @elseif($status_id == 2)
                                                    <button class="btn btn-success deliver-order" data-id="{{$slno}}" data-ord_no="{{$order['order_no']}}" style="width:150px;">Deliver Order</button><br />
                                                @elseif($status_id == 3)
                                                    <button class="btn btn-primary view-feedback" data-id="{{$slno}}" data-ord_no="{{$order['order_no']}}" style="width:150px;">View Customer Feedback</button><br />
                                                @endif                                            
                                                <p style="width:300px; color:#098909; font-size:16px; fon-weight:600; margin-top:10px;" id="message_{{$slno}}"></p>
                                            </td>
                                        </tr>
                                        <?php $slno++; ?>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div><h4>No orders found</h4></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
<script src="{{url('/')}}/assets/vendor/js/jquery.min.js"></script>
<script src="{{url('/')}}/assets/vendor/js/admin_order_details.js"></script>
