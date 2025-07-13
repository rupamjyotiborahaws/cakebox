<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
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
        $orders = Orders::where(['user_id' => Auth::user()->id])->whereIn('status',[1,2,3])->select('*')->orderBy('order_date','DESC')->get();
        foreach ($orders as $key => $value) {
            //$cake_name = Type::where(['id'=>$value['cake_type']])->get()[0]['cake_type']; 
            //$cake_weight = Weight::where(['id'=>$value['weight']])->get()[0]['cake_weight'];
            //$cake_flavor = Flavor::where(['id'=>$value['flavor']])->get()[0]['flavor_name'];
            $payment_info = Payment::where(['order_id'=>$value['order_no']])->first();
            $feedback_info = Feedback::where(['order_id'=>$value['id']])->get();
            $result[$key]['order_date'] = $value['order_date'];
            $result[$key]['status'] = $value['status'];
            $result[$key]['cake_type'] = $value['cake_type'];
            $result[$key]['cake_weight'] = $value['weight'];
            $result[$key]['cake_flavor'] = $value['flavor'];
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
        $order_info = Orders::where(['order_no' => $request->order_no])
                    ->select('orders.id as oid', 'orders.occassion', 'orders.delivery_date_time','orders.instruction','orders.design_reference','orders.cake_type', 
                            'orders.flavor', 'orders.weight')->get();
        $del_datetime = explode(" ", $order_info[0]['delivery_date_time']);
        $del_date = $del_datetime[0];
        $del_time = $del_datetime[1];
        if($order_info) {
            $return_data['result'] = $order_info;
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
        $error = '';
        $caketypes = ['Chocolate','Vanilla'];
        $cakeSelected = '';
        $found = false;
        foreach($caketypes as $caketype) {
            if(stripos($cake_type, $caketype) !== false) {
                $found = true;
                $cakeSelected = $caketype;
                break;
            }
        }
        if($found) {
            if(preg_match('/\b' . preg_quote($cakeSelected, '/') . '\b/i', $cake_type, $matches)) {
                $cake_type = strtolower($matches[0]);
            }
        } else {
            if($error == '') {
                $error = 'Please provide a valid cake (Chocolate or Vanilla)';
            } else {
                $error .= ', cake (Chocolate or Vanilla)';
            }
        }
        if (preg_match('/\d+\s*(gm|kg|GM|KG|Gm|Kg)\b/i', $cake_weight, $matches)) {
            $cake_weight = strtoupper($cake_weight);
        } 
        else {
            if($error == '') {
                $error = 'Please provide a valid cake weight. For example 500 gm, 1 kg etc.';
            } else {
                $error .= ', valid cake weight. For example 500 gm, 1 kg etc.';
            }
        }
        if($error != "") {
            return redirect()->back()->with(['error' => $error])->withInput();
        }
        if($cake_type == 'chocolate') {
            if($cake_weight == '250GM' || $cake_weight == '250 GM') {
                $total_amount = 300.00;    
            } else if($cake_weight == '500GM' || $cake_weight == '500 GM') {
                $total_amount = 450.00;    
            } else if($cake_weight == '1KG' || $cake_weight == '1 KG') {
                $total_amount = 900.00;    
            } else if($cake_weight == '1.5KG' || $cake_weight == '1.5 KG') {
                $total_amount = 1350.00;    
            } else if($cake_weight == '2KG' || $cake_weight == '2 KG') {
                $total_amount = 1750.00;    
            } else if($cake_weight == '2.5KG' || $cake_weight == '2.5 KG') {
                $total_amount = 2000.00;    
            } else if($cake_weight == '3KG' || $cake_weight == '3 KG') {
                $total_amount = 2400.00;    
            } else if($cake_weight == '3.5KG' || $cake_weight == '3.5 KG') {
                $total_amount = 2800.00;    
            } else if($cake_weight == '4KG' || $cake_weight == '4 KG') {
                $total_amount = 3500.00;    
            } else if($cake_weight == '4.5KG' || $cake_weight == '4.5 KG') {
                $total_amount = 4000.00;    
            } else if($cake_weight == '5KG' || $cake_weight == '5 KG') {
                $total_amount = 4500.00;    
            }
        } else {
            if($cake_weight == '250GM' || $cake_weight == '250 GM') {
                $total_amount = 250.00;    
            } else if($cake_weight == '500GM' || $cake_weight == '500 GM') {
                $total_amount = 400.00;    
            } else if($cake_weight == '1KG' || $cake_weight == '1 KG') {
                $total_amount = 800.00;    
            } else if($cake_weight == '1.5KG' || $cake_weight == '1.5 KG') {
                $total_amount = 1150.00;    
            } else if($cake_weight == '2KG' || $cake_weight == '2 KG') {
                $total_amount = 1500.00;    
            } else if($cake_weight == '2.5KG' || $cake_weight == '2.5 KG') {
                $total_amount = 1800.00;    
            } else if($cake_weight == '3KG' || $cake_weight == '3 KG') {
                $total_amount = 2200.00;    
            } else if($cake_weight == '3.5KG' || $cake_weight == '3.5 KG') {
                $total_amount = 2600.00;    
            } else if($cake_weight == '4KG' || $cake_weight == '4 KG') {
                $total_amount = 3000.00;    
            } else if($cake_weight == '4.5KG' || $cake_weight == '4.5 KG') {
                $total_amount = 3300.00;    
            } else if($cake_weight == '5KG' || $cake_weight == '5 KG') {
                $total_amount = 3600.00;    
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
                'occassion' => strtoupper($occassion),
                'cake_type' => strtoupper($cake_type),
                'flavor' => $cake_flavor == null ? 'Not Available' : strtoupper($cake_flavor),
                'weight' => strtoupper($cake_weight),
                'delivery_date_time' => $delivery_datetime,
                'instruction' => $cake_instruction == null || $cake_instruction == "" ? "Not Available" : strtoupper($cake_instruction),
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

    public function voice_order(Request $request)
    {
        if(!$request->hasFile('audio')) {
            return response()->json(['error' => 'No audio file uploaded'], 400);
        }
        $file = $request->file('audio');
        $tempPath = $file->storeAs('temp', uniqid() . '.webm');
        $filePath = storage_path("app/{$tempPath}");
        // Send to Whisper API
        $response = Http::withToken(env('OPENAI_API_KEY'))->attach('file', fopen($filePath, 'r'), 'voice.webm')->post('https://api.openai.com/v1/audio/transcriptions',[
                'model' => 'whisper-1',
                'response_format' => 'json',
                'language' => 'en',
        ]);
        // Clean up
        Storage::delete($tempPath);
        if($response->successful()) {
            $transcribedText = $response->json()['text'];
            $error = "";
            // Step 2: Parse using GPT
            $prompt = <<<PROMPT
            Convert the following cake order into JSON format with keys:
            - occassion
            - cake    
            - flavor
            - weight
            - message_on_cake
            - delivery_date_time (in ISO format)
            - quantity
            User said: "{$transcribedText}"
            If something is missing, leave it blank.
            PROMPT;
            $gptResponse = Http::withToken(env('OPENAI_API_KEY'))->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a cake order extraction assistant.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);
            if(!$gptResponse->successful()) {
                return response()->json(['error' => 'Could not understand your order. Kindly specify Occassion, Cake, Flavor, Weight, Delivery Date and Time, Special message on the cake'], 500);
            }
            $orderData = json_decode($gptResponse->json()['choices'][0]['message']['content'], true);

            if($orderData['occassion'] == "") {
                if($error == "") {
                    $error = "Tell me the Occassion";
                } else {
                    $error .= ", Occassion";    
                }
            }
            if($orderData['cake'] == "") {
                if($error == "") {
                    $error = "Tell me the cake (Chocolate/Vanilla)";
                } else {
                    $error .= ", Cake (Chocolate/Vanilla)";    
                }
            }
            if($orderData['weight'] == "") {
                if($error == "") {
                    $error = "Tell me size of the cake you need (250gm/500gm/1kg etc. (Max 5kg))";
                } else {
                    $error .= ", Size of the cake you need (250gm/500gm/1kg etc. (Max 5kg))";    
                }
            }
            if($orderData['delivery_date_time'] == "") {
                if($error == "") {
                    $error = "Tell me when you need this to be delivered (Date & Time)";
                } else {
                    $error .= ", Delivery date & time";    
                }
            }

            if($error != "") {
                return response()->json([
                    'status' => 'error',
                    'message' => $error
                ]);
            } else {
                $total_amount = 1.00;
                $error = '';
                $caketypes = ['Chocolate','Vanilla'];
                $cakeSelected = '';
                $found = false;
                foreach($caketypes as $caketype) {
                    if(stripos($orderData['cake'], $caketype) !== false) {
                        $found = true;
                        $cakeSelected = $caketype;
                        break;
                    }
                }
                if($found) {
                    if(preg_match('/\b' . preg_quote($cakeSelected, '/') . '\b/i', $orderData['cake'], $matches)) {
                        $orderData['cake'] = strtolower($matches[0]);
                    }
                } else {
                    if($error == '') {
                        $error = 'Tell me which cake (Chocolate or Vanilla)';
                    } else {
                        $error .= '& which cake (Chocolate or Vanilla)';
                    }
                }
                if (preg_match('/\d+\s*(gm|kg|GM|KG|Gm|Kg|gram|kilogram|Gram|Kilogram|GRAM|KILOGRAM)\b/i', $orderData['weight'], $matches)) {
                    $orderData['weight'] = strtoupper($orderData['weight']);
                } 
                else {
                    if($error == '') {
                        $error = 'Tell a valid cake weight (250gm/500gm/1kg/1.5kg/2kg/2.5kg/3kg/3.5kg/4kg/4.5kg/5kg (Max 5kg))';
                    } else {
                        $error .= '& valid cake weight (250gm/500gm/1kg/1.5kg/2kg/2.5kg/3kg/3.5kg/4kg/4.5kg/5kg (Max 5kg))';
                    }
                }
                if($error != "") {
                    return response()->json([
                        'status' => 'error',
                        'message' => $error
                    ]);
                }
                if($orderData['cake'] == 'chocolate') {
                    if($orderData['weight'] == '250GM' || $orderData['weight'] == '250 GM') {
                        $total_amount = 300.00;    
                    } else if($orderData['weight'] == '500GM' || $orderData['weight'] == '500 GM') {
                        $total_amount = 450.00;    
                    } else if($orderData['weight'] == '1KG' || $orderData['weight'] == '1 KG') {
                        $total_amount = 900.00;    
                    } else if($orderData['weight'] == '1.5KG' || $orderData['weight'] == '1.5 KG') {
                        $total_amount = 1350.00;    
                    } else if($orderData['weight'] == '2KG' || $orderData['weight'] == '2 KG') {
                        $total_amount = 1750.00;    
                    } else if($orderData['weight'] == '2.5KG' || $orderData['weight'] == '2.5 KG') {
                        $total_amount = 2000.00;    
                    } else if($orderData['weight'] == '3KG' || $orderData['weight'] == '3 KG') {
                        $total_amount = 2400.00;    
                    } else if($orderData['weight'] == '3.5KG' || $orderData['weight'] == '3.5 KG') {
                        $total_amount = 2800.00;    
                    } else if($orderData['weight'] == '4KG' || $orderData['weight'] == '4 KG') {
                        $total_amount = 3500.00;    
                    } else if($orderData['weight'] == '4.5KG' || $orderData['weight'] == '4.5 KG') {
                        $total_amount = 4000.00;    
                    } else if($orderData['weight'] == '5KG' || $orderData['weight'] == '5 KG') {
                        $total_amount = 4500.00;    
                    }
                } else {
                    if($orderData['weight'] == '250GM' || $orderData['weight'] == '250 GM') {
                        $total_amount = 250.00;    
                    } else if($orderData['weight'] == '500GM' || $orderData['weight'] == '500 GM') {
                        $total_amount = 400.00;    
                    } else if($orderData['weight'] == '1KG' || $orderData['weight'] == '1 KG') {
                        $total_amount = 800.00;    
                    } else if($orderData['weight'] == '1.5KG' || $orderData['weight'] == '1.5 KG') {
                        $total_amount = 1150.00;    
                    } else if($orderData['weight'] == '2KG' || $orderData['weight'] == '2 KG') {
                        $total_amount = 1500.00;    
                    } else if($orderData['weight'] == '2.5KG' || $orderData['weight'] == '2.5 KG') {
                        $total_amount = 1800.00;    
                    } else if($orderData['weight'] == '3KG' || $orderData['weight'] == '3 KG') {
                        $total_amount = 2200.00;    
                    } else if($orderData['weight'] == '3.5KG' || $orderData['weight'] == '3.5 KG') {
                        $total_amount = 2600.00;    
                    } else if($orderData['weight'] == '4KG' || $orderData['weight'] == '4 KG') {
                        $total_amount = 3000.00;    
                    } else if($orderData['weight'] == '4.5KG' || $orderData['weight'] == '4.5 KG') {
                        $total_amount = 3300.00;    
                    } else if($orderData['weight'] == '5KG' || $orderData['weight'] == '5 KG') {
                        $total_amount = 3600.00;    
                    }    
                }
                $order_no = 'CB_'.Auth::user()->id.'_'.strtotime(date('Y-m-d H:i:s'));
                $order = Orders::create([
                    'occassion' => strtoupper($orderData['occassion']),
                    'cake_type' => strtoupper($orderData['cake']),
                    'flavor' => $orderData['flavor'] == '' ? 'Not Available' : strtoupper($orderData['flavor']),
                    'weight' => strtoupper($orderData['weight']),
                    'order_date' => date('Y-m-d H:i:s'),
                    'delivery_date_time' => $orderData['delivery_date_time'],
                    'instruction' => $orderData['message_on_cake'] == "" ? "Not Available" : strtoupper($orderData['message_on_cake']),
                    'design_reference' => '',
                    'user_id' => Auth::user()->id,
                    'status' => 1,
                    'order_no' => $order_no
                ]);
                if($order) {
                    $payment = Payment::create([
                        'order_id' => $order_no,
                        'amount_to_be_paid' => $total_amount,
                        'amount_paid' => 0,
                        'payment_id' => 'Not Available',
                        'total_amount' => $total_amount
                    ]);    
                }
                $adminUsers = User::where('isAdmin', true)->get();
                $message = 'You got a new order from '.Auth::user()->name;
                foreach ($adminUsers as $admin) {
                    //$admin->notify(new NewOrderNotification($message,$order_no));
                    $admin->notify(new NewOrderNotification());
                }
                //$recipient = '+91'.Auth::user()->phone_no;
                //$message = "Your order for ".$cake_type_name." cake weighted ".$cake_weight_desc." has been placed. Delivery requested on ".date("l, F j, Y g:i A", strtotime($delivery_datetime))."\nThank you for ordering with us.\nTeam CakeBox";
                //$value = $this->twilioService->sendSms($recipient, $message);
                return response()->json([
                    'status' => 'success',
                    'order_no' => $order_no
                ]);
            }
        }

        return response()->json(['error' => 'Transcription failed'], 500);
    }
}
