<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CanceledOrder extends Model
{
    use HasFactory;

    protected $table = 'cancelled_orders';

    protected $fillable = ['order_id','cancel_reason','refund_received'];
}
