<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TwilioService;
use App\Models\Orders;
use App\Models\Type;
use App\Models\Weight;
use App\Models\Flavor;
use App\Models\Payment;
use App\Models\User;
use App\Models\Otp;
use Auth;

class UserController extends Controller
{
    protected $twilioService;

    public function __construct(TwilioService $twilioService)
    {
        $this->twilioService = $twilioService;
    }
    
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
            $result[$key]['design_reference'] = $value['design_reference'];
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

    public function update_profile(Request $request) {
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['phone_no'] = $request->phone_no;
        $user = User::where(['id' => Auth::user()->id])->update($data);
        if($user) {
            return response()->json([
                'status' => 'success',
                'msg' => 'Profile updated'
            ]);
        } else {
            return response()->json([
                'status' => 'failed',
                'msg' => 'Profile could not be updated. Try again!'
            ]);
        }
    }

    public function send_otp(Request $request) {
        $recipient = '+91'.$request->phone_no;
        $otp = str_pad(random_int(0, pow(10, 6) - 1), 6, '0', STR_PAD_LEFT);
        $created_otp = Otp::create([
            'phone_no' => $recipient,
            'otp' => $otp,
            'varified' => false
        ]);
        if($created_otp) {
            $message = "Your One Time Password for creating the user account in CakeBox is ".$otp;
            $value = $this->twilioService->sendSms($recipient, $message);
            if($value) {
                return response()->json([
                    'status' => 'success',
                    'otp_id' => $created_otp->id
                ]);    
            } else {
                return response()->json([
                    'status' => 'failed'
                ]);
            }
        } else {
            return response()->json([
                'status' => 'failed'
            ]);
        }
    }

    public function validate_otp(Request $request) {
        $otp = $request->otp;
        $otp_id = $request->otp_id;
        $otp_det = Otp::where(['id' => $otp_id, 'otp' => $otp])->get();
        if(count($otp_det) > 0) {
            Otp::where(['id' => $otp_id])->update(['varified' => true]);
            return response()->json([
                'status' => 'success',
                'otp_id' => $otp_id
            ]);
        } else {
            return response()->json([
                'status' => 'failed',
                'otp_id' => $otp_id
            ]);
        }
    }
}
