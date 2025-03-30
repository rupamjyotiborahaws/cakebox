<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function index(Request $request) {
        return view('frontend.index');
    }

    public function products(Request $request) {
        return view('frontend.products');
    }
}
