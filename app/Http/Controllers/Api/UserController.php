<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TwilioService;
use App\Traits\ApiResponse;
use App\Notifications\NewOrderNotification;
use App\Models\Orders;
use App\Models\Type;
use App\Models\Weight;
use App\Models\Flavor;
use App\Models\Payment;
use App\Models\User;
use App\Models\Otp;
use App\Models\Feedback;
use App\Models\CanceledOrder;
use Auth;
use Carbon\Carbon;

class UserController extends Controller
{
    use ApiResponse;
    protected $twilioService;

    public function __construct(TwilioService $twilioService)
    {
        $this->twilioService = $twilioService;
    }
    
    public function get_my_orders(Request $request) {
        $result = [];
        $orders = Orders::whereIn('status',[1,2,3])->select('*')->orderBy('order_date','DESC')->get();
        foreach ($orders as $key => $value) {
            $cake_name = Type::where(['id'=>$value['cake_type']])->get()[0]['cake_type']; 
            $cake_weight = Weight::where(['id'=>$value['weight']])->get()[0]['cake_weight'];
            $cake_flavor = Flavor::where(['id'=>$value['flavor']])->get()[0]['flavor_name'];
            $payment_info = Payment::where(['order_id'=>$value['order_no']])->first();
            $feedback_info = Feedback::where(['order_id'=>$value['id']])->get();
            $result[$key]['order_date'] = $value['order_date'];
            $result[$key]['status'] = $value['status'];
            $result[$key]['cake_type'] = $cake_name;
            $result[$key]['cake_weight'] = $cake_weight;
            $result[$key]['cake_flavor'] = $cake_flavor;
            $result[$key]['delivery_date_time'] = $value['delivery_date_time'];
            $result[$key]['order_no'] = $value['order_no'];
            $result[$key]['design_reference'] = $value['design_reference'];
            $result[$key]['feedback'] = count($feedback_info) > 0 ? $feedback_info[0]['feedback'] : ""; 
            if($payment_info == null) {
                $result[$key]['total_amount'] = 0.00;
                $result[$key]['payment_id'] = 'Not Available';
                $result[$key]['payment_status'] = $payment_info['status'];
                $result[$key]['amount_to_be_paid'] = 0.00;
                $result[$key]['amount_paid'] = 0.00;
            } else {
                $result[$key]['total_amount'] = $payment_info['total_amount'];
                $result[$key]['payment_id'] = $payment_info['payment_id'];
                $result[$key]['payment_status'] = $payment_info['status'];
                $result[$key]['amount_to_be_paid'] = $payment_info['amount_to_be_paid'];
                $result[$key]['amount_paid'] = $payment_info['amount_paid'];
            }
        }
        return $this->success('Data found', 'success', $result);
    }

