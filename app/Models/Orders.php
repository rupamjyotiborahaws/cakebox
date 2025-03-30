<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = ['occassion','cake_type','flavor','weight','order_date','delivery_date_time','instruction','design_reference','user_id','status','order_no'];
}
