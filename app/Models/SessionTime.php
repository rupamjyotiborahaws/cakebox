<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionTime extends Model
{
    use HasFactory;

    protected $table = 'session_management';

    protected $fillable = [
        'session_time'
    ];

    public static function getSessionTime() {
        return SessionTime::where(['id'=>1])->first();
    }
}
