<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\TwilioService;
use Log;
use Auth;
use Carbon\Carbon;
use App\Models\Type;
use App\Models\Flavor;
use App\Models\Weight;
use App\Models\Orders;
use App\Models\Payment;

class OrderController extends Controller
{
    protected $twilioService;

    public function __construct(TwilioService $twilioService)
    {
        $this->twilioService = $twilioService;
    }
    
    public function new_order(Request $request) {
        $types = Type::all();
        $flavors = Flavor::all();
        $weights = Weight::all();
        return view('users.new_order', compact('types','flavors','weights'));
    }

    public function place_order(Request $request) {
        $occassion  = $request->occassion;
        $cake_type  = $request->cake_type;
        $cake_flavor  = $request->cake_flavor;
        $cake_weight  = $request->cake_weight;
        $cake_instruction  = $request->cake_instruction;
        $delivery_datetime = Carbon::parse($request->cake_delivery_date . ' ' . $request->cake_delivery_time);
        $total_amount = 800;
        $request->validate([
            'image' => 'required|file|mimes:jpg,jpeg,png,svg|max:2048',
        ]);
        $fileUrl = true;
        if($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploaded_reference_images', $fileName, 'public');
            $fileUrl = Storage::url($filePath);
        }
        if($fileUrl) {
            $order_no = 'CB_'.Auth::user()->id.'_'.strtotime(date('Y-m-d H:i:s'));
            $order = Orders::create([
                'occassion' => $occassion,
                'cake_type' => $cake_type,
                'flavor' => $cake_flavor,
                'weight' => $cake_weight,
                'order_date' => date('Y-m-d H:i:s'),
                'delivery_date_time' => $delivery_datetime,
                'instruction' => $cake_instruction || "Not Available",
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
            $cake_type_name = Type::where(['id'=>$cake_type])->get()[0]['cake_type'];
            $cake_weight_desc = Weight::where(['id'=>$cake_weight])->get()[0]['cake_weight'];
            $recipient = '+91'.Auth::user()->phone_no;
            $message = "Your order for ".$cake_type_name." cake weighted ".$cake_weight_desc." has been placed. Delivery requested on ".date("l, F j, Y g:i A", strtotime($delivery_datetime))."\nThank you for ordering with us.\nTeam CakeBox";
            $value = $this->twilioService->sendSms($recipient, $message);
            return redirect()->route('order')->with('success', 'Your order is placed! Track your order <a href="#">here</a>');
        } else {
            return redirect()->back()->withErrors(['error' => 'Uploaded file could not be saved. Please try again']);
        }
    }

    public function past_orders(Request $request) {
        return view('users.past_orders');
    }
}
