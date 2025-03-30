<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Orders;
use App\Models\Type;
use App\Models\Weight;
use App\Models\Flavor;
use App\Models\Payment;

class UserController extends Controller
{
    public function get_my_orders(Request $request) {
        $result = [];
        $orders = Orders::select('*')->orderBy('order_date','DESC')->get();
        foreach ($orders as $key => $value) {
            $cake_name = Type::where(['id'=>$value['cake_type']])->get()[0]['cake_type']; 
            $cake_weight = Weight::where(['id'=>$value['weight']])->get()[0]['cake_weight'];
            $cake_flavor = Flavor::where(['id'=>$value['flavor']])->get()[0]['flavor_name'];
            $payment_info = Payment::where(['order_id'=>$value['order_no']])->first();
            $result[$key]['order_date'] = $value['order_date'];
            $result[$key]['status'] = $value['status'];
            $result[$key]['cake_type'] = $cake_name;
            $result[$key]['cake_weight'] = $cake_weight;
            $result[$key]['cake_flavor'] = $cake_flavor;
            $result[$key]['delivery_date_time'] = $value['delivery_date_time'];
            if($payment_info == null) {
                $result[$key]['total_amount'] = 0.00;
                $result[$key]['amount_to_be_paid'] = 0.00;
                $result[$key]['amount_paid'] = 0.00;
                $result[$key]['payment_id'] = 'Not Available';
            } else {
                $result[$key]['total_amount'] = $payment_info['total_amount'];
                $result[$key]['amount_to_be_paid'] = $payment_info['amount_to_be_paid'];
                $result[$key]['amount_paid'] = $payment_info['amount_paid'];
                $result[$key]['payment_id'] = $payment_info['payment_id'];
            }
        }
        return response()->json([
            'status' => 'success',
            'data' => $result
        ]);
    }
}
