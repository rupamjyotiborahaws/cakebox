<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Orders;

class AdminController extends Controller
{
    public function dashboard(Request $request) {
        return view('admin.dashboard');
    }

    public function order_details_by_status(Request $request, $status_id) {
        $status_id = $request->status_id;
        $order_data = [];
        //$order_det = Orders::join('types', 'orders.cake_type', '=', 'types.id')
        //->join('flavors', 'orders.flavor', '=', 'flavors.id')
        //->join('weight', 'orders.weight', '=', 'weight.id')
        $order_det = Orders::join('payments', 'orders.order_no', '=', 'payments.order_id')
        ->where(['orders.status' => $status_id])
        ->select('orders.occassion', 'orders.order_date', 'orders.delivery_date_time','orders.instruction',
        'orders.design_reference','orders.order_no','orders.cake_type as cakeType', 'orders.flavor as flavorName', 
        'orders.weight as cakeWeight','payments.total_amount','payments.amount_paid','payments.payment_order_id','payments.payment_id')
        ->get();

        foreach ($order_det as $key => $order_value) {
            $order_data[$key]['occassion'] = $order_value['occassion'];
            $order_data[$key]['order_date'] = date("l, F j, Y g:i A", strtotime($order_value['order_date']));
            $order_data[$key]['delivery_date_time'] = date("l, F j, Y g:i A", strtotime($order_value['delivery_date_time']));
            $order_data[$key]['instruction'] = $order_value['instruction'];
            $order_data[$key]['design_reference'] = $order_value['design_reference'];
            $order_data[$key]['cakeType'] = $order_value['cakeType'];
            $order_data[$key]['flavorName'] = $order_value['flavorName'];
            $order_data[$key]['cakeWeight'] = $order_value['cakeWeight'];
            $order_data[$key]['order_no'] = $order_value['order_no'];  
            $order_data[$key]['total_amount'] = $order_value['total_amount'];
            $order_data[$key]['amount_paid'] = $order_value['amount_paid'];
            $order_data[$key]['payment_order_id'] = $order_value['payment_order_id']; 
            $order_data[$key]['payment_id'] = $order_value['payment_id'];  
        }
        return view('admin.order_details',compact('order_data','status_id'));
    }

}
