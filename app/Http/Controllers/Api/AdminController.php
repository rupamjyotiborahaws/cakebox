<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\Orders;
use App\Models\OrderStatus;

class AdminController extends Controller
{
    public function get_orders_for_admin_dashboard(Request $request) {
        $all_status = [];
        $order_count = [];
        $orders = Orders::select('status', DB::raw('COUNT(id) as total_orders'))->groupBy('status')->get();
        $order_status_det = OrderStatus::select('id','order_status')->get();
        foreach ($orders as $key1 => $value1) {
            $order_count[$value1['status']] = $value1['total_orders'];
        }
        return response()->json([
            'all_status' => $order_status_det,
            'order_count' => $order_count,
            'status' => 'success'
        ]);  
    }

    public function process_order(Request $request) {
        $ord_no = $request->order_no;
        $order_processed = Orders::where(['order_no' => $ord_no])->update(['status' => 2]);
        if($order_processed) {
            return response()->json([
                'status' => 'success',
                'message' => 'Status Updated to order processing'
            ]);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Could not update the order status'
            ]);    
        }
    }

    public function deliver_order(Request $request) {
        $ord_no = $request->order_no;
        $order_delivered = Orders::where(['order_no' => $ord_no])->update(['status' => 3]);
        if($order_delivered) {
            return response()->json([
                'status' => 'success',
                'message' => 'Order Delivered'
            ]);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Could not deliver the order'
            ]);    
        }
    }
}
