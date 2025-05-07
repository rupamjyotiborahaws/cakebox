<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use DB;
use App\Models\Orders;
use App\Models\OrderStatus;
use App\Models\Feedback;

class AdminController extends Controller
{
    use ApiResponse;
    
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
            return $this->success('Status Updated to order processing', 'success');
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Could not update the order status'
            ]);
            return $this->error('Could not update the order status', 'failed');    
        }
    }

    public function deliver_order(Request $request) {
        $ord_no = $request->order_no;
        $order_delivered = Orders::where(['order_no' => $ord_no])->update(['status' => 3]);
        if($order_delivered) {
            return $this->success('Order Delivered', 'success');
        } else {
            return $this->error('Could not deliver the order', 'failed');    
        }
    }

    public function get_order_feedback(Request $request) {
        $ord_no = $request->order_no;
        $order_id = Orders::where(['order_no' => $ord_no])->get()[0]['id'];
        $feedback = Feedback::where(['order_id' => $order_id])->get();
        if(count($feedback) > 0) {
            $result = json_encode([
                'feedback' => $feedback[0]['feedback'],
                'rating' => $feedback[0]['rating']
            ]);
            return $this->success('Feedback found','success',$result);
        } else {
            return $this->error('No feedback & rating found','failed');    
        }
    }
}