    public function update_profile(Request $request) {
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['phone_no'] = $request->phone_no;
        $user = User::where(['id' => Auth::user()->id])->update($data);
        if($user) {
            return $this->success('Profile updated', 'success');
        } else {
            return $this->error('Profile could not be updated', 'failed');
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
                return $this->success('OTP sent to phone no.', 'success', $created_otp->id);    
            } else {
                return $this->error('OTP could not be send', 'failed');
            }
        } else {
            return $this->error('OTP could not be created', 'failed');
        }
    }

    public function validate_otp(Request $request) {
        $otp = $request->otp;
        $otp_id = $request->otp_id;
        $otp_det = Otp::where(['id' => $otp_id, 'otp' => $otp])->get();
        if(count($otp_det) > 0) {
            Otp::where(['id' => $otp_id])->update(['varified' => true]);
            return $this->success('OTP is varified', 'success', $otp_id);
        } else {
            return $this->error('Invalid OTP', 'failed', $otp_id);
        }
    }

    public function submit_feedback(Request $request) {
        $order_no = $request->order_no;
        $feedback = $request->feedback;
        $rating = $request->rating;
        $order_id = Orders::where(['order_no' => $order_no])->get()[0]['id'];
        $feedback = Feedback::create([
            'order_id' => $order_id,
            'feedback' => $feedback,
            'rating' => $rating,
            'user_id' => Auth::user()->id
        ]);
        if($feedback) {
            return $this->success('Feedback submitted', 'success');
        } else {
            return $this->error('Feedback could not be saved', 'failed');
        }
    }

    public function cancel_order(Request $request) {
        $order_no = $request->order_no;
        $order_id = Orders::where(['order_no' => $order_no])->get()[0]['id'];
        $cancel = CanceledOrder::create([
            'order_id' => $order_id
        ]);
        if($cancel) {
            Orders::where(['order_no' => $order_no])->update(['status' => 4]);
            return $this->success('Order cancelled', 'success');
        } else {
            return $this->error('Order could not be cancelled', 'failed');
        }
    }

    public function get_order_info(Request $request) {
        $return_data = [];
        $del_datetime = $del_date = $del_time = '';
        $order_info = Orders::join('flavors', 'orders.flavor', '=', 'flavors.id')
                    ->join('weight', 'orders.weight', '=', 'weight.id')
                    ->join('types', 'orders.cake_type', '=', 'types.id')
                    ->where(['order_no' => $request->order_no])
                    ->select('orders.id as oid', 'orders.occassion', 'orders.delivery_date_time','orders.instruction','orders.design_reference','types.cake_type','types.id as typeid', 
                            'flavors.flavor_name', 'flavors.id as flavorid', 'weight.cake_weight', 'weight.id as weightid')
                    ->get();
        $del_datetime = explode(" ", $order_info[0]['delivery_date_time']);
        $del_date = $del_datetime[0];
        $del_time = $del_datetime[1];
        if($order_info) {
            $types = Type::all();
            $weights = Weight::all();
            $flavors = Flavor::all();
            $return_data['result'] = $order_info;
            $return_data['types'] = $types;
            $return_data['weights'] = $weights;
            $return_data['flavors'] = $flavors;
            $return_data['del_date'] = $del_date;
            $return_data['del_time'] = $del_time;
            return $this->success('Order data found','success', $return_data);
        } else {
            return $this->error('Order data not found','failed',);
        }
    }

    public function update_order(Request $request) {
        $o_id  = $request->o_id;
        $occassion  = $request->occassion;
        $cake_type  = $request->cake_type;
        $cake_flavor  = $request->cake_flavor;
        $cake_weight  = $request->cake_weight;
        $cake_instruction  = trim($request->cake_instruction);
        $delivery_datetime = Carbon::parse($request->cake_delivery_date . ' ' . $request->cake_delivery_time);
        $total_amount = 0.00;
        $fileUrl = true;
        $filePath = '';
        $cake_type_name = Type::where(['id'=>$cake_type])->get()[0]['cake_type'];
        $cake_weight_desc = Weight::where(['id'=>$cake_weight])->get()[0]['cake_weight'];
        if($cake_type_name == 'Chocolate') {
            if($cake_weight_desc == '250GM') {
                $total_amount = 300.00;    
            } else if($cake_weight_desc == '500GM') {
                $total_amount = 450.00;    
            } else if($cake_weight_desc == '1KG') {
                $total_amount = 900.00;    
            } else if($cake_weight_desc == '1.5KG') {
                $total_amount = 1350.00;    
            } else if($cake_weight_desc == '2KG') {
                $total_amount = 1750.00;    
            }
        } else {
            if($cake_weight_desc == '250GM') {
                $total_amount = 250.00;    
            } else if($cake_weight_desc == '500GM') {
                $total_amount = 400.00;    
            } else if($cake_weight_desc == '1KG') {
                $total_amount = 800.00;    
            } else if($cake_weight_desc == '1.5KG') {
                $total_amount = 1150.00;    
            } else if($cake_weight_desc == '2KG') {
                $total_amount = 1500.00;    
            }    
        }
        if($request->hasFile('image')) {
            $request->validate([
                'image' => 'file|mimes:jpg,jpeg,png,svg|max:2048',
            ]);
            $file = $request->file('image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploaded_reference_images', $fileName, 'public');
            $fileUrl = Storage::url($filePath);
        }
        if($fileUrl) {
            $order = [
                'occassion' => $occassion,
                'cake_type' => $cake_type,
                'flavor' => $cake_flavor,
                'weight' => $cake_weight,
                'delivery_date_time' => $delivery_datetime,
                'instruction' => $cake_instruction == null || $cake_instruction == "" ? "Not Available" : $cake_instruction,
                'design_reference' => $filePath,
            ];
            $check_order = Orders::where(['id' => $o_id])->get();
            if(count($check_order) > 0) {
                $return_amount = 0.00;
                $updated_order = Orders::where(['id' => $o_id])->update($order);
                if($updated_order) {
                    $payment_info = Payment::where(['order_id' => $check_order[0]['order_no']])->first();
                    if($payment_info['amount_paid'] == 0) {
                        $amount_to_be_paid = $total_amount;
                    } else {
                        if($payment_info['amount_paid'] < $total_amount) {
                            $amount_to_be_paid = $total_amount - $payment_info['amount_paid'];
                            $return_amount = 0.00;    
                        } else if($payment_info['amount_paid'] > $total_amount) {
                            $amount_to_be_paid = 0.00;
                            $return_amount = $payment_info['amount_paid'] - $total_amount;    
                        } else if($payment_info['amount_paid'] == $total_amount) {
                            $amount_to_be_paid = 0.00;
                            $return_amount = 0.00;    
                        }
                    }
                    $payment = [
                        'amount_to_be_paid' => $amount_to_be_paid,
                        'total_amount' => $total_amount,
                        'return_amount' => $return_amount
                    ];
                    Payment::where(['order_id' => $check_order[0]['order_no']])->update($payment);  
                }
                $adminUsers = User::where('isAdmin', true)->get();
                $message = 'Order No. '.$check_order[0]['order_no']. 'has been updated. Please check!';
                foreach ($adminUsers as $admin) {
                    $admin->notify(new NewOrderNotification());
                }
                return $this->success('Order is updated','success');
            }
        } else {
            return $this->error('Uploaded file could not be saved','failed');
        }
    }
}
