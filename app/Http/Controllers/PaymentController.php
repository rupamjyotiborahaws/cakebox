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
        try {
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
                'status' => 'success',
                'order_id' => $razorpayOrder['id']
            ]);
        } catch (\Razorpay\Api\Errors\Base $e) {
            \Log::error('Razorpay Order Creation Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Order could not be created. Please try again.'
            ], 500);
        }
    }

    public function paymentSuccess(Request $request)
    {
        $request->validate([
            'payment_order_id' => 'required|string',
            'payment_id' => 'required|string',
            'signature' => 'required|string',
        ]);
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        try {
            $attributes = [
                'razorpay_order_id' => $request->payment_order_id,
                'razorpay_payment_id' => $request->payment_id,
                'razorpay_signature' => $request->signature
            ];
            $api->utility->verifyPaymentSignature($attributes);
            \Log::info('Razorpay payment verified', $attributes);
            $payment = Payment::where(['order_id'=>$request->order_no,'payment_order_id'=>$request->payment_order_id])->first();
            if($payment) {
                $payment->status = "paid";
                $payment->payment_id = $request->payment_id;
                $payment->amount_paid = $payment->total_amount;
                $payment->amount_to_be_paid = 0.00;
                $payment->save();
                return response()->json(['status' => 'success']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Payment failed! Order No. does not exists.'], 400);
            }
        } catch (\Razorpay\Api\Errors\SignatureVerificationError $e) {
            \Log::error('Razorpay Signature Verification Failed: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Payment verification failed.'], 400);
        } catch (\Exception $e) {
            \Log::error('Razorpay Unknown Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Payment failed! Something went wrong.'], 500);
        }
    }

    public function checkOrderStatus(Request $request)
    {
        $order = Payment::where('payment_order_id', $request->query('order_id'))->first();
        return response()->json(['status' => $order->status]);
    }

    public function webhookHandler(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature');
        $webhookSecret = env('RAZORPAY_WEBHOOK_SECRET');
        if(hash_hmac('sha256', $payload, $webhookSecret) !== $signature) {
            return response()->json(['message' => 'Invalid signature'], 400);
        }
        $event = $request->input('event');
        $payment = $request->input('payload.payment.entity');
        if($event === 'payment.captured') {
            $payment_info = Payment::where('payment_order_id', $payment['order_id'])->first();
            if($payment_info) {
                $payment_info->update([
                    'status' => 'paid',
                    'payment_id' => $payment['id']
                ]);
            }
            return response()->json(['status' => 'success', 'message' => 'Payment completed through QR Code']);
        }
        return response()->json(['message' => 'Webhook processed']);
    }
}

