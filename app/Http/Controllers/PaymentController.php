<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function createOrder(Request $request)
    {
        $amount = $request->amount;
        $order_no = $request->order_no;
        $payment = Payment::where(['order_id' => $order_no])->get()[0];
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        $orderData = [
            'receipt'         => 'rcptid_' . rand(1000, 9999),
            'amount'          => $amount * 100,
            'currency'        => 'INR',
            'payment_capture' => 1
        ];
        $razorpayOrder = $api->order->create($orderData);
        $payment->payment_order_id = $razorpayOrder['id'];
        $payment->save();
        return response()->json([
            'order_id' => $razorpayOrder['id']
        ]);
    }

    public function paymentSuccess(Request $request)
    {
        $payment = Payment::where(['order_id'=>$request->order_no,'payment_order_id'=>$request->payment_order_id])->first();
        if($payment) {
            $payment->status = "paid";
            $payment->payment_id = $request->payment_id;
            $payment->amount_paid = $payment->total_amount;
            $payment->amount_to_be_paid = 0.00;
            $payment->save();
            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['status' => 'failed']);
        }
    }
}

