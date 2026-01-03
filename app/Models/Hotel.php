<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hotel_name',
        'hotel_address',
        'hotel_mobile',
        'hotel_telephone',
        'hotel_l_t_number',
        'hotel_gst_number',
        'hotel_email',
        'license_key',
        'license_expiry'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
