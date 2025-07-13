<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\TwilioService;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewOrderNotification;
use App\Events\NewNotification;
use Log;
use Auth;
use Carbon\Carbon;
use App\Models\Type;
use App\Models\Flavor;
use App\Models\Weight;
use App\Models\Orders;
use App\Models\Payment;
use App\Models\User;

class OrderController extends Controller
{
    protected $twilioService;

    public function __construct(TwilioService $twilioService)
    {
        $this->twilioService = $twilioService;
    }
    
    public function new_order(Request $request) {
        return view('users.new_order');
    }

    public function place_order(Request $request) {
        $occassion  = $request->occassion;
        $cake_type  = $request->cake_type;
        $cake_flavor  = $request->cake_flavor;
        $cake_weight  = $request->cake_weight;
        $cake_instruction  = trim($request->cake_instruction);
        $delivery_datetime = Carbon::parse($request->cake_delivery_date . ' ' . $request->cake_delivery_time);
        $total_amount = 1.00;
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
            $order_no = 'CB_'.Auth::user()->id.'_'.strtotime(date('Y-m-d H:i:s'));
            $order = Orders::create([
                'occassion' => $occassion,
                'cake_type' => strtoupper($cake_type),
                'flavor' => $cake_flavor == null ? 'Not Available' : strtoupper($cake_flavor),
                'weight' => strtoupper($cake_weight),
                'order_date' => date('Y-m-d H:i:s'),
                'delivery_date_time' => $delivery_datetime,
                'instruction' => $cake_instruction == null || $cake_instruction == "" ? "Not Available" : strtoupper($cake_instruction),
                'design_reference' => $filePath,
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
            return redirect()->route('order')->with('success', 'Your order is placed! Track your order <a href="'.route('your_orders').'">here</a>');
        } else {
            return redirect()->back()->with(['error' => 'Uploaded file could not be saved. Please try again']);
        }
    }

    public function past_orders(Request $request) {
        return view('users.past_orders');
    }
}
