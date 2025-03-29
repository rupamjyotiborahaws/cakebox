<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Orders;
use App\Models\Type;
use App\Models\Weight;
use App\Models\Flavor;

class UserController extends Controller
{
    public function get_my_orders(Request $request) {
        $result = [];
        $orders = Orders::select('*')->orderBy('order_date','DESC')->get();
        foreach ($orders as $key => $value) {
            $cake_name = Type::where(['id'=>$value['cake_type']])->get()[0]['cake_type']; 
            $cake_weight = Weight::where(['id'=>$value['weight']])->get()[0]['cake_weight'];
            $cake_flavor = Flavor::where(['id'=>$value['flavor']])->get()[0]['flavor_name'];
            $result[$key]['order_date'] = $value['order_date'];
            $result[$key]['status'] = $value['status'];
            $result[$key]['cake_type'] = $cake_name;
            $result[$key]['cake_weight'] = $cake_weight;
            $result[$key]['cake_flavor'] = $cake_flavor;
        }
        return response()->json([
            'status' => 'success',
            'data' => $result
        ]);
    }
}
